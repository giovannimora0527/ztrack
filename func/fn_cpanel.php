<?
	set_time_limit(0);
	
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/email.php');
	include ('../tools/sms.php');
	checkUserSession();
	checkUserCPanelPrivileges();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	function checkToUserPrivileges($id)
	{
		global $ms;
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r,MYSQL_ASSOC);
			
			if ($row["manager_id"] != $_SESSION["cpanel_manager_id"])
			{
				die;
			}
		}
	}
	
	function checkToObjectPrivileges($imei)
	{
		global $ms, $la;
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r,MYSQL_ASSOC);
			
			if ($row["manager_id"] != $_SESSION["cpanel_manager_id"])
			{
				echo $la['YOU_HAVE_NO_PRIVILEGES_TO_DO_THAT'];
				die;
			}
		}
	}
	
	if(@$_POST['cmd'] == 'send_email')
	{
		// close connection with web browser and start email sending loop on server side
		ob_start();
		echo 'OK';
		header("Connection: close");
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
		
		$manager_id = $_POST['manager_id'];
		$send_to = $_POST['send_to'];
		$user_ids = $_POST['user_ids'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		
		$count = 0;
		
		$email_arr = array();
		
		if ($send_to == 'all')
		{
			if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
			{
				if ($manager_id == 0)
				{
					$q = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%')";
				}
				else
				{
					$q = "SELECT * FROM `gs_users` WHERE `manager_id`='".$manager_id."' AND `privileges` NOT LIKE ('%subuser%')";
				}
			}
			else
			{
				$q = "SELECT * FROM `gs_users` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND WHERE `privileges` NOT LIKE ('%subuser%')";
			}
			
			$r = mysqli_query($ms, $q);
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$email_arr[] = $row["email"];
			}
			
		}
		else if ($send_to == 'selected')
		{
			$user_ids_ = json_decode(stripslashes($user_ids),true);
			
			foreach ($user_ids_ as $user_id)
			{
				$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r,MYSQL_ASSOC);
				$email_arr[] = $row["email"];
			}
		}
		
		foreach ($email_arr as $email)
		{        
			sendEmail($email, $subject, $message);
			
			$count++;
			if ($count == 50);
			{
				sleep(1);
				$count = 0;
			}
		}
		
		die;
	}
	
	if(@$_POST['cmd'] == 'send_email_test')
	{
		// close connection with web browser and start email sending loop on server side
		ob_start();
		echo 'OK';		
		header("Connection: close");
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
		
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		$email = $_SESSION["email"];
		
		sendEmail($email, $subject, $message);
		die;
	}
	
	if(@$_POST['cmd'] == 'clear_sms_queue')
	{
		clearSMSAPPQueue($gsValues['SMS_GATEWAY_IDENTIFIER']);
		echo 'OK';
		
		die;
	}
	
	if(@$_POST['cmd'] == 'load_cpanel_values')
	{	
		$result = array('server_key' => strtoupper(md5($gsValues['HW_KEY'])),
				'user_id' => $_SESSION["cpanel_user_id"],
				'privileges' => $_SESSION["cpanel_privileges"],
				'language' => $_SESSION["language"]);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_manager_list')
	{			
		$q = "SELECT * FROM `gs_users` WHERE privileges LIKE ('%manager%') ORDER BY `username` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$privileges = json_decode($row['privileges'],true);
			
			if ($privileges['type'] == 'manager')
			{
				$manager_id = $row['id'];
				
				// get user number
				$q2 = "SELECT * FROM `gs_users` WHERE `manager_id`='".$manager_id."'";
				$r2 = mysqli_query($ms, $q2);
				$row2 = mysqli_fetch_array($r2);
				
				$user_count = mysqli_num_rows($r2);
				$user_count -= 1; // - 1 because we do not want count manager user
				
				// get obj number
				$q2 = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
				$r2 = mysqli_query($ms, $q2);
				$obj_count = mysqli_num_rows($r2);
				
				// get obj num
				$obj_num = $row['manager_obj_num'];
				
				$result[$manager_id] = array('username' => $row['username'].' ('.$user_count.' - '.$obj_count.'/'.$obj_num.')');	
			}
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_user_search_list')
	{
		$result = array();
		
		$search = strtoupper(@$_GET['search']);
		$manager_id = @$_GET['manager_id'];
		
		// check if admin or manager
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			if ($manager_id == 0)
			{				
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			if ($manager_id == 0)
			{				
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND  `privileges` NOT LIKE ('%admin%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_users`
			WHERE `privileges` NOT LIKE ('%subuser%')
			AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'
			AND (`id` LIKE '%$search%'
			OR UPPER(`username`) LIKE '%$search%'
			OR UPPER(`email`) LIKE '%$search%')";
		}
		
		$q .= " ORDER BY username ASC";
		
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$data['value'] = $row['id'];
			$data['text'] = htmlentities(stripslashes($row['username']));
			$result[] = $data;	
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_search_list')
	{
		$result = array();
		
		$search = strtoupper(@$_GET['search']);
		$manager_id = @$_GET['manager_id'];
		
		// check if admin or manager
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($manager_id == 0)
			{
				$q = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%'";
			}
			else
			{
				$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%')";
		}
		
		$q .= " ORDER BY name ASC";
		
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$data['value'] = $row['imei'];
			$data['text'] = stripslashes($row['name']);
			$result[] = $data;	
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
		die;
	}

	if(@$_GET['cmd'] == 'load_user_list')
	{	
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = strtoupper(@$_GET['s']); // get search
		$manager_id = @$_GET['manager_id'];
		
		if(!$sidx) $sidx = 1;
		
		// check if admin or manager
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			if ($manager_id == 0)
			{				
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			if ($manager_id == 0)
			{				
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND  `privileges` NOT LIKE ('%admin%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_users`
			WHERE `privileges` NOT LIKE ('%subuser%')
			AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'
			AND (`id` LIKE '%$search%'
			OR UPPER(`privileges`) LIKE '%$search%'
			OR UPPER(`username`) LIKE '%$search%'
			OR UPPER(`email`) LIKE '%$search%')";
		}
		
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q .= " ORDER BY $sidx $sord LIMIT $start, $limit";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($count > 0)
		{
			$i=0;
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				if ($row['active'] == 'true')
				{
					$active= '<img src="theme/images/green_bullet.png" />';
				}
				else
				{
					$active= '<img src="theme/images/red_cross.png" />';
				}
				
				$active_till = '';
				
				if ($row['account_expire'] == 'true')
				{
					if (strtotime($row['account_expire_dt']) > 0)
					{
						$active_till = $row['account_expire_dt'];
					}
				}
				
				$row['privileges'] = json_decode($row['privileges'],true);
				
				$privileges = '';
				if ($row['privileges']['type'] == 'super_admin') {$privileges = $la['SUPER_ADMINISTRATOR'];}
				if ($row['privileges']['type'] == 'admin') {$privileges = $la['ADMINISTRATOR'];}
				if ($row['privileges']['type'] == 'manager') {$privileges = $la['MANAGER'];}
				if ($row['privileges']['type'] == 'user') {$privileges = $la['USER'];}
				if ($row['privileges']['type'] == 'viewer') {$privileges = $la['VIEWER'];}

				if ($row['api'] == 'true')
				{
					$api= '<img src="theme/images/green_bullet.png" />';
				}
				else
				{
					$api= '<img src="theme/images/red_cross.png" />';
				}
				
				$dt_reg = convUserTimezone($row['dt_reg']);
				$dt_login = convUserTimezone($row['dt_login']);
				
				// get sub user number
				$q2 = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$row['id']."'";
				$r2 = mysqli_query($ms, $q2);
				$subacc = mysqli_num_rows($r2);
				
				// get gps objects number
				$q2 = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$row['id']."'";
				$r2 = mysqli_query($ms, $q2);
				$objects = mysqli_num_rows($r2);
				
				// check if some tracker activation will end soon
				while($row2 = mysqli_fetch_array($r2,MYSQL_ASSOC))
				{
					$imei = $row2['imei'];
					$q3 = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
					$r3 = mysqli_query($ms, $q3);
					$row3 = mysqli_fetch_array($r3);
					$active_dt = $row3['active_dt'];
					
					$diff = strtotime($active_dt) - strtotime(gmdate("Y-m-d"));
					$days = $diff / 86400;
					if ($days < $gsValues['NOTIFY_OBJ_EXPIRE_PERIOD'])
					{
						$objects = '<font color="red">'.$objects.'</font>';
					}
				}				
				
				// set modify buttons
				$modify = '<a href="#" onclick="userEdit(\''.$row['id'].'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
				// check if user is not admin or manager, if admin or manager do not show delete button;
				if (($row['privileges']['type'] != "super_admin") && ($row['privileges']['type'] != "admin") && ($row['privileges']['type'] != "manager"))
				{
					$modify .= '<a href="#" onclick="userDelete(\''.$row['id'].'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				}
				$modify .= '<a href="#" onclick="userLogin(\''.$row['id'].'\');" title="'.$la['LOGIN_AS_USER'].'"><img src="theme/images/login-small.png" /></a>';
				// set row
				$responce->rows[$i]['cell']=array($row['id'],$active,$active_till,$privileges,$row['username'],$row['email'],$api,$dt_reg,$dt_login,$row['ip'],$subacc,$objects,$modify);
				$i++;
			}
		}
		
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
		$search = strtoupper(@$_GET['s']); // get search
		$manager_id = @$_GET['manager_id'];
		
		if(!$sidx) $sidx = 1;
		
		// check if admin or manager
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($manager_id == 0)
			{
				$q = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%'";
			}
			else
			{
				$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%')";
		}
		
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q .= " ORDER BY $sidx $sord LIMIT $start, $limit";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($count > 0)
		{
			$i=0;
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				$imei = $row['imei'];
				
				if ($row['active'] == 'true')
				{
					$active= '<img src="theme/images/green_bullet.png" />';
				}
				else
				{
					$active= '<img src="theme/images/red_cross.png" />';
				}
				$active_dt = $row['active_dt'];
				
				$last_connection = $row['dt_server'];
				$dt_now = gmdate("Y-m-d H:i:s");
				
				$dt_difference = strtotime($dt_now) - strtotime($last_connection);
				if($dt_difference < $gsValues['CONNECTION_TIMEOUT'] * 60)
				{
					$loc_valid = $row['loc_valid'];
					
					if ($loc_valid == 1)
					{
						$status = '<img src="img/connection_gsm_gps.png" />';
					}
					else
					{
						$status = '<img src="img/connection_gsm.png" />';
					}
				}
				else
				{
					$status = '<img src="img/connection_no.png" />';
				}
				
				$last_connection = convUserTimezone($last_connection);
				
				$protocol = $row['protocol'];
				$port = $row['port'];
				
				$used_at = '';
				
				$q2 = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' ORDER BY `user_id` ASC";
				$r2 = mysqli_query($ms, $q2);
				
				if (mysqli_num_rows($r2) > 0)
				{
					while($row2 = mysqli_fetch_array($r2,MYSQL_ASSOC))
					{
						$user_id = $row2['user_id'];
						$user = getUserData($user_id);
						
						if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
						{
							$used_at .= '<a href="#" onclick="userEdit(\''.$user_id.'\');">'.$user['username'].'</a>, ';
						}
						else
						{
							if ($user['manager_id'] == $_SESSION["cpanel_manager_id"])
							{
								$used_at .= '<a href="#" onclick="userEdit(\''.$user_id.'\');">'.$user['username'].'</a>, ';
							}
						}
					}
					$used_at = rtrim($used_at, ', ');
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="objectEdit(\''.$imei.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
				$modify .= '<a href="#" onclick="objectClearHistory(\''.$imei.'\');" title="'.$la['CLEAR_HISTORY'].'"><img src="theme/images/clear.png" /></a>';
				$modify .= '<a href="#" onclick="objectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				
				// set row
				$responce->rows[$i]['cell']=array($row['name'],$row['imei'],$row['sim_number'],$active,$active_dt,$last_connection,$protocol,$port,$status,$used_at,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_unused_object_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = strtoupper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx = 1;
		
		$q = "SELECT * FROM `gs_objects_unused` WHERE UPPER(`imei`) LIKE '%$search%'";
		
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q .= " ORDER BY $sidx $sord LIMIT $start, $limit";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($count > 0)
		{
			$i=0;
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				$imei = $row['imei'];
				
				$last_connection = $row['dt_server'];
				$last_connection = convUserTimezone($last_connection);
				
				$protocol = $row['protocol'];
				$port = $row['port'];
				$count = $row['count'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="unusedObjectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				
				// set row
				$responce->rows[$i]['cell']=array($row['imei'],$last_connection,$protocol,$port,$count,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_user_object_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = strtoupper(@$_GET['s']); // get search
		$user_id = $_GET['id'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($count > 0)
		{
			$q2 = "SELECT * FROM `gs_objects` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") ORDER BY $sidx $sord LIMIT $start, $limit";
			$r2 = mysqli_query($ms, $q2);
			
			if (!$r2){die;}
			
			$i=0;
			while($row2 = mysqli_fetch_array($r2,MYSQL_ASSOC)) {
				$imei = $row2['imei'];
				
				if ($row2['active'] == 'true')
				{
					$active = '<input id="dialog_user_edit_object_list_grid_active_'.$imei.'" class="checkbox" type="checkbox" checked="yes"/>';
				}
				else
				{
					$active = '<input id="dialog_user_edit_object_list_grid_active_'.$imei.'" class="checkbox" type="checkbox"/>';
				}
				$active_dt = '<input id="dialog_user_edit_object_list_grid_active_dt_'.$imei.'" value="'.$row2['active_dt'].'" type="text" placeholder="YYYY-MM-DD"/>';
				
				$last_connection = $row2['dt_server'];
				$dt_now = gmdate("Y-m-d H:i:s");
				
				$dt_difference = strtotime($dt_now) - strtotime($last_connection);
				if($dt_difference < $gsValues['CONNECTION_TIMEOUT'] * 60)
				{
					$loc_valid = $row2['loc_valid'];
					
					if ($loc_valid == 1)
					{
						$status = '<img src="img/connection_gsm_gps.png" />';
					}
					else
					{
						$status = '<img src="img/connection_gsm.png" />';
					}
				}
				else
				{
					$status = '<img src="img/connection_no.png" />';
				}
				
				$last_connection = convUserTimezone($last_connection);
				
				// set modify buttons
				$modify = '<a href="#" onclick="userObjectEdit(\''.$imei.'\');" title="'.$la['SAVE'].'"><img src="theme/images/save-small2.png" /></a>';
				$modify .= '<a href="#" onclick="objectEdit(\''.$imei.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
				$modify .= '<a href="#" onclick="objectClearHistory(\''.$imei.'\');" title="'.$la['CLEAR_HISTORY'].'"><img src="theme/images/clear.png" /></a>';
				$modify .= '<a href="#" onclick="userObjectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				// set row
				$responce->rows[$i]['cell']=array($row2['name'],$imei,$active,$active_dt,$last_connection,$status,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_user_subaccount_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = strtoupper(@$_GET['s']); // get search
		$user_id = $_GET['id'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		if ($count > 0)
		{		
			$i=0;
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) {
				$id = $row['id'];
				
				if ($row['active'] == 'true')
				{
					$active = '<input id="dialog_user_edit_subaccount_list_grid_active_'.$id.'" class="checkbox" type="checkbox" checked="yes"/>';
				}
				else
				{
					$active = '<input id="dialog_user_edit_subaccount_list_grid_active_'.$id.'" class="checkbox" type="checkbox"/>';
				}
				
				$username = '<input id="dialog_user_edit_subaccount_list_grid_username_'.$id.'" value="'.$row['username'].'" type="text"/>';
				$email = '<input id="dialog_user_edit_subaccount_list_grid_email_'.$id.'" value="'.$row['email'].'" type="text"/>';
				$password = '<input id="dialog_user_edit_subaccount_list_grid_password_'.$id.'" value="" placeholder="Enter new password" type="text"/>';
				
				$ip = $row['ip'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="userSubaccountEdit(\''.$id.'\');" title="'.$la['SAVE'].'"><img src="theme/images/save-small2.png" /></a>';
				$modify .= '<a href="#" onclick="userSubaccountDelete(\''.$id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				// set row
				$responce->rows[$i]['cell']=array($id,$active,$username,$email,$password,$ip,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'add_object')
	{
		$name = $_POST['name'];
		$imei = strtoupper($_POST['imei']);
		$model = $_POST['model'];
		$vin = $_POST['vin'];
		$plate_number = $_POST['plate_number'];
		$device = $_POST['device'];
		$sim_number = $_POST['sim_number'];
		$manager_id = $_POST['manager_id'];
		$active = $_POST['active'];
		$active_dt = $_POST['active_dt'];
		$user_ids = $_POST['user_ids'];
		
		$user_ids_ = json_decode(stripslashes($user_ids),true);
		
		if ($imei != "")
		{
			if (checkObjectLimitSystem())
			{
				echo $la['SYSTEM_OBJECT_LIMIT_IS_REACHED'];
				die;
			}
			
			if (!checkObjectExistsSystem($imei))
			{
				// check if admin or manager
				if ($_SESSION["cpanel_privileges"] == 'manager')
				{
					$manager_id = $_SESSION["cpanel_manager_id"];
					
					// check object limit
					$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
					$r = mysqli_query($ms, $q);
					$num = mysqli_num_rows($r);
					
					if($num >= $_SESSION["manager_obj_num"])
					{
						echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_OBJECTS'];
						die;
					}
				}
				
				addObjectSystemExtended($name, $imei, $model, $vin, $plate_number, $device, $sim_number, $active, $active_dt, $manager_id);
				
				createObjectDataTable($imei);
				
				for($i=0; $i<count($user_ids_); $i++)
				{
					$user_id = $user_ids_[$i];
									
					addObjectUser($user_id, $imei, 0, 0, 0);
				}
				
				echo 'OK';
			}
			else
			{
				if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
				{
					echo $la['THIS_IMEI_ALREADY_EXISTS'];
				}
				else
				{
					echo $la['THIS_IMEI_ALREADY_EXISTS_OR_IS_USED_BY_ANOTHER_MANAGER'];	
				}
			}
		}
		die;
	}
	
	if(@$_POST['cmd'] == 'clear_object_history')
	{
		$imei = $_POST['imei'];
		
		checkToObjectPrivileges($imei);
		
		clearObjectHistory($imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'edit_object')
	{
		$name = $_POST['name'];
		$imei = $_POST['imei'];
		$model = $_POST['model'];
		$vin = $_POST['vin'];
		$plate_number = $_POST['plate_number'];
		$device = $_POST['device'];
		$sim_number = $_POST['sim_number'];
		$manager_id = $_POST['manager_id'];
		$active = $_POST['active'];
		$active_dt = $_POST['active_dt'];
		$user_ids = $_POST['user_ids'];
		
		checkToObjectPrivileges($imei);
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$manager_id = $_SESSION["cpanel_manager_id"];
		}
		
		$q = "UPDATE `gs_objects` SET 	`name`='".$name."',
						`model`='".$model."',
						`vin`='".$vin."',
						`plate_number`='".$plate_number."',
						`device`='".$device."',
						`sim_number`='".$sim_number."',
						`active`='".$active."',
						`active_dt`='".$active_dt."',
						`manager_id`='".$manager_id."' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		// get object group, driver and trailer settings (we do not want to to lose them)
		$gs_user_objects = array();
				
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$gs_user_objects[] = $row;
		}
		
		// delete object from all users 
		$q = "DELETE FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		// add object to all users
		$user_ids_ = json_decode(stripslashes($user_ids),true);
		
		for($i=0; $i<count($user_ids_); $i++)
		{
			$user_id = $user_ids_[$i];
			
			$group_id = 0;
			$driver_id = 0;
			$trailer_id = 0;
			
			for($j=0; $j<count($gs_user_objects); $j++)
			{
				if ($gs_user_objects[$j]['user_id'] == $user_id)
				{
					$group_id = $gs_user_objects[$j]['group_id'];
					$driver_id = $gs_user_objects[$j]['driver_id'];
					$trailer_id = $gs_user_objects[$j]['trailer_id'];
				}
			}
							
			addObjectUser($user_id, $imei, $group_id, $driver_id, $trailer_id);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object')
	{
		$imei = $_POST['imei'];
		
		checkToObjectPrivileges($imei);
		
		delObjectSystem($imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkToObjectPrivileges($imei);
		
			delObjectSystem($imei);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_unused_object')
	{
		$imei = $_POST['imei'];
		
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_unused_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'add_user_objects')
	{
		$user_id = $_POST['user_id'];
		$imeis = strtoupper($_POST['imeis']);
		
		$imeis_ = json_decode(stripslashes($imeis),true);
		
		for($i=0; $i<count($imeis_); $i++)
		{		    
			$imei = $imeis_[$i];
					
			// check if admin or manager
			if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
			{
				$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			}
			else
			{
				$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."' AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
			}
			
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num >= 1)
			{					
				addObjectUser($user_id, $imei, 0, 0, 0);
			}
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'edit_user_object')
	{
		$imei = $_POST['imei'];
		$active = $_POST['active'];
		$active_dt = $_POST['active_dt'];
		
		checkToObjectPrivileges($imei);
		
		$q = "UPDATE `gs_objects` SET `active`='".$active."', `active_dt`='".$active_dt."' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'get_object_active_period_avg_date')
	{
		$id = $_POST['id'];
		
		echo getObjectActivePeriodAvgDate($id);
		die;
	}
	
	if(@$_POST['cmd'] == 'set_object_active_period_selected')
	{
		$imeis = $_POST["imeis"];
		$active = $_POST['active'];
		$active_dt = $_POST['active_dt'];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			$q = "UPDATE `gs_objects` SET `active`='".$active."', `active_dt`='".$active_dt."' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_user_object')
	{
		$user_id = $_POST['user_id'];
		$imei = $_POST['imei'];
		
		checkToObjectPrivileges($imei);
		
		delObjectUser($user_id, $imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_user_objects')
	{
		$user_id = $_POST['user_id'];
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkToObjectPrivileges($imei);
		
			delObjectUser($user_id, $imei);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'edit_user_subaccount')
	{
		$id = $_POST['id'];
		$active = $_POST['active'];
		$username = $_POST['username'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		// check if same username and email is not used by another user
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo $la['THIS_USERNAME_ALREADY_EXISTS'];
			die;
		}
		
		$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo $la['THIS_EMAIL_ALREADY_EXISTS'];
			die;
		}
		
		$q = "UPDATE `gs_users` SET `active`='".$active."', `username`='".$username."', `email`='".$email."' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// change password
		if ($password != '')
		{
			$q = "UPDATE `gs_users` SET `password`='".md5($password)."' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_user_subaccount')
	{
		$id = $_POST['id'];
		
		$q = "DELETE FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_user_subaccounts')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			$q = "DELETE FROM `gs_users` WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'register_user')
	{
		$email = $_POST['email'];
		$send = $_POST['send'];
		
		if ($email != '')
		{
			$privileges = array();
			$privileges['type'] = 'user';
			$privileges['history'] = stringToBool($gsValues['HISTORY']);
			$privileges['reports'] = stringToBool($gsValues['REPORTS']);
			$privileges['rilogbook'] = stringToBool($gsValues['RILOGBOOK']);
			$privileges['object_control'] = stringToBool($gsValues['OBJECT_CONTROL']);
			$privileges['image_gallery'] = stringToBool($gsValues['IMAGE_GALLERY']);
			$privileges['chat'] = stringToBool($gsValues['CHAT']);
			$privileges = json_encode($privileges);
				
			// check if admin or manager
			if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
			{
				$manager_id = $_POST['manager_id'];
				
			}
			else
			{
				$manager_id = $_SESSION["cpanel_manager_id"];
			}
			
			$result = addUser($send, 'true', 'false', '', $privileges, $manager_id, $email, '', $gsValues['OBJ_HISTORY_CLEAR'], $gsValues['OBJ_EDIT'], $gsValues['OBJ_ADD'], $gsValues['OBJ_NUM'], $gsValues['OBJ_DT']);

			echo $result;
		}
		die;
	}
	
	if(@$_POST['cmd'] == 'edit_user')
	{
		$id = $_POST['id'];
		$username = strtolower($_POST['username']);
		$email = strtolower($_POST['email']);
		$password = $_POST['password'];
		$active = $_POST['active'];
		$account_expire = $_POST['account_expire'];
		$account_expire_dt = $_POST['account_expire_dt'];
		$privileges = $_POST['privileges'];
		$manager_id = $_POST['manager_id'];
		$manager_obj_num = $_POST['manager_obj_num'];
		$obj_history_clear = $_POST['obj_history_clear'];
		$obj_edit = $_POST['obj_edit'];
		$obj_add = $_POST['obj_add'];
		$obj_num = $_POST['obj_num'];
		$obj_dt = $_POST['obj_dt'];
		$api = $_POST['api'];
		$api_key = $_POST['api_key'];
		$info = $_POST['info'];
		$sms_gateway_server = $_POST['sms_gateway_server'];
		$places_markers = $_POST['places_markers'];
		$places_routes = $_POST['places_routes'];
		$places_zones = $_POST['places_zones'];
		
		$comment = $_POST['comment'];
		
		checkToUserPrivileges($id);
		
		// check if same username and email is not used by another user
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo $la['THIS_USERNAME_ALREADY_EXISTS'];
			die;
		}
		
		$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo $la['THIS_EMAIL_ALREADY_EXISTS'];
			die;
		}
		
		$privileges = json_decode(stripslashes($privileges), true);
		
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			if ($privileges['type'] == 'manager')
			{
				$manager_id = $id;
			}
		}		
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			// prevents from setting higher privileges
			if ($privileges['type'] == 'super_admin')
			{
				die;
			}
			
			if ($privileges['type'] == 'manager')
			{
				$manager_id = $id;
			}
		}
		else
		{
			// prevents from setting higher privileges
			if (($privileges['type'] == 'super_admin') || ($privileges['type'] == 'admin'))
			{
				die;
			}
			
			// prevents from settings other user as manager
			if (($privileges['type'] == 'manager') && ($id != $_SESSION["cpanel_manager_id"]))
			{
				die;
			}
			
			// prevents from saving to another users
			if ($id == $_SESSION["cpanel_manager_id"])
			{
				$privileges['type'] = $_SESSION["cpanel_privileges"];
				$manager_obj_num = $_SESSION["manager_obj_num"];
			}
			else
			{
				$manager_obj_num = '';
			}
			
			$manager_id = $_SESSION["cpanel_manager_id"];
		}
		
		// do not allow user to add objects from settings if user is manager or managed by manager
		if ($manager_id != 0)
		{
			$obj_add = 'false';
			$obj_dt = '';
		}
		
		$privileges = json_encode($privileges);
		
		$q = "UPDATE `gs_users` SET 	`active`='".$active."',
						`account_expire`='".$account_expire."',
						`account_expire_dt`='".$account_expire_dt."',
						`privileges`='".$privileges."',
						`manager_id`='".$manager_id."',
						`manager_obj_num`='".$manager_obj_num."',
						`username`='".$username."',
						`email`='".$email."',
						`api`='".$api."',
						`api_key`='".$api_key."',
						`obj_history_clear`='".$obj_history_clear."',
						`obj_edit`='".$obj_edit."',
						`obj_add`='".$obj_add."',
						`obj_num`='".$obj_num."',
						`obj_dt`='".$obj_dt."',
						`info`='".$info."',
						`sms_gateway_server`='".$sms_gateway_server."',
						`places_markers`='".$places_markers."',
						`places_routes`='".$places_routes."',
						`places_zones`='".$places_zones."',
						`comment`='".$comment."'
						WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// change password
		if ($password != '')
		{
			$q = "UPDATE `gs_users` SET `password`='".md5($password)."' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
		
	if(@$_POST['cmd'] == 'delete_user')
	{
		$id = $_POST["id"];
		
		checkToUserPrivileges($id);
		
		delUser($id);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_users')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			checkToUserPrivileges($id);
		
			delUser($id);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'login_user')
	{
		$id = $_POST["id"];
		
		checkToUserPrivileges($id);
		
		setUserSession($id);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_values')
	{
		$imei = $_POST['imei'];
		
		checkToObjectPrivileges($imei);
		
		// get users where object is available
		$users = array();
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' ORDER BY `user_id` ASC";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$row['user_id']."'";
			$r2 = mysqli_query($ms, $q2);
			$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
			
			$data['value'] = $row['user_id'];
			$data['text'] = stripslashes($row2['username']);
			$users[] = $data;
		}
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('name' => $row["name"],
				'imei' => $row["imei"],
				'model' => $row["model"],
				'vin' => $row["vin"],
				'plate_number' => $row["plate_number"],
				'device' => $row["device"],
				'sim_number' => $row["sim_number"],
				'manager_id' => $row["manager_id"],
				'active' => $row["active"],
				'active_dt' => $row["active_dt"],
				'users' => $users
				);
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_user_values')
	{
		$id = $_POST['user_id'];
		
		checkToUserPrivileges($id);
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if (!strtotime($row["account_expire_dt"]) > 0)
		{
			$row["account_expire_dt"] = '';
		}
		
		if($row["api"] == "")
		{
			$row["api"] = 'false';
		}
		
		//check is api key is generated, if not - generate
		if($row["api_key"] == "")
		{
			$row["api_key"] = strtoupper(md5($_POST['user_id'].$row["email"]));
			$row["api_key"] = substr($row["api_key"], -15);
		}
		
		$privileges = array();
		$privileges = json_decode($row['privileges'], true);
		$privileges = checkUserPrivilegesArray($privileges);
		
		if($row["sms_gateway_server"] == "")
		{
			$row["sms_gateway_server"] = 'false';
		}
		
		$info = json_decode($row['info'], true);
		if ($info == null)
		{
			$info = array('name' => '',
				      'company' => '',
				      'address' => '',
				      'post_code' => '',
				      'city' => '',
				      'country' => '',
				      'phone1' => '',
				      'phone2' => '',
				      'email' => ''
				      );
		}
		
		if ($row["obj_history_clear"] == '')
		{
			$row["obj_history_clear"] = 'false';
		}
		
		$result = array('active' => $row["active"],
				'account_expire' => $row["account_expire"],
				'account_expire_dt' => $row["account_expire_dt"],
				'privileges' => $privileges,
				'manager_id' => $row["manager_id"],
				'manager_obj_num' => $row["manager_obj_num"],
				'username' => $row["username"],
				'email' => $row["email"],
				'obj_history_clear' => $row["obj_history_clear"],
				'obj_edit' => $row["obj_edit"],
				'obj_add' => $row["obj_add"],
				'obj_num' => $row["obj_num"],
				'obj_dt' => $row["obj_dt"],
				'api' => $row["api"],
				'api_key' => $row["api_key"],
				'info' => $info,
				'sms_gateway_server' => $row['sms_gateway_server'],
				'places_markers' => $row['places_markers'],
				'places_routes' => $row['places_routes'],
				'places_zones' => $row['places_zones'],
				'comment' => $row['comment']
				);
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_server_values')
	{
		// get not modified server settings
		include ('../config.custom.php');
		
		$result = array('url_logo' => $gsValues['URL_LOGO'],
				'name' => stripcslashes($gsValues['NAME']),
				'generator' => stripcslashes($gsValues['GENERATOR']),
				'show_about' => $gsValues['SHOW_ABOUT'],
				'languages' => $gsValues['LANGUAGES'],
				'url_login' => $gsValues['URL_LOGIN'],
				'url_help' => $gsValues['URL_HELP'],
				'url_contact' => $gsValues['URL_CONTACT'],
				'url_shop' => $gsValues['URL_SHOP'],
				'url_sms_gateway_app' => $gsValues['URL_SMS_GATEWAY_APP'],
				'geocoder_cache' => $gsValues['GEOCODER_CACHE'],
				'connection_timeout' => $gsValues['CONNECTION_TIMEOUT'],
				'history_period' => $gsValues['HISTORY_PERIOD'],
				'db_backup_email' => $gsValues['DB_BACKUP_EMAIL'],				
				'map_osm' => $gsValues['MAP_OSM'],
				'map_bing' => $gsValues['MAP_BING'],
				'map_google' => $gsValues['MAP_GOOGLE'],
				'map_google_traffic' => $gsValues['MAP_GOOGLE_TRAFFIC'],
				'map_mapbox' => $gsValues['MAP_MAPBOX'],
				'map_yandex' => $gsValues['MAP_YANDEX'],
				'map_bing_key' => $gsValues['MAP_BING_KEY'],
				'map_google_key' => $gsValues['MAP_GOOGLE_KEY'],
				'map_mapbox_key' => $gsValues['MAP_MAPBOX_KEY'],
				'map_layer' => $gsValues['MAP_LAYER'],
				'map_zoom' => $gsValues['MAP_ZOOM'],
				'map_lat' => $gsValues['MAP_LAT'],
				'map_lng' => $gsValues['MAP_LNG'],
				'page_after_login' => $gsValues['PAGE_AFTER_LOGIN'],
				'allow_registration' => $gsValues['ALLOW_REGISTRATION'],
				'account_expire' => $gsValues['ACCOUNT_EXPIRE'],
				'account_expire_period' => $gsValues['ACCOUNT_EXPIRE_PERIOD'],
				'language' => $gsValues['LANGUAGE'],				
				'unit_of_distance' => $gsValues['UNIT_OF_DISTANCE'],
				'unit_of_capacity' => $gsValues['UNIT_OF_CAPACITY'],
				'unit_of_temperature' => $gsValues['UNIT_OF_TEMPERATURE'],
				'currency' => $gsValues['CURRENCY'],
				'timezone' => $gsValues['TIMEZONE'],
				'dst' => $gsValues['DST'],
				'dst_start' => $gsValues['DST_START'],
				'dst_end' => $gsValues['DST_END'],
				'obj_add' => $gsValues['OBJ_ADD'],
				'obj_num' => $gsValues['OBJ_NUM'],
				'obj_dt' => $gsValues['OBJ_DT'],
				'obj_trial_period' => $gsValues['OBJ_TRIAL_PERIOD'],
				'obj_edit' => $gsValues['OBJ_EDIT'],
				'obj_history_clear' => $gsValues['OBJ_HISTORY_CLEAR'],
				'history' => $gsValues['HISTORY'],
				'reports' => $gsValues['REPORTS'],
				'rilogbook' => $gsValues['RILOGBOOK'],
				'object_control' => $gsValues['OBJECT_CONTROL'],
				'image_gallery' => $gsValues['IMAGE_GALLERY'],
				'chat' => $gsValues['CHAT'],
				'sms_gateway_server' => $gsValues['SMS_GATEWAY_SERVER'],
				'notify_obj_expire' => $gsValues['NOTIFY_OBJ_EXPIRE'],
				'notify_obj_expire_period' => $gsValues['NOTIFY_OBJ_EXPIRE_PERIOD'],
				'notify_account_expire' => $gsValues['NOTIFY_ACCOUNT_EXPIRE'],
				'notify_account_expire_period' => $gsValues['NOTIFY_ACCOUNT_EXPIRE_PERIOD'],
				'payment_type' => $gsValues['PAYMENT_TYPE'],
				'payment_url' => $gsValues['PAYMENT_URL'],
				'payment_paypal_account' => $gsValues['PAYMENT_PAYPAL_ACCOUNT'],
				'payment_paypal_name' => $gsValues['PAYMENT_PAYPAL_NAME'],
				'payment_paypal_cur' => $gsValues['PAYMENT_PAYPAL_CUR'],
				'payment_paypal_amount' => $gsValues['PAYMENT_PAYPAL_AMOUNT'],
				'payment_paypal_custom' => $gsValues['PAYMENT_PAYPAL_CUSTOM'],
				'payment_paypal_ipn_url' => $gsValues['URL_ROOT'].'/api/billing/paypal.php',
				'email' => $gsValues['EMAIL'],
				'email_no_reply' => $gsValues['EMAIL_NO_REPLY'],
				'email_signature' => $gsValues['EMAIL_SIGNATURE'],
				'email_smtp' => $gsValues['EMAIL_SMTP'],
				'email_smtp_host' => $gsValues['EMAIL_SMTP_HOST'],
				'email_smtp_port' => $gsValues['EMAIL_SMTP_PORT'],
				'email_smtp_auth' => $gsValues['EMAIL_SMTP_AUTH'],
				'email_smtp_secure' => $gsValues['EMAIL_SMTP_SECURE'],
				'email_smtp_username' => $gsValues['EMAIL_SMTP_USERNAME'],
				'email_smtp_password' => $gsValues['EMAIL_SMTP_PASSWORD'],
				'sms_gateway' => $gsValues['SMS_GATEWAY'],
				'sms_gateway_type' => $gsValues['SMS_GATEWAY_TYPE'],
				'sms_gateway_number_filter' => $gsValues['SMS_GATEWAY_NUMBER_FILTER'],
				'sms_gateway_url' => $gsValues['SMS_GATEWAY_URL'],
				'sms_gateway_identifier' => $gsValues['SMS_GATEWAY_IDENTIFIER'],				
				'server_cleanup_users_ae' => $gsValues['SERVER_CLEANUP_USERS_AE'],
				'server_cleanup_objects_not_activated_ae' => $gsValues['SERVER_CLEANUP_OBJECTS_NOT_ACTIVATED_AE'],
				'server_cleanup_objects_not_used_ae' => $gsValues['SERVER_CLEANUP_OBJECTS_NOT_USED_AE'],
				'server_cleanup_db_junk_ae' => $gsValues['SERVER_CLEANUP_DB_JUNK_AE'],
				'server_cleanup_users_days' => $gsValues['SERVER_CLEANUP_USERS_DAYS'],
				'server_cleanup_objects_not_activated_days' => $gsValues['SERVER_CLEANUP_OBJECTS_NOT_ACTIVATED_DAYS'],				
				'reports_schedule' => $gsValues['REPORTS_SCHEDULE'],
				'places_markers' => $gsValues['PLACES_MARKERS'],
				'places_routes' => $gsValues['PLACES_ROUTES'],
				'places_zones' => $gsValues['PLACES_ZONES']
				);
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_server_values')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$config = '';
		
		foreach ($_POST as $key => $value)
		{
			if ($key <> 'cmd')
			{
				$config .= '$gsValues[\''.strtoupper($key).'\'] = "'.$value.'";'."\r\n";
			}
		}
		
		$config = "<?\r\n".$config. "?>";
		
		$handle = fopen('../config.custom.php', 'w');
		fwrite($handle, $config);
		fclose($handle);
		
		echo 'OK';
		die;		
	}
	
	if(@$_POST['cmd'] == 'clear_geocoder_cache')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$q = "DELETE FROM gs_geocoder_cache";
		$r = mysqli_query($ms, $q);
		
		$q = "ALTER TABLE gs_geocoder_cache AUTO_INCREMENT = 1";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
?>