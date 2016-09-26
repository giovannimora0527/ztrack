<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/sms.php');
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
	
	if(@$_POST['cmd'] == 'save_cmd_template')
	{
		$cmd_id = $_POST["cmd_id"];
		$name = $_POST["name"];
		$protocol = $_POST["protocol"];
		$gateway = $_POST["gateway"];
		$type = $_POST["type"];
		$cmd_ = $_POST["cmd_"];
		
		if ($cmd_id == 'false')
		{
			$q = "INSERT INTO `gs_user_cmd`(`user_id`,
							`name`,
							`protocol`,
							`gateway`,
							`type`,
							`cmd`)
							VALUES
							('".$user_id."',
							'".$name."',
							'".$protocol."',
							'".$gateway."',
							'".$type."',
							'".$cmd_."')";	
		}
		else
		{
			$q = "UPDATE `gs_user_cmd` SET 	`name`='".$name."',
							`protocol`='".$protocol."',
							`gateway`='".$gateway."',
							`type`='".$type."',
							`cmd`='".$cmd_."'
							WHERE `cmd_id`='".$cmd_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_cmd_templates')
	{		
		$q = "DELETE FROM `gs_user_cmd` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_cmd_template')
	{
		$cmd_id = $_POST["cmd_id"];
		
		$q = "DELETE FROM `gs_user_cmd` WHERE `cmd_id`='".$cmd_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_cmd_exec')
	{
		$cmd_id = $_POST["cmd_id"];
		
		$q = "DELETE FROM `gs_object_cmd_exec` WHERE `cmd_id`='".$cmd_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'exec_cmd')
	{
		$imei = $_POST["imei"];
		$name = $_POST["name"];
		$gateway = $_POST["gateway"];
		$sim_number = $_POST["sim_number"];
		$type = $_POST["type"];
		$cmd_ = $_POST["cmd_"];
		
		if ($gateway == 'gprs')
		{
			sendObjectGPRSCommand($user_id, $imei, $name, $type, $cmd_);
		}
		else if ($gateway == 'sms')
		{
			$result = sendObjectSMSCommand($user_id, $imei, $name, $cmd_);
			
			if ($result == false)
			{
				echo 'error_sms';
				die;
			}
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_cmd_exec_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx = 1;
		
		// get records number
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$count = mysqli_num_rows($r);
		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".$_SESSION["privileges_imei"].") ORDER BY $sidx $sord";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") ORDER BY $sidx $sord";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$responce = new stdClass();
		$responce->page = 1;
		//$responce->total = $count;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC)) {
			$cmd_id = $row['cmd_id'];
			$time = convUserTimezone($row['dt_cmd']);
			$object = getObjectName($row['imei']);
			
			$name = $row['name'];
			$gateway = strtoupper($row['gateway']);
			$type = strtoupper($row['type']);
			$cmd = $row['cmd'];
			
			if ($row['status'] == 0)
			{
				$status = '<img src="theme/images/loading16.gif" />';
			}
			else if ($row['status'] == 1)
			{
				$status = '<img src="theme/images/green_bullet.png" />';
			}
			
			$re_hex = $row['re_hex'];
			
			// set modify buttons
			$modify = '<a href="#" onclick="cmdExecDelete(\''.$cmd_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['id']=$cmd_id;
			$responce->rows[$i]['cell']=array($time,$object,$name,$gateway,$type,$cmd,$status,$modify,$re_hex);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_cmd_template_list')
	{			
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_cmd` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_cmd` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) {
			$cmd_id = $row['cmd_id'];
			$name = $row['name'];
			$protocol = $row['protocol'];
			$gateway = strtoupper($row['gateway']);
			$type = strtoupper($row['type']);
			$cmd = $row['cmd'];			
			
			// set modify buttons
			$modify = '<a href="#" onclick="cmdTemplateProperties(\''.$cmd_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '<a href="#" onclick="cmdTemplateDelete(\''.$cmd_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['id']=$cmd_id;
			$responce->rows[$i]['cell']=array($name,$protocol,$gateway,$type,$cmd,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_cmd_template_data')
	{
		$q = "SELECT * FROM `gs_user_cmd` WHERE `user_id`='".$user_id."' ORDER BY `cmd_id` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{		
			$cmd_id = $row['cmd_id'];
			$result[$cmd_id] = array(	'name' => $row['name'],
							'protocol' => $row['protocol'],
							'gateway' => $row['gateway'],
							'type' => $row['type'],
							'cmd' => $row['cmd']
							);
		}
		echo json_encode($result);
		die;
	}
?>