<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	// check privileges
	if ($_SESSION["privileges"] == 'subuser')
	{
		$user_id = $_SESSION["manager_id"];
	}
	else
	{
		$user_id = $_SESSION["user_id"];
	}

	if(@$_POST['cmd'] == 'load_object_data')
	{		
		if (isset($_POST['imei']))
		{
			$imei = strtoupper(@$_POST['imei']); // get imei
			
			// check privileges
			if ($_SESSION["privileges"] == 'subuser')
			{			
				$q = "SELECT * FROM `gs_user_objects`
				WHERE `user_id`='".$user_id ."' AND `imei`='".$imei."' AND `imei` IN (".$_SESSION["privileges_imei"].")";
			}
			else
			{
				$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' AND `imei`='".$imei."'";
			}	
		}
		else
		{
			// check privileges
			if ($_SESSION["privileges"] == 'subuser')
			{
				$q = "SELECT * FROM `gs_user_objects`
				WHERE `user_id`='".$user_id ."' AND `imei` IN (".$_SESSION["privileges_imei"].") ORDER BY `imei` ASC";
			}
			else
			{
				$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' ORDER BY `imei` ASC";
			}	
		}
		
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$imei = $row['imei'];
			
			$q2 = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r2 = mysqli_query($ms, $q2);
			$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
			
			if ($row2['active'] == 'true')
			{
				$result[$imei] = array();
				$result[$imei]['v'] = true;
				$result[$imei]['f'] = false;
				$result[$imei]['s'] = false;
				$result[$imei]['evt'] = false;
				$result[$imei]['a'] = '';
				$result[$imei]['l'] = array();
				$result[$imei]['d'] = array();
				
				$dt_server = $row2['dt_server'];
				$dt_tracker = $row2['dt_tracker'];
				$lat = $row2['lat'];
				$lng = $row2['lng'];
				$altitude = $row2['altitude'];
				$angle = $row2['angle'];
				$speed = $row2['speed'];
				$params = json_decode($row2['params'],true);
				
				$speed = convSpeedUnits($speed, 'km', $_SESSION["unit_distance"]);
				$altitude = convAltitudeUnits($altitude, 'km', $_SESSION["unit_distance"]);
				
				// status
				$result[$imei]['st'] = false;
				
				$result[$imei]['ststr'] = '';
				
				$dt_last_stop = strtotime($row2['dt_last_stop']);
				$dt_last_idle = strtotime($row2['dt_last_idle']);
				$dt_last_move = strtotime($row2['dt_last_move']);
				
				if (($dt_last_stop > 0) || ($dt_last_move > 0))
				{
					// stopped and moving
					if ($dt_last_stop < $dt_last_move)
					{
						$result[$imei]['st'] = 'm';
						$result[$imei]['ststr'] = $la['MOVING'].' '.getTimeDetails(strtotime(gmdate("Y-m-d H:i:s")) - $dt_last_move);
					}
					else
					{
						$result[$imei]['st'] = 's';
						$result[$imei]['ststr'] = $la['STOPPED'].' '.getTimeDetails(strtotime(gmdate("Y-m-d H:i:s")) - $dt_last_stop);
					}
					
					// idle
					if (($dt_last_stop <= $dt_last_idle) && ($dt_last_move <= $dt_last_idle))
					{
						$result[$imei]['st'] = 'i';
						$result[$imei]['ststr'] = $la['ENGINE_IDLE'].' '.getTimeDetails(strtotime(gmdate("Y-m-d H:i:s")) - $dt_last_idle);
					}
				}
				
				// protocol
				$result[$imei]['p'] = $row2['protocol'];
				
				// connection/loc valid check
				$dt_now = gmdate("Y-m-d H:i:s");
				$dt_difference = strtotime($dt_now) - strtotime($dt_server);
				if($dt_difference < $gsValues['CONNECTION_TIMEOUT'] * 60)
				{
					$loc_valid = $row2['loc_valid'];
					
					if ($loc_valid == 1)
					{
						$conn = 2;
					}
					else
					{
						$conn = 1;
					}	
				}
				else
				{
					// offline status
					if (strtotime($dt_server) > 0)
					{
						$result[$imei]['st'] = 'off';
						$result[$imei]['ststr'] = $la['OFFLINE'].' '.getTimeDetails(strtotime(gmdate("Y-m-d H:i:s")) - strtotime($dt_server));
					}
					
					$conn = 0;
					$speed = 0;
				}
				
				$result[$imei]['cn'] = $conn;
				
				// location data
				if (($lat != 0) && ($lng != 0))
				{
					$result[$imei]['d'][] = array(	convUserTimezone($dt_server),
									convUserTimezone($dt_tracker),
									$lat,
									$lng,
									$altitude,
									$angle,
									$speed,
									$params);
				}
				
				// odometer and engine_hours				
				$row2['odometer'] = convDistanceUnits($row2['odometer'], 'km', $_SESSION["unit_distance"]);
				
				$result[$imei]['o'] = floor($row2['odometer']);
				$result[$imei]['eh'] = floor($row2['engine_hours'] / 60 / 60);
			}
		}
		
		mysqli_close($ms);
		
		ob_start();
		header('Content-type: application/json');
		echo json_encode($result);
		header("Connection: close");
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
		die;
	}
?>