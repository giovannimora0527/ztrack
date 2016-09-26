<?
        if (@$api_access != true) { die; }
        
        // split command and params
        //$cmd = explode(',', $cmd);
	$cmd = urldecode($cmd);
	$cmd = stripslashes($cmd);
	$cmd = str_getcsv($cmd, ",", '"');
        $command = @$cmd[0];
        $command = strtoupper($command);
	
	if ($command == 'USER_GET_OBJECTS')
        {
		// command validation
                if (count($cmd) < 1) { die; }
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$imei = $row['imei'];
			
			$q2 = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r2 = mysqli_query($ms, $q2);
			
			$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
			
			if ($row2)
			{
				$result[] = array('imei' => $row2['imei'],
						'protocol' => $row2['protocol'],
						'ip' => $row2['ip'],
						'port' => $row2['port'],
						'active' => $row2['active'],
						'active_dt' => $row2['active_dt'],
						'name' => $row2['name']);
			}
		}
		
		header('Content-type: application/json');
                echo json_encode($result); 
	}
	
	if ($command == 'OBJECT_GET_CMDS')
        {
		// command validation
                if (count($cmd) < 2) { die; }
		
		// command parameters
                $imei = strtoupper($cmd[1]);
		
		$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."' AND `status`='0'";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$result[] = array($row['cmd_id'], $row['cmd']);
			
			$q2 = "UPDATE `gs_object_cmd_exec` SET `status`='1' WHERE `cmd_id`='".$row["cmd_id"]."'";
			$r2 = mysqli_query($ms, $q2);
		}
		
		header('Content-type: application/json');
                echo json_encode($result); 
	}
	
	if ($command == 'OBJECT_CMD_GPRS')
        {
                // command validation
                if (count($cmd) < 5) { die; }
		
		// command parameters
                $imei = strtoupper($cmd[1]);
		$name = $cmd[2];
		$type = $cmd[3];
		$cmd = $cmd[4];
		
		$type = strtolower($type);
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if (!$row)
		{
			die();
		}
		
		sendObjectGPRSCommand($user_id, $imei, $name, $type, $cmd);	
	}
	
	if ($command == 'OBJECT_CMD_SMS')
        {
                // command validation
                if (count($cmd) < 4) { die; }
		
		// command parameters
                $imei = strtoupper($cmd[1]);
		$name = $cmd[2];
		$cmd = $cmd[3];
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if (!$row)
		{
			die();
		}
		
		sendObjectSMSCommand($user_id, $imei, $name, $cmd);
	}
        
        if ($command == 'OBJECT_GET_LOCATIONS')
        {
                // command validation
                if (count($cmd) < 2) { die; }
                
                // command parameters
                $imeis = strtoupper($cmd[1]);
                $imeis = explode(';', $imeis);
                $imeis = implode('","', $imeis);
                $imeis = '"'.$imeis.'"';
                
                $q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' AND `imei` IN (".$imeis.")";
                $r = mysqli_query($ms, $q);
                
                $result = array();
                
                while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
                        $imei = $row['imei'];
                        
                        $q2 = "SELECT * FROM `gs_objects` WHERE `imei` IN (".$imei.")";
			$r2 = mysqli_query($ms, $q2);
			$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
                        
                        $result[$imei] = array('dt_server' => $row2['dt_server'],
                                                'dt_tracker' => $row2['dt_tracker'],
                                                'lat' => $row2['lat'],
                                                'lng' => $row2['lng'],
                                                'altitude' => $row2['altitude'],
                                                'angle' => $row2['angle'],
                                                'speed' => $row2['speed'],
                                                'params' => json_decode($row2['params'],true),
                                                'loc_valid' => $row2['loc_valid']);        
		}
                
                header('Content-type: application/json');
                echo json_encode($result); 
        }
	
        if ($command == 'OBJECT_GET_MESSAGES')
        {
                // command validation
                if (count($cmd) < 4) { die; }
                
                // command parameters
                $imei = strtoupper($cmd[1]);
		$dtf = $cmd[2];
		$dtt = $cmd[3];
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if (!$row)
		{
			die();
		}
		
		$result = array();
		
		$q = "SELECT DISTINCT	dt_tracker,
					lat,
					lng,
					altitude,
					angle,
					speed,
					params
					FROM `gs_object_data_".$imei."` WHERE dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
					
		$r = mysqli_query($ms, $q);
		
		while($route_data=mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$result[] = array(	$route_data['dt_tracker'],
						$route_data['lat'],
						$route_data['lng'],
						$route_data['altitude'],
						$route_data['angle'],
						$route_data['speed'],
						paramsToArray($route_data['params']));
		}
		
		header('Content-type: application/json');
                echo json_encode($result); 
	}
	
	if ($command == 'GET_ADDRESS')
        {
                // command validation
                if (count($cmd) < 3) { die; }
                
                // command parameters
                $lat = $cmd[1];
		$lng = $cmd[2];
		
		$result = '';
		
		if (($lat <> '') && ($lng <> ''))
		{
			$result = geocoderGetAddress($lat, $lng);	
		}
		
		header('Content-Type: text/html; charset=utf-8');
                echo $result; 
	}
?>