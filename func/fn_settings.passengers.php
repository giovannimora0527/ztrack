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
	
	
	if(@$_POST['cmd'] == 'delete_all_object_passengers')
	{		
		$q = "DELETE FROM `gs_user_object_passengers` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object_passenger')
	{
		$passenger_id = $_POST["passenger_id"];
		
		$q = "DELETE FROM `gs_user_object_passengers` WHERE `passenger_id`='".$passenger_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_passenger')
	{
		$passenger_id = $_POST["passenger_id"];
		$passenger_name = $_POST["passenger_name"];
		$passenger_assign_id = strtoupper($_POST["passenger_assign_id"]);
		$passenger_idn = $_POST["passenger_idn"];
		$passenger_address = $_POST["passenger_address"];
		$passenger_phone = $_POST["passenger_phone"];
		$passenger_email = $_POST["passenger_email"];
		$passenger_desc = $_POST["passenger_desc"];
		
		if ($passenger_id == 'false')
		{
			$q = "INSERT INTO `gs_user_object_passengers`(	`user_id`,
									`passenger_name`,
									`passenger_assign_id`,
									`passenger_idn`,
									`passenger_address`,
									`passenger_phone`,
									`passenger_email`,
									`passenger_desc`)
									VALUES
									('".$user_id."',
									 '".$passenger_name."',
									 '".$passenger_assign_id."',
									 '".$passenger_idn."',
									 '".$passenger_address."',
									 '".$passenger_phone."',
									 '".$passenger_email."',
									 '".$passenger_desc."')";
		}
		else
		{
			$q = "UPDATE `gs_user_object_passengers` SET  	`passenger_name`='".$passenger_name."',
									`passenger_assign_id`='".$passenger_assign_id."',
									`passenger_idn`='".$passenger_idn."',
									`passenger_address`='".$passenger_address."',
									`passenger_phone`='".$passenger_phone."',
									`passenger_email`='".$passenger_email."',
									`passenger_desc`='".$passenger_desc."'
									WHERE `passenger_id`='".$passenger_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_passenger_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_object_passengers` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_object_passengers` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$passenger_id = $row['passenger_id'];
			$passenger_name = $row['passenger_name'];
			$passenger_idn = $row["passenger_idn"];
			$passenger_desc = $row['passenger_desc'];
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsObjectPassengerProperties(\''.$passenger_id.'\');"  title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
			$modify .= '<a href="#" onclick="settingsObjectPassengerDelete(\''.$passenger_id.'\');"  title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['cell']=array($passenger_name,$passenger_idn,$passenger_desc,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_passenger_values')
	{
		$passenger_id = $_POST["passenger_id"];
		
		$q = "SELECT * FROM `gs_user_object_passengers` WHERE `passenger_id`='".$passenger_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('name' => $row['passenger_name'],
				'assign_id' => $row['passenger_assign_id'],
				'idn' => $row['passenger_idn'],
				'address' => $row['passenger_address'],
				'phone' => $row['passenger_phone'],
				'email' => $row['passenger_email'],
				'desc' => $row['passenger_desc']
				);
		
		echo json_encode($result);
		die;
	}
?>