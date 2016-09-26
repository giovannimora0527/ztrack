<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'load_event_values')
	{
		$user_id = $_SESSION["user_id"];
		
		$q = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$user_id."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row = mysqli_fetch_array($r))
		{
			$event_id = $row['event_id'];
			
			$day_time = json_decode($row['day_time'], true);
			
			$result[$event_id] = array(	'type' => $row['type'],
							'name' => $row['name'],
							'active' => $row['active'],
							'duration_from_last_event' => $row['duration_from_last_event'],
							'duration_from_last_event_minutes' => $row['duration_from_last_event_minutes'],
							'week_days' => $row['week_days'],
							'day_time' => $day_time,
							'imei' => $row['imei'],
							'checked_value' => $row['checked_value'],
							'route_trigger' => $row['route_trigger'],
							'zone_trigger' => $row['zone_trigger'],
							'routes' => $row['routes'],
							'zones' => $row['zones'],
							'notify_system' => $row['notify_system'],
							'notify_email' => $row['notify_email'],
							'notify_email_address' => $row['notify_email_address'],
							'notify_sms' => $row['notify_sms'],
							'notify_sms_number' => $row['notify_sms_number'],							
							'email_template_id' => $row['email_template_id'],
							'sms_template_id' => $row['sms_template_id'],
							'cmd_send' => $row['cmd_send'],
							'cmd_gateway' => $row['cmd_gateway'],
							'cmd_type' => $row['cmd_type'],
							'cmd_string' => $row['cmd_string']
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_event_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$user_id = $_SESSION["user_id"];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$event_id = $row['event_id'];
			$name = $row['name'];
			
			if ($row['active'] == 'true')
			{
				$active = '<img src="theme/images/green_bullet.png" />';
			}
			else
			{
				$active = '<img src="theme/images/red_cross.png" />';
			}
			
			$notify_system = explode(",", $row['notify_system']);
			
			if (@$notify_system[0] == 'true')
			{
				$notify_system = '<img src="theme/images/green_bullet.png" />';
			}
			else
			{
				$notify_system = '<img src="theme/images/red_cross.png" />';
			}
			
			if ($row['notify_email'] == 'true')
			{
				$notify_email = '<img src="theme/images/green_bullet.png" />';
			}
			else
			{
				$notify_email = '<img src="theme/images/red_cross.png" />';
			}
			
			if ($row['notify_sms'] == 'true')
			{
				$notify_sms = '<img src="theme/images/green_bullet.png" />';
			}
			else
			{
				$notify_sms = '<img src="theme/images/red_cross.png" />';
			}
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsEventProperties(\''.$event_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '</a><a href="#" onclick="settingsEventDelete(\''.$event_id.'\');"  title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['cell']=array($name,$active,$notify_system,$notify_email,$notify_sms,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_event')
	{
		$event_id = $_POST["event_id"];
		$user_id = $_SESSION["user_id"];
		
		$q = "DELETE FROM `gs_user_events` WHERE `event_id`='".$event_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `event_id`='".$event_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_event')
	{
		$event_id = $_POST["event_id"];
		$user_id = $_SESSION["user_id"];
		$type = $_POST["type"];
		$name = $_POST["name"];
		$active = $_POST["active"];
		$duration_from_last_event = $_POST["duration_from_last_event"];
		$duration_from_last_event_minutes = $_POST["duration_from_last_event_minutes"];
		$week_days = $_POST["week_days"];
		$day_time = $_POST["day_time"];
		$imei = $_POST["imei"];
		$checked_value = $_POST["checked_value"];
		$route_trigger = $_POST["route_trigger"];
		$zone_trigger = $_POST["zone_trigger"];
		$routes = $_POST["routes"];
		$zones = $_POST["zones"];
		$notify_system = $_POST["notify_system"];
		$notify_email = $_POST["notify_email"];
		$notify_email_address = $_POST["notify_email_address"];
		$notify_sms = $_POST["notify_sms"];
		$notify_sms_number = $_POST["notify_sms_number"];
		$email_template_id = $_POST["email_template_id"];
		$sms_template_id = $_POST["sms_template_id"];
		$cmd_send = $_POST["cmd_send"];
		$cmd_gateway = $_POST["cmd_gateway"];
		$cmd_type = $_POST["cmd_type"];
		$cmd_string = $_POST["cmd_string"];
		
		if ($event_id == 'false')
		{
			$q = "INSERT INTO `gs_user_events` (`user_id`,
								`type`,
								`name`,
								`active`,
								`duration_from_last_event`,
								`duration_from_last_event_minutes`,
								`week_days`,
								`day_time`,
								`imei`,
								`checked_value`,
								`route_trigger`,
								`zone_trigger`,
								`routes`,
								`zones`,
								`notify_system`,
								`notify_email`,
								`notify_email_address`,
								`notify_sms`,
								`notify_sms_number`,
								`email_template_id`,
								`sms_template_id`,
								`cmd_send`,
								`cmd_gateway`,
								`cmd_type`,
								`cmd_string`
								) VALUES (
								'".$user_id."',
								'".$type."',
								'".$name."',
								'".$active."',
								'".$duration_from_last_event."',
								'".$duration_from_last_event_minutes."',
								'".$week_days."',
								'".$day_time."',
								'".$imei."',
								'".$checked_value."',
								'".$route_trigger."',
								'".$zone_trigger."',
								'".$routes."',
								'".$zones."',
								'".$notify_system."',
								'".$notify_email."',
								'".$notify_email_address."',
								'".$notify_sms."',												
								'".$notify_sms_number."',
								'".$email_template_id."',
								'".$sms_template_id."',
								'".$cmd_send."',
								'".$cmd_gateway."',
								'".$cmd_type."',
								'".$cmd_string."')";
		}
		else
		{
			$q = "UPDATE `gs_user_events` SET 	`type`='".$type."', 
								`name`='".$name."',
								`active`='".$active."',
								`duration_from_last_event`='".$duration_from_last_event."',
								`duration_from_last_event_minutes`='".$duration_from_last_event_minutes."',
								`week_days`='".$week_days."',
								`day_time`='".$day_time."',
								`imei`='".$imei."',
								`checked_value`='".$checked_value."',
								`route_trigger`='".$route_trigger."',
								`zone_trigger`='".$zone_trigger."',
								`routes`='".$routes."',
								`zones`='".$zones."',
								`notify_system`='".$notify_system."',
								`notify_email`='".$notify_email."',
								`notify_email_address`='".$notify_email_address."',
								`notify_sms`='".$notify_sms."',
								`notify_sms_number`='".$notify_sms_number."',
								`email_template_id`='".$email_template_id."',
								`sms_template_id`='".$sms_template_id."',
								`cmd_send`='".$cmd_send."',
								`cmd_gateway`='".$cmd_gateway."',
								`cmd_type`='".$cmd_type."',
								`cmd_string`='".$cmd_string."'
								WHERE `event_id`='".$event_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
	}
?>