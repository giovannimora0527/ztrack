<?
	set_time_limit(0);
	
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('fn_cleanup.php');
	include ('../tools/sms.php');
	checkUserSession();
	checkUserCPanelPrivileges();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'server_cleanup_users')
	{
		$days = $_POST['days'];
		$result = serverCleanupUsers($days);
		
		echo $result;
		
		die;
	}
	
	if(@$_POST['cmd'] == 'server_cleanup_objects_not_activated')
	{
		$days = $_POST['days'];
		$result = serverCleanupObjectsNotActivated($days);
		
		echo $result;
		
		die;
	}
	
	if(@$_POST['cmd'] == 'server_cleanup_objects_not_used')
	{
		$result = serverCleanupObjectsNotUsed();
		
		echo $result;
		
		die;
	}
	
	if(@$_POST['cmd'] == 'server_cleanup_db_junk')
	{
		$result = serverCleanupDbJunk();
		
		echo $result;
		
		die;
	}
	
	if(@$_POST['cmd'] == 'stats')
	{
		// check if admin or manager
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			$manager_id = @$_POST['manager_id'];
			
			// switch admin/manager
			if ($manager_id != 0)
			{
				$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%') AND `manager_id`='".$manager_id."'";
				$q_objects = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
			}
			else
			{
				$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%')";
				$q_objects = "SELECT * FROM `gs_objects`";
			}
		}
		else
		{
			$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%') AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
			$q_objects = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
		}
	    
		$r = mysqli_query($ms, $q_users);
		$total_users = mysqli_num_rows($r);
		
		$r = mysqli_query($ms, $q_objects);
		$total_objects = mysqli_num_rows($r);
		
		$total_objects_online = 0;
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{            
			$last_connection = $row['dt_server'];
			$dt_now = gmdate("Y-m-d H:i:s");
			
			$dt_difference = strtotime($dt_now) - strtotime($last_connection);
			if($dt_difference < $gsValues['CONNECTION_TIMEOUT'] * 60)
			{
				$total_objects_online += 1;
			}
		}
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$total_objects .= '/'.$_SESSION["manager_obj_num"];
		}
		
		// total unused objects
		$total_unused_objects = 0;
		
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			$q_unused_objects = "SELECT * FROM `gs_objects_unused`";
			$r = mysqli_query($ms, $q_unused_objects);
			$total_unused_objects = mysqli_num_rows($r);
		}
		
		$sms_gateway_total_in_queue = getSMSAPPTotalInQueue($gsValues['SMS_GATEWAY_IDENTIFIER']);
		
		$result = array('total_users' => $total_users,
				'total_objects' => $total_objects,
				'total_objects_online' => $total_objects_online,
				'total_unused_objects' => $total_unused_objects,
				'sms_gateway_total_in_queue' => $sms_gateway_total_in_queue);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_custom_map_list')
	{
		$result = array();
		
		$q = "SELECT * FROM `gs_maps` ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		while($row=mysqli_fetch_array($r))
		{
			$map_id = $row['map_id'];
			$name = $row['name'];
			$active = $row['active'];
			$type = strtoupper($row['type']);
			$url = $row['url'];
			$layers = $row['layers'];
			
			$result[] = array('map_id' => $map_id, 'name' => $name, 'active' => $active, 'type' => $type, 'url' => $url, 'layers' => $layers);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_custom_map')
	{
		$result = array();
		
		$map_id = $_POST['map_id'];
		
		$q = "SELECT * FROM `gs_maps` WHERE `map_id`='".$map_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('name' => $row['name'], 'active' => $row['active'], 'type' => $row['type'], 'url' => $row['url'], 'layers' => $row['layers']);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_custom_map')
	{
		$map_id = $_POST["map_id"];
		$name = $_POST["name"];
		$active = $_POST["active"];
		$type = $_POST["type"];
		$url = $_POST["url"];
		$layers = $_POST["layers"];
		
		if ($map_id == 'false')
		{
			$q = "INSERT INTO `gs_maps` 	(`name`,
							`active`,
							`type`,
							`url`,
							`layers`
							) VALUES (
							'".$name."',
							'".$active."',
							'".$type."',
							'".$url."',
							'".$layers."')";
		}
		else
		{
			$q = "UPDATE `gs_maps` SET 	`name`='".$name."', 
							`active`='".$active."',
							`type`='".$type."',
							`url`='".$url."',
							`layers`='".$layers."'
							WHERE `map_id`='".$map_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
	}
	
	if(@$_POST['cmd'] == 'delete_custom_map')
	{
		$map_id = $_POST["map_id"];
		
		$q = "DELETE FROM `gs_maps` WHERE `map_id`='".$map_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}

	
	if(@$_POST['cmd'] == 'delete_all_custom_maps')
	{		
		$q = "DELETE FROM `gs_maps`";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_template_list')
	{
		$result = array();
		
		$q = "SELECT * FROM `gs_templates` WHERE `language`='english' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		while($row=mysqli_fetch_array($r))
		{
			$name = $row['name'];
			
			$result[] = array('name' => $name);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_template')
	{
		$result = array();
		
		$name = $_POST['name'];
		$language = $_POST['language'];
		
		$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='".$language."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if (!$row)
		{
			$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='english'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
		}
		
		$result = array('name' => $row['name'], 'subject' => $row['subject'], 'message' => $row['message']);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_template')
	{
		$name = $_POST['name'];
		$language = $_POST['language'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		
		$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='".$language."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			$q = "UPDATE gs_templates SET `subject`='".$subject."', `message`='".$message."' WHERE `name`='".$name."' AND `language`='".$language."'";
			$r = mysqli_query($ms, $q);
		}
		else
		{
			$q = "INSERT INTO `gs_templates` 	(`name`,
								`language`,
								`subject`,
								`message`)
								VALUES (
								'".$name."',
								'".$language."',
								'".$subject."',
								'".$message."')";		
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_log_list')
	{
		$result = array();
		
		$dir = $gsValues['PATH_ROOT'].'/logs';
		$dh = opendir($dir);
		
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				$modified = convUserTimezone(gmdate("Y-m-d H:i:s", filemtime($dir.'/'.$file)));
				$size = filesize($dir.'/'.$file);
				$size = number_format($size / 1048576, 3);
				$result[] = array('name' => $file, 'modified' => $modified, 'size' => $size);
			}
		}
		
		closedir($dh);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_log')
	{
		$file = $_POST['file'];
		
		$file = $gsValues['PATH_ROOT'].'logs/'.$file;
		if(is_file($file))
		{
			@unlink($file);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_logs')
	{
		$path = $gsValues['PATH_ROOT'].'logs/';
		
		$files = glob($path."*.php");
		
		if (is_array($files))
		{
			foreach($files as $file)
			{
				if(is_file($file))
				{
					@unlink($file);
				}
			}
		}
		
		echo 'OK';
		die;
	}
?>