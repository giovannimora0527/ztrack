<?
	include ('s_init.php');
	include ('s_events.php');
	include ('../func/fn_common.php');
	include ('../tools/gc_func.php');
	
	// describe loc array data
	//$loc['imei']
	//$loc['ip']
	//$loc['port']
	//$loc['dt_server']
	//$loc['dt_tracker']
	//$loc['lat']
	//$loc['lng']
	//$loc['altitude']
	//$loc['angle']
	//$loc['speed']
	//$loc['loc_valid']
	//$loc['params'] - stores array of params like acc, di, do, ai...
	//$loc['event']

	function insert_db_loc($loc)
	{
		global $ms;
		
		//pg_start();
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		$loc['lat'] = (double)sprintf('%0.6f', $loc['lat']);
		$loc['lng'] = (double)sprintf('%0.6f', $loc['lng']);
		$loc['altitude'] = floor($loc['altitude']);
		$loc['angle'] = floor($loc['angle']);
		$loc['speed'] = floor($loc['speed']);
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		// check for wrong speed
		if ($loc['speed'] > 300)
		{
			return false;
		}
		
		// check if object exists in system
		if (!checkObjectExistsSystem($loc['imei']))
		{
			insert_db_unused($loc);
			return false;
		}
		
		// adjust GPS time
		$loc['dt_tracker'] = adjustObjectTime($loc['imei'], $loc['dt_tracker']);
		
		// check if dt_tracker is one day too far - skip coordinate		      
		if (strtotime($loc['dt_tracker']) >= strtotime(gmdate("Y-m-d H:i:s").' +1 days'))
		{
			return false;
		}
		
		// check if dt_tracker is at least one hour too far - set 0 UTC time
		if (strtotime($loc['dt_tracker']) >= strtotime(gmdate("Y-m-d H:i:s").' +1 hours'))
		{
			$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
		}
		
		// get previous known location
		$loc_prev = get_gs_objects_data($loc['imei']);
		
		// merge params only if dt_tracker is newer
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			$loc['params'] = mergeParams($loc_prev['params'], $loc['params']);
		}
		
		insert_gs_objects($loc, $loc_prev);
		
		// check for duplicate locations
		if (loc_filter($loc, $loc_prev) == false)
		{
			insert_gs_object_data($loc);
			
			if ($loc['loc_valid'] == 1)
			{
				// check for local events if dt_tracker is newer, in other case only tracker events will be checked
				if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
				{
					check_events($loc, true);
				}
				else
				{
					check_events($loc, false);
				}
			}
			else
			{
				// add previous known location if there is no location from device
				if (($loc['lat'] == 0) && ($loc['lng'] == 0))
				{
					$loc['dt_tracker'] = $loc_prev['dt_tracker'];
					$loc['lat'] = $loc_prev['lat'];
					$loc['lng'] = $loc_prev['lng'];
					$loc['altitude'] = $loc_prev['altitude'];
					$loc['angle'] = $loc_prev['angle'];
					$loc['speed'] = $loc_prev['speed'];
				}
				
				// check if location exists
				if (($loc['lat'] != 0) && ($loc['lng'] != 0))
				{
					check_events($loc, false);
				}
			}
		}
		
		//pg_end();
	}
	
	function insert_db_noloc($loc)
	{
		global $ms;
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		// get existing tracker data and previous location
		$loc_prev = get_gs_objects_data($loc['imei']);
		
		if ($loc_prev != false)
		{
			// add previous known location
			$loc['dt_tracker'] = $loc_prev['dt_tracker'];
			$loc['lat'] = $loc_prev['lat'];
			$loc['lng'] = $loc_prev['lng'];
			$loc['altitude'] = $loc_prev['altitude'];
			$loc['angle'] = $loc_prev['angle'];
			$loc['speed'] = $loc_prev['speed'];
			$loc['loc_valid'] = $loc_prev['loc_valid'];
			$loc['params'] = mergeParams($loc_prev['params'], $loc['params']);
			
			$q = "UPDATE gs_objects SET 	`protocol`='".$loc['protocol']."',
							`ip`='".$loc['ip']."',
							`port`='".$loc['port']."',
							`dt_server`='".$loc['dt_server']."',
							`params`='".json_encode($loc['params'])."'
							WHERE imei='".$loc['imei']."'";
							
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
			
			// check if location exists
			if (($loc['lat'] != 0) && ($loc['lng'] != 0))
			{
				insert_db_status($loc, $loc_prev);
				
				check_events($loc, false);
			}
		}
	}
	
	function insert_db_unused($loc)
	{
		global $ms;
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		$q = "INSERT INTO `gs_objects_unused` (imei, protocol, ip, port, dt_server, count)
						VALUES ('".$loc['imei']."', '".$loc['protocol']."', '".$loc['ip']."', '".$loc['port']."', '".$loc['dt_server']."', '1')
						ON DUPLICATE KEY UPDATE protocol = '".$loc['protocol']."', ip = '".$loc['ip']."', port = '".$loc['port']."', dt_server = '".$loc['dt_server']."', count = count + 1";
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
	}
		
	function insert_db_rfid_swipe($swipe)
	{
		global $ms;
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		$loc['lat'] = (double)sprintf('%0.6f', $loc['lat']);
		$loc['lng'] = (double)sprintf('%0.6f', $loc['lng']);
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		// one minute interval between card swipes
		$q = "SELECT * FROM gs_rfid_swipe_data WHERE 	`imei`='".$swipe['imei']."' AND
								`rfid`='".$swipe['rfid']."' AND
								`dt_swipe` > DATE_SUB('".$swipe['dt_swipe']."', INTERVAL 1 MINUTE)";
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		$row = mysqli_fetch_array($r);
		
		if (!$row)
		{
			$q = 'INSERT INTO gs_rfid_swipe_data (	dt_server,
								dt_swipe,
								imei,
								lat,
								lng,
								rfid
								) VALUES (
								"'.$swipe['dt_server'].'",
								"'.$swipe['dt_swipe'].'",
								"'.$swipe['imei'].'",
								"'.$swipe['lat'].'",
								"'.$swipe['lng'].'",
								"'.$swipe['rfid'].'")';
							
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));	
		}
	}
	
	function insert_gs_objects($loc, $loc_prev)
	{
		global $ms;
		
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			if ($loc['loc_valid'] == 1)
			{
				// calculate angle
				$loc['angle'] = getAngle($loc_prev['lat'], $loc_prev['lng'], $loc['lat'], $loc['lng']);
				
				$q = "UPDATE gs_objects SET	`protocol`='".$loc['protocol']."',
								`ip`='".$loc['ip']."',
								`port`='".$loc['port']."',
								`dt_server`='".$loc['dt_server']."',
								`dt_tracker`='".$loc['dt_tracker']."',
								`lat`='".$loc['lat']."',
								`lng`='".$loc['lng']."',
								`altitude`='".$loc['altitude']."',
								`angle`='".$loc['angle']."',
								`speed`='".$loc['speed']."',
								`loc_valid`='".$loc['loc_valid']."',
								`params`='".json_encode($loc['params'])."'
								WHERE imei='".$loc['imei']."'";
								
				$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
			}
			else
			{
				$q = "UPDATE gs_objects SET 	`protocol`='".$loc['protocol']."',
								`ip`='".$loc['ip']."',
								`port`='".$loc['port']."',
								`dt_server`='".$loc['dt_server']."',
								`loc_valid`='0',
								`params`='".json_encode($loc['params'])."'
								WHERE imei='".$loc['imei']."'";
								
				$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
			}
		}
		else
		{
			$q = "UPDATE gs_objects SET 	`protocol`='".$loc['protocol']."',
							`ip`='".$loc['ip']."',
							`port`='".$loc['port']."',
							`dt_server`='".$loc['dt_server']."'
							WHERE imei='".$loc['imei']."'";
							
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		}
		
		insert_db_status($loc, $loc_prev);
		
		insert_db_odo_engh($loc, $loc_prev);
		
		insert_db_ri($loc, $loc_prev);
	}
	
	function insert_gs_object_data($loc)
	{
		global $ms;
		
		if (($loc['lat'] != 0) && ($loc['lat'] != 0))
		{
			$q = "INSERT INTO gs_object_data_".$loc['imei']."(	dt_server,
										dt_tracker,
										lat,
										lng,
										altitude,
										angle,
										speed,
										params
										) VALUES (
										'".$loc['dt_server']."',
										'".$loc['dt_tracker']."',
										'".$loc['lat']."',
										'".$loc['lng']."',
										'".$loc['altitude']."',
										'".$loc['angle']."',
										'".$loc['speed']."',
										'".json_encode($loc['params'])."')";
										
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		}
	}
	
	function insert_db_status($loc, $loc_prev)
	{
		global $ms;
		
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			$imei = $loc['imei'];
			$params = $loc['params'];
			
			$dt_last_stop = strtotime($loc_prev['dt_last_stop']);
			$dt_last_idle = strtotime($loc_prev['dt_last_idle']);
			$dt_last_move = strtotime($loc_prev['dt_last_move']);
			
			if ($loc['loc_valid'] == 1)
			{
				// status stop
				if ((($dt_last_stop <= 0) || ($loc_prev['speed'] > 0)) && ($loc['speed'] == 0))
				{
					$q = "UPDATE gs_objects SET `dt_last_stop`='".$loc['dt_server']."' WHERE imei='".$imei."'";			
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					
					$dt_last_stop = strtotime($loc['dt_server']);
				}
				
				// status moving
				if (($loc_prev['speed'] == 0) && ($loc['speed'] > 0))
				{
					$q = "UPDATE gs_objects SET `dt_last_move`='".$loc['dt_server']."' WHERE imei='".$imei."'";			
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					
					$dt_last_move = strtotime($loc['dt_server']);
				}	
			}
			
			// status idle
			if ($dt_last_stop >= $dt_last_move)
			{
				$sensor = getSensorFromType($imei, 'acc');
				$acc = $sensor[0]['param'];
				
				if (isset($params[$acc]))
				{
					if (($params[$acc] == 1) && ($dt_last_idle <= 0))
					{
						$q = "UPDATE gs_objects SET `dt_last_idle`='".$loc['dt_server']."' WHERE imei='".$imei."'";
						$r = mysqli_query($ms, $q) or die(mysqli_error($ms));	
					}
					else if (($params[$acc] == 0) && ($dt_last_idle > 0))
					{
						$q = "UPDATE gs_objects SET `dt_last_idle`='0000-00-00 00:00:00' WHERE imei='".$imei."'";
						$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					}
				}
			}
			else
			{
				if ($dt_last_idle > 0)
				{
					$q = "UPDATE gs_objects SET `dt_last_idle`='0000-00-00 00:00:00' WHERE imei='".$imei."'";
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
				}
			}
		}
	}
	
	function insert_db_odo_engh($loc, $loc_prev)
	{
		global $ms;
		
		$imei = $loc['imei'];
		$params = $loc['params'];
		$params_prev = $loc_prev['params'];
		
		// odo gps
		if ($loc_prev['odometer_type'] == 'gps')
		{
			if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
			{
				if (($loc_prev['lat'] != 0) && ($loc_prev['lng'] != 0) && ($loc['speed'] > 3))
				{
					$odometer = getLengthBetweenCoordinates($loc_prev['lat'], $loc_prev['lng'], $loc['lat'], $loc['lng']);
					
					$q = 'UPDATE gs_objects SET `odometer` = odometer + '.$odometer.' WHERE imei="'.$imei.'"';
					$r = mysqli_query($ms, $q);
				}	
			}
		}
		
		// odo sen
		if ($loc_prev['odometer_type'] == 'sen')
		{
			$sensor = getSensorFromType($imei, 'odo');
			
			if ($sensor != false)
			{
				$sensor_ = $sensor[0];
				
				$odo = getSensorValue($params, $sensor_);
				
				$result_type = $sensor_['result_type'];
				
				if ($result_type == 'abs')
				{
					if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
					{	
						$q = 'UPDATE gs_objects SET `odometer` = '.$odo['value'].' WHERE imei="'.$imei.'"';
						$r = mysqli_query($ms, $q);
					}
				}
				
				if ($result_type == 'rel')
				{
					$q = 'UPDATE gs_objects SET `odometer` = odometer + '.$odo['value'].' WHERE imei="'.$imei.'"';
					$r = mysqli_query($ms, $q);
				}
			}
		}
		
		// engh acc
		if ($loc_prev['engine_hours_type'] == 'acc')
		{
			if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
			{
				if ((strtotime($loc['dt_tracker']) > 0) && (strtotime($loc_prev['dt_tracker']) > 0))
				{
					$engine_hours = 0;
					
					// get ACC sensor
					$sensor = getSensorFromType($imei, 'acc');
					$acc = $sensor[0]['param'];
					
					// calculate engine hours from ACC
					$dt_tracker = $loc['dt_tracker'];
					$dt_tracker_prev = $loc_prev['dt_tracker'];
					
					if (isset($params_prev[$acc]) && isset($params[$acc]))
					{
						if (($params_prev[$acc] == '1') && ($params[$acc] == '1'))
						{
							$engine_hours = strtotime($dt_tracker)-strtotime($dt_tracker_prev);
							
							$q = 'UPDATE gs_objects SET `engine_hours` = engine_hours + '.$engine_hours.' WHERE imei="'.$imei.'"';
							$r = mysqli_query($ms, $q);
						}
					}	
				}
			}
		}
		
		// eng sen
		if ($loc_prev['engine_hours_type'] == 'sen')
		{
			$sensor = getSensorFromType($imei, 'engh');
			
			if ($sensor != false)
			{
				$sensor_ = $sensor[0];
				
				$engh = getSensorValue($params, $sensor_);
								
				$result_type = $sensor_['result_type'];
				
				if ($result_type == 'abs')
				{
					if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
					{	
						$q = 'UPDATE gs_objects SET `engine_hours` = '.$engh['value'].' WHERE imei="'.$imei.'"';
						$r = mysqli_query($ms, $q);
					}
				}
				
				if ($result_type == 'rel')
				{
					$q = 'UPDATE gs_objects SET `engine_hours` = engine_hours + '.$engh['value'].' WHERE imei="'.$imei.'"';
					$r = mysqli_query($ms, $q);
				}
			}
		}
	}
	
	function insert_db_ri($loc, $loc_prev)
	{
		global $ms;
		
		// logbook
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			$imei = $loc['imei'];
			$params = $loc['params'];
			$params_prev = $loc_prev['params'];
			
			$group_array = array('da', 'pa', 'ta');
			
			for ($i=0; $i<count($group_array); ++$i)
			{
				$group = $group_array[$i];
				
				$sensor = getSensorFromType($imei, $group);
				
				if ($sensor != false)
				{
					$sensor_ = $sensor[0];
					
					$sensor_data = getSensorValue($params, $sensor_);
					$assign_id = $sensor_data['value'];
					
					$sensor_data_prev = getSensorValue($params_prev, $sensor_);
					$assign_id_prev = $sensor_data_prev['value'];
					
					if ((string)$assign_id != (string)$assign_id_prev)
					{
						insert_db_ri_data($loc['dt_server'], $loc['dt_tracker'], $imei, $group, $assign_id, $loc['lat'], $loc['lng']);
					}
				}
				
			}
		}
	}
	
	function insert_db_ri_data($dt_server, $dt_tracker, $imei, $group, $assign_id, $lat, $lng)
	{
		global $ms;
		
		$address = geocoderGetAddress($lat, $lng);
		
		$q = 'INSERT INTO gs_rilogbook_data  (	`dt_server`,
							`dt_tracker`,
							`imei`,
							`group`,
							`assign_id`,
							`lat`,
							`lng`,
							`address`
							) VALUES (
							"'.$dt_server.'",
							"'.$dt_tracker.'",
							"'.$imei.'",
							"'.$group.'",
							"'.$assign_id.'",
							"'.$lat.'",
							"'.$lng.'",
							"'.$address.'")';
							
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
	}
	
	function get_gs_objects_data($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM gs_objects WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			$row['params'] = json_decode($row['params'],true);
			
			return $row;
		}
		else
		{
			return false;
		}
	}
	
	function loc_filter($loc, $loc_prev)
	{
		global $ms, $gsValues;
		
		if ($gsValues['LOCATION_FILTER'] == false)
		{
			return false;
		}
		
		if (isset($loc['lat']) && isset($loc['lng']) && isset($loc['params']))
		{
			if (($loc['event'] == '') && ($loc_prev['params'] == $loc['params']))
			{
				$dt_difference = abs(strtotime($loc['dt_server']) - strtotime($loc_prev['dt_server']));
				
				if($dt_difference < 120)
				{
					// skip same location
					if (($loc_prev['lat'] == $loc['lat']) && ($loc_prev['lng'] == $loc['lng']) && ($loc_prev['speed'] == $loc['speed']))
					{
						return true;
					}
					
					// skip drift
					$distance = getLengthBetweenCoordinates($loc_prev['lat'], $loc_prev['lng'], $loc['lat'], $loc['lng']);
					if (($dt_difference < 30) && ($distance < 0.01) && ($loc['speed'] < 3) && ($loc_prev['speed'] == 0))
					{
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
///////////////////////////
//$tstart;
//function pg_end()
//{
//	global $tstart;
//	//Get current time as we did at start
//	$mtime = microtime();
//	$mtime = explode(" ",$mtime);
//	$mtime = $mtime[1] + $mtime[0];
//	//Store end time in a variable
//	$tend = $mtime;
//	//Calculate the difference
//	$totaltime = ($tend - $tstart);
//	//Output result
//	echo ($totaltime.'s');
//}
//
//function pg_start()
//{
//	global $tstart;
//	//Get current time
//	$mtime = microtime();
//	//Split seconds and microseconds
//	$mtime = explode(" ",$mtime);
//	//Create one value for start time
//	$mtime = $mtime[1] + $mtime[0];
//	//Write start time into a variable
//	$tstart = $mtime; 
//}
?>