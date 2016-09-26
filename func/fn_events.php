<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	// check privileges
	if ($_SESSION["privileges"] == 'subuser')
	{
		$user_id = $_SESSION["manager_id"];
	}
	else
	{
		$user_id = $_SESSION["user_id"];
	}
	
	if(@$_POST['cmd'] == 'load_last_event')
	{
		$result = array();
		
		// check privileges		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."' AND `imei` IN (".$_SESSION["privileges_imei"].")
			ORDER BY event_id DESC LIMIT 1";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."'
			ORDER BY event_id DESC LIMIT 1";
		}
		
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if($row)
		{		
			$imei = $row['imei'];
			
			if ($row['obj_name'] == "")
			{
				$row['obj_name'] = getObjectName($imei);
			}
			
			$row['dt_tracker'] = convUserTimezone($row['dt_tracker']);
			
			$result = $row;
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
		die;	
	}
	
	if(@$_POST['cmd'] == 'delete_all_events')
	{
		$q = "DELETE FROM `gs_user_events_data` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}

	if(@$_POST['cmd'] == 'load_event_data')
	{
		$event_id = $_POST['event_id'];
		
		// check privileges		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."' AND `event_id`='".$event_id."' AND `imei` IN (".$_SESSION["privileges_imei"].") LIMIT 1";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."' AND `event_id`='".$event_id."' LIMIT 1";
		}
		
		$r = mysqli_query($ms, $q);
		$event_data = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if ($event_data['obj_name'] == "")
		{
			$event_data['obj_name'] = getObjectName($event_data['imei']);
		}
		
		$event_data['speed'] = convSpeedUnits($event_data['speed'], 'km', $_SESSION["unit_distance"]);
		$event_data['altitude'] = convAltitudeUnits($event_data['altitude'], 'km', $_SESSION["unit_distance"]);
		
		// paramsToArray will be removed later
		$params = paramsToArray($event_data['params']);
		
		$result = array('obj_name' => $event_data['obj_name'],
				'imei' => $event_data['imei'],
				'event_desc' => $event_data['event_desc'],
				'dt_server' => convUserTimezone($event_data['dt_server']),
				'dt_tracker' => convUserTimezone($event_data['dt_tracker']),
				'lat' => $event_data['lat'],
				'lng' => $event_data['lng'],
				'altitude' => $event_data['altitude'],
				'angle' => $event_data['angle'],
				'speed' => $event_data['speed'],
				'params' => $params);
		header('Content-type: application/json');
		echo json_encode($result);
		die;	
	}

	if(@$_GET['cmd'] == 'load_event_list')
	{		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = strtoupper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx =1;
			
		// check privileges		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."'
			AND (UPPER(`event_desc`) LIKE '%$search%' OR UPPER(`obj_name`) LIKE '%$search%')
			AND `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."'
			AND (UPPER(`event_desc`) LIKE '%$search%' OR UPPER(`obj_name`) LIKE '%$search%')";
		}
		
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if( $count >0 )
		{
			$total_pages = ceil($count/$limit);
		}
		else
		{
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		// check privileges		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."'
			AND (UPPER(`event_desc`) LIKE '%$search%' OR UPPER(`obj_name`) LIKE '%$search%')
			AND `imei` IN (".$_SESSION["privileges_imei"].")
			ORDER BY $sidx $sord LIMIT $start, $limit";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_events_data`
			WHERE `user_id`='".$user_id."'
			AND (UPPER(`event_desc`) LIKE '%$search%' OR UPPER(`obj_name`) LIKE '%$search%')
			ORDER BY $sidx $sord LIMIT $start, $limit";
		}
		
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($count > 0)
		{
			$i=0;
			while($event_data = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				$imei = $event_data['imei'];
				
				if (checkObjectActive($imei) == true)
				{
					$dt_tracker = convUserTimezone($event_data['dt_tracker']);
					
					$q2 = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$event_data['imei']."'";
					$r2 = mysqli_query($ms, $q2);
					$user_data = mysqli_fetch_array($r2,MYSQL_ASSOC);
					
					if ($event_data['obj_name'] == "")
					{
						$event_data['obj_name'] = getObjectName($event_data['imei']);
					}
					
					$responce->rows[$i]['id'] = $event_data['event_id'];
					$responce->rows[$i]['cell']=array($dt_tracker, $event_data['obj_name'], $event_data['event_desc']);
					$i++;	
				}	
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
?>