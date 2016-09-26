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
	
	if(@$_POST['cmd'] == 'add_object')
	{
		if($_SESSION["obj_add"] != 'false')
		{
			$name = $_POST["name"];
			$imei = strtoupper($_POST["imei"]);
			
			if (checkObjectLimitSystem())
			{
				echo 'LIMIT';
				die;
			}
			
			if(checkObjectExistsUser($imei))
			{
				echo 'EXISTS';
				die;
			}
			
			if($_SESSION["obj_add"] == 'limited')
			{
				if(checkObjectLimitUser())
				{
					echo 'LIMIT';
					die;
				}
			}
			
			if ($_SESSION["obj_add"] == "trial")
			{
				$active_dt = date("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$gsValues['OBJ_TRIAL_PERIOD'].' days'));
			}
			else
			{
				$active_dt = $_SESSION["obj_dt"];
			}
			
			addObjectSystem($name, $imei, 'true', $active_dt, $_SESSION["manager_id"]);
			
			addObjectUser($user_id, $imei, 0, 0, 0);
			
			createObjectDataTable($imei);
			
			echo 'OK';
		}
		die;
	}
	
	if(@$_POST['cmd'] == 'duplicate_object')
	{
		if($_SESSION["obj_add"] != 'false')
		{
			$duplicate_imei = strtoupper($_POST["duplicate_imei"]);
			$name = $_POST["name"];
			$imei = strtoupper($_POST["imei"]);
			
			if (checkObjectLimitSystem())
			{
				echo 'LIMIT';
				die;
			}
			
			if(checkObjectExistsUser($imei))
			{
				echo 'EXISTS';
				die;
			}
			
			if($_SESSION["obj_add"] == 'limited')
			{
				if(checkObjectLimitUser())
				{
					echo 'LIMIT';
					die;
				}
			}
			
			if ($_SESSION["obj_add"] == "trial")
			{
				$active_date = date("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$gsValues['OBJ_TRIAL_PERIOD'].' days'));
			}
			else
			{
				$active_date = $_SESSION["obj_dt"];
			}
			
			duplicateObjectSystem($duplicate_imei, $imei, $active_date, $_SESSION["manager_id"], $name);
			
			$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$duplicate_imei."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			addObjectUser($user_id, $imei, $row['group_id'], $row['driver_id'], $row['trailer_id']);
			
			createObjectDataTable($imei);
			
			//write log
			writeLog('object_op', 'Add object: successful. IMEI: '.$imei);
			
			echo 'OK';
		}
		die;	
	}
	
	if(@$_POST['cmd'] == 'edit_object')
	{
		$group_id = $_POST["group_id"];
		$driver_id = $_POST["driver_id"];
		$trailer_id = $_POST["trailer_id"];
		$name = $_POST["name"];
		$imei = $_POST["imei"];
		$device = $_POST["device"];
		$sim_number = $_POST["sim_number"];
		$model = $_POST["model"];
		$vin = $_POST["vin"];
		$plate_number = $_POST["plate_number"];
		$icon = $_POST["icon"];
		$map_arrows = $_POST["map_arrows"];
		$map_icon = $_POST["map_icon"];
		$tail_color = $_POST["tail_color"];
		$tail_points = $_POST["tail_points"];
		$fcr = $_POST["fcr"];
		$time_adj = $_POST["time_adj"];
		$accuracy = $_POST["accuracy"];		
		
		$q = "UPDATE `gs_user_objects` SET 	`group_id`='".$group_id."',
							`driver_id`='".$driver_id."',
							`trailer_id`='".$trailer_id."'
							WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "UPDATE `gs_objects` SET 	`name`='".$name."',
						`icon`='".$icon."',
						`map_icon`='".$map_icon."',
						`map_arrows`='".$map_arrows."',
						`tail_color`='".$tail_color."',
						`tail_points`='".$tail_points."',
						`device`='".$device."',
						`sim_number`='".$sim_number."',
						`model`='".$model."',
						`vin`='".$vin."',
						`plate_number`='".$plate_number."',
						`fcr`='".$fcr."',
						`accuracy`='".$accuracy."'						
						WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		// set time adjustment
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if($time_adj != $row["time_adj"])
		{
			$q = "UPDATE `gs_objects` SET  `dt_server`='0000-00-00 00:00:00',
							`dt_tracker`='0000-00-00 00:00:00',
							`lat`='0',
							`lng`='0',
							`altitude`='0',
							`angle`='0',
							`speed`='0',
							`loc_valid`='0',
							`params`='',
							`time_adj`='".$time_adj."'
							WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		// set odometer and engine hours type
		$odometer_type = $_POST["odometer_type"];
		$engine_hours_type = $_POST["engine_hours_type"];
		
		$q = "UPDATE `gs_objects` SET `odometer_type`='".$odometer_type."', `engine_hours_type`='".$engine_hours_type."' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		// get odometer and engine_hours and check if saving is needed
		$odometer = $_POST["odometer"];
		
		if ($odometer != 'false')
		{
			// save in km
			$odometer = convDistanceUnits($odometer, $_SESSION["unit_distance"], 'km');
			$odometer = floor($odometer);
			
			$q = "UPDATE `gs_objects` SET `odometer`='".$odometer."' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		$engine_hours = $_POST["engine_hours"];
		
		if ($engine_hours != 'false')
		{
			$engine_hours = $engine_hours * 60 * 60;
			
			$q = "UPDATE `gs_objects` SET `engine_hours`='".$engine_hours."' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'clear_object_history')
	{
		if($_SESSION["obj_history_clear"] == 'true')
		{
			$imei = $_POST['imei'];
			
			$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if($row)
			{
				clearObjectHistory($imei);
			}
			
			echo 'OK';	
		}
		
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object')
	{
		$imei = $_POST["imei"];
		
		delObjectUser($user_id, $imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_values')
	{
		// check privileges		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT gs_objects.*, gs_user_objects.*
				FROM gs_objects
				INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
				WHERE gs_user_objects.user_id='".$user_id."'
				AND gs_objects.imei IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT gs_objects.*, gs_user_objects.*
				FROM gs_objects
				INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
				WHERE gs_user_objects.user_id='".$user_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row = mysqli_fetch_array($r))
		{			
			$imei = $row['imei'];
			
			// get object accuracy
			$accuracy = getObjectAccuracy($imei);
			
			// get object sensor list
			$sensors = getObjectSensors($imei);
			
			// get object service list
			$service = getObjectService($imei);
			
			// set default fcr if not set in DB
			$fcr = getObjectFCR($imei);
			
			// set default odometer and engine hours type if not set in DB
			if ($row['odometer_type'] == '')
			{
				$row['odometer_type'] = 'gps';
			}
			
			if ($row['engine_hours_type'] == '')
			{
				$row['engine_hours_type'] = 'acc';
			}
			
			// odometer and engine hours
			$row['odometer'] = convDistanceUnits($row['odometer'], 'km', $_SESSION["unit_distance"]);
			$row['odometer'] = floor($row['odometer']);
			
			$row['engine_hours'] = floor($row['engine_hours'] / 60 / 60);
			
			// map arrows
			$default = array(	'arrow_no_connection' => 'arrow_red',
						'arrow_stopped' => 'arrow_red',
						'arrow_moving' => 'arrow_green',
						'arrow_engine_idle' => 'off',
						'arrow_event_sos' => 'off'
						);
			
			if (($row['map_arrows'] == '') || (json_decode($row['map_arrows'],true) == null))
			{
				$map_arrows = $default;
			}
			else
			{
				$map_arrows = json_decode($row['map_arrows'],true);
				
				if (!isset($map_arrows["arrow_no_connection"])) { $map_arrows["arrow_no_connection"] = $default["arrow_no_connection"]; }
				if (!isset($map_arrows["arrow_stopped"])) { $map_arrows["arrow_stopped"] = $default["arrow_stopped"]; }
				if (!isset($map_arrows["arrow_moving"])) { $map_arrows["arrow_moving"] = $default["arrow_moving"]; }
				if (!isset($map_arrows["arrow_engine_idle"])) { $map_arrows["arrow_no_connection"] = $default["arrow_engine_idle"]; }
				if (!isset($map_arrows["arrow_event_sos"])) { $map_arrows["arrow_no_connection"] = $default["arrow_event_sos"]; }
			}		
			
			$result[$imei] = array( 'protocol' => $row['protocol'],
					        'group_id' => $row['group_id'],
						'driver_id' => $row['driver_id'],
						'trailer_id' => $row['trailer_id'],
						'name' => $row['name'],
						'icon' => $row['icon'],
						'map_arrows' => $map_arrows,
						'map_icon' => $row['map_icon'],
						'tail_color' => $row['tail_color'],
						'tail_points' => $row['tail_points'],
						'device' => $row['device'], 
						'sim_number' => $row['sim_number'],
						'model' => $row['model'],
						'vin' => $row['vin'],
						'plate_number' => $row['plate_number'],
						'odometer_type' => $row['odometer_type'],
						'engine_hours_type' => $row['engine_hours_type'],
						'odometer' => $row['odometer'],
						'engine_hours' => $row['engine_hours'],
						'fcr' => $fcr,
						'time_adj' => $row['time_adj'],
						'accuracy' => $accuracy,						
						'sensors' => $sensors,
						'service' => $service,
						'params' => getParamsArray($row['params']),
						'active' => $row['active'],
						'active_dt' => $row['active_dt']
						);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_info_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$imei = $_GET['imei'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		$speed = convSpeedUnits($row['speed'], 'km', $_SESSION["unit_distance"]).' '.$la["UNIT_SPEED"];
		$altitude = convAltitudeUnits($row['altitude'], 'km', $_SESSION["unit_distance"]).' '.$la["UNIT_HEIGHT"];	
		
		// paramsToArray will be removed later
		$row['params'] = paramsToArray($row['params']);
		
		$arr_params = array();
		
		$lat = sprintf('%0.6f', $row['lat']);
		$lng = sprintf('%0.6f', $row['lng']);
		
		foreach ($row['params'] as $key => $value)
		{
			if (substr($key, 0, 2) != 'h_')
			{
				array_push($arr_params, $key.'='.$value);
			}
		}
		
		$params = implode(', ', $arr_params);
		
		if ($params == '') { $params = '-';}
		
		$protocol = $row['protocol'];
		
		if ($protocol == '') { $protocol = '-';}
		
		$list_array = array(	$la['ALTITUDE'] => $altitude,
					$la['ANGLE'] => $row['angle'].' &deg;',
					$la['LATITUDE'] => $lat.' &deg;',
					$la['LONGITUDE'] => $lng.' &deg;',
					$la['PARAMETERS'] => $params,
					$la['PROTOCOL'] => $protocol,
					$la['SPEED'] => $speed,
					$la['TIME_POSITION'] => convUserTimezone($row['dt_tracker']),
					$la['TIME_SERVER'] => convUserTimezone($row['dt_server'])
					);
		
		ksort($list_array);
		
		$responce = new stdClass();
		
		$i=0;
		
		foreach ($list_array as $key => $value)
		{
			$responce->rows[$i]['cell']=array($key, $value);
			$i++;
		}

		$responce->page = 1;
		//$responce->total = $count;
		$responce->records = $count;
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = strtoupper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$imei = $row['imei'];
				
				if ($row['active'] == 'true')
				{
					$active = '<img src="theme/images/green_bullet.png" />';
					$active_dt = $row['active_dt'];
				}
				else
				{
					$active = '<img src="theme/images/red_cross.png" />';
					if ($gsValues['PAYMENT_TYPE'] == "")
					{
						$active_dt = '';
					}
					else if ($gsValues['PAYMENT_TYPE'] == "url")
					{
						$active_dt = '<a href="'.$gsValues['PAYMENT_URL'].'" target="_blank">'.$la['ACTIVATE'].'</a>';
					}
					elseif ($gsValues['PAYMENT_TYPE'] == "paypal")
					{
						$ppa = $gsValues['PAYMENT_PAYPAL_ACCOUNT'];
						$pn = $gsValues['PAYMENT_PAYPAL_NAME'];
						$pc = $gsValues['PAYMENT_PAYPAL_CUR'];
						$pa = $gsValues['PAYMENT_PAYPAL_AMOUNT'];
						$ue = $_SESSION["email"];
						$pl = 'https://www.paypal.com/cgi-bin/webscr?business='.$ppa.'&cmd=_xclick&currency_code='.$pc;
						$pl .='&amount='.$pa.'&item_name='.$pn.' '.$imei;
						$pl .='&custom='.$gsValues['PAYMENT_PAYPAL_CUSTOM'].','.$imei;
						$active_dt = '<a href="'.$pl.'" target="_blank">'.$la['ACTIVATE'].'</a>';
					}
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="settingsObjectEdit(\''.$imei.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
				$modify .= '<a href="#" onclick="settingsObjectDuplicate(\''.$imei.'\');" title="'.$la['DUPLICATE'].'"><img src="theme/images/duplicate.png" /></a>';
				$modify .= '<a href="#" onclick="settingsObjectClearHistory(\''.$imei.'\');" title="'.$la['CLEAR_HISTORY'].'"><img src="theme/images/clear.png" /></a>';
				$modify .= '<a href="#" onclick="settingsObjectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				
				// set row
				$responce->rows[$i]['cell']=array($row['name'],$imei,$active,$active_dt,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_custom_icon')
	{
		$file = $_POST['file'];
		$path = $gsValues['PATH_ROOT'];
		
		$icon_file = $path.'/'.$file;
		if(is_file($icon_file))
		{
			@unlink($icon_file);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_custom_icons')
	{
		$filter = $_SESSION['user_id'].'_';
		
		$path = $gsValues['PATH_ROOT'].'data/user/objects';
		$dh = opendir($path);
	    
		$result = array();
		    
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				if (0 === strpos($file, $filter))
				{
					$icon_file = $path.'/'.$file;
					if(is_file($icon_file))
					{
						@unlink($icon_file);
					}
				}
			}
		}
		
		closedir($dh);
		
		echo 'OK';
		die;
	}
?>