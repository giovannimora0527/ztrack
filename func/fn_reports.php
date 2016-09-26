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
	
	if(@$_POST['cmd'] == 'save_report')
	{
		$report_id = $_POST["report_id"];
		$name = $_POST["name"];
		$type = $_POST["type"];
		$format = $_POST["format"];
		$show_addresses = $_POST["show_addresses"];
		$zones_addresses = $_POST["zones_addresses"];
		$stop_duration = $_POST["stop_duration"];
		$speed_limit = $_POST["speed_limit"];
		$imei = $_POST["imei"];
		$zone_ids = $_POST["zone_ids"];
		$sensor_names = $_POST["sensor_names"];
		$data_items = $_POST["data_items"];
		$schedule_period = $_POST["schedule_period"];
		$schedule_email_address = $_POST["schedule_email_address"];
		
		if ($report_id == 'false')
		{
			$q = "INSERT INTO `gs_user_reports`(	`user_id`,
								`name`,
								`type`,
								`format`,
								`show_addresses`,
								`zones_addresses`,
								`stop_duration`,
								`speed_limit`,
								`imei`,
								`zone_ids`,
								`sensor_names`,
								`data_items`,
								`schedule_period`,
								`schedule_email_address`)
								VALUES
								('".$user_id."',
								'".$name."',
								'".$type."',
								'".$format."',
								'".$show_addresses."',
								'".$zones_addresses."',
								'".$stop_duration."',
								'".$speed_limit."',
								'".$imei."',
								'".$zone_ids."',
								'".$sensor_names."',
								'".$data_items."',
								'".$schedule_period."',
								'".$schedule_email_address."')";	
		}
		else
		{
			$q = "UPDATE `gs_user_reports` SET 	`name`='".$name."',
								`type`='".$type."',
								`format`='".$format."',
								`show_addresses`='".$show_addresses."',
								`zones_addresses`='".$zones_addresses."',
								`stop_duration`='".$stop_duration."',
								`speed_limit`='".$speed_limit."',
								`imei`='".$imei."',
								`zone_ids`='".$zone_ids."',
								`sensor_names`='".$sensor_names."',
								`data_items`='".$data_items."',
								`schedule_period`='".$schedule_period."',
								`schedule_email_address`='".$schedule_email_address."'
								WHERE `report_id`='".$report_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_report')
	{
		$report_id = $_POST["report_id"];
		
		$q = "DELETE FROM `gs_user_reports` WHERE `report_id`='".$report_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_report_list')
	{			
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_reports` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		$q = "SELECT * FROM `gs_user_reports` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) {
			$report_id = $row['report_id'];
			$name = $row['name'];
			
			if ($row['type'] == 'general')
			{
				$type = $la['GENERAL_INFO'];
			}
			else if ($row['type'] == 'general_merged')
			{
				$type = $la['GENERAL_INFO_MERGED'];
			}
			else if ($row['type'] == 'object_info')
			{
				$type = $la['OBJECT_INFO'];
			}
			else if ($row['type'] == 'drives_stops')
			{
				$type = $la['DRIVES_AND_STOPS'];
			}
			else if ($row['type'] == 'travel_sheet')
			{
				$type = $la['TRAVEL_SHEET'];
			}
			else if ($row['type'] == 'events')
			{
				$type = $la['EVENTS'];
			}
			else if ($row['type'] == 'overspeed')
			{
				$type = $la['OVERSPEEDS'];
			}
			else if ($row['type'] == 'underspeed')
			{
				$type = $la['UNDERSPEEDS'];
			}
			else if ($row['type'] == 'zone_in_out')
			{
				$type = $la['ZONE_IN_OUT'];
			}
			else if ($row['type'] == 'service')
			{
				$type = $la['SERVICE'];
			}
			else if ($row['type'] == 'rag')
			{
				$type = $la['DRIVER_BEHAVIOR_RAG'];
			}
			else if ($row['type'] == 'fuelfillings')
			{
				$type = $la['FUEL_FILLINGS'];
			}
			else if ($row['type'] == 'fuelthefts')
			{
				$type = $la['FUEL_THEFTS'];
			}
			else if ($row['type'] == 'logic_sensor_info')
			{
				$type = $la['LOGIC_SENSOR_INFO'];
			}
			else if ($row['type'] == 'acc_graph')
			{
				$type = $la['IGNITION_GRAPH'];
			}
			else if ($row['type'] == 'fuellevel_graph')
			{
				$type = $la['FUEL_LEVEL_GRAPH'];
			}
			else if ($row['type'] == 'temperature_graph')
			{
				$type = $la['TEMPERATURE_GRAPH'];
			}
			else if ($row['type'] == 'sensor_graph')
			{
				$type = $la['SENSOR_GRAPH'];
			}
			else
			{
				$type = '';
			}
			
			$format = strtoupper($row['format']);
			
			$imei = count(explode(",", $row['imei']));
			
			if ($row['zone_ids'] != '')
			{
				$zone_ids = count(explode(",", $row['zone_ids']));
			}
			else
			{
				$zone_ids = '0';
			}
			
			$schedule_period = '';
			
			if ($row['schedule_period'] == '')
			{
				$schedule_period = '<img src="theme/images/red_cross.png" />';
			}
			else
			{
				$schedule_period = '<img src="theme/images/green_bullet.png" />';
			}
			
			
			// set modify buttons
			$modify = '<a href="#" onclick="historyReportsDelete(\''.$report_id.'\');"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['id']=$report_id;
			$responce->rows[$i]['cell']=array($name,$type,$format,$imei,$zone_ids,$schedule_period,$modify);
			$i++;
		}
		
		$responce->page = 1;
		//$responce->total = $count;
		$responce->records = $count;
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_report_data')
	{
		$q = "SELECT * FROM `gs_user_reports` WHERE `user_id`='".$user_id."' ORDER BY `report_id` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{		
			$report_id = $row['report_id'];
			$result[$report_id] = array(	'name' => $row['name'],
							'type' => $row['type'],
							'format' => $row['format'],
							'show_addresses' => $row['show_addresses'],
							'zones_addresses' => $row['zones_addresses'],
							'stop_duration' => $row['stop_duration'],
							'speed_limit' => $row['speed_limit'],
							'imei' => $row['imei'],
							'zone_ids' => $row['zone_ids'],
							'sensor_names' => $row['sensor_names'],
							'data_items' => $row['data_items'],
							'schedule_period' => $row['schedule_period'],
							'schedule_email_address' => $row['schedule_email_address']
							);
		}
		echo json_encode($result);
		die;
	}
?>