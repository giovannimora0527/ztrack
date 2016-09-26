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
	
	if(@$_POST['cmd'] == 'delete_all_object_drivers')
	{		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$img_file = $gsValues['PATH_ROOT'].'data/user/drivers/'.$row['driver_img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}
		}
		
		$q = "DELETE FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset driver_id in objects
		$q = "UPDATE `gs_user_objects` SET `driver_id`='0' WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object_driver')
	{
		$driver_id = $_POST["driver_id"];
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `driver_id`='".$driver_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		$img_file = $gsValues['PATH_ROOT'].'data/user/drivers/'.$row['driver_img_file'];
		if(is_file($img_file))
		{
			@unlink($img_file);
		}
		
		$q = "DELETE FROM `gs_user_object_drivers` WHERE `driver_id`='".$driver_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset driver_id in objects
		$q = "UPDATE `gs_user_objects` SET `driver_id`='0' WHERE `driver_id`='".$driver_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_driver')
	{
		$driver_id = $_POST["driver_id"];
		$driver_name = $_POST["driver_name"];
		$driver_assign_id = strtoupper($_POST["driver_assign_id"]);
		$driver_idn = $_POST["driver_idn"];
		$driver_address = $_POST["driver_address"];
		$driver_phone = $_POST["driver_phone"];
		$driver_email = $_POST["driver_email"];
		$driver_desc = $_POST["driver_desc"];
		$driver_img_file = $_POST["driver_img_file"];
		
		if ($driver_img_file == 'false')
		{
			$driver_img_file = '';
		}
		
		if ($driver_img_file == 'delete')
		{
			$driver_img_file = '';
			
			if ($driver_id != 'false')
			{
				$q = "SELECT * FROM `gs_user_object_drivers` WHERE `driver_id`='".$driver_id."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r,MYSQL_ASSOC);
				
				$img_file = $gsValues['PATH_ROOT'].'data/user/drivers/'.$row['driver_img_file'];
				if(is_file($img_file))
				{
					@unlink($img_file);
				}
				
				$q = "UPDATE `gs_user_object_drivers` SET `driver_img_file`='' WHERE `driver_id`='".$driver_id."'";
				$r = mysqli_query($ms, $q);
			}
		}
		
		if ($driver_img_file == 'true')
		{			
			$driver_img_file = $user_id.'_'.md5(gmdate("Y-m-d H:i:s")).'.png';
			
			$file_temp = $gsValues['PATH_ROOT'].'data/user/drivers/'.$user_id.'_temp.png';
			$file_new = $gsValues['PATH_ROOT'].'data/user/drivers/'.$driver_img_file;
			
			if(is_file($file_temp))
			{
				rename($file_temp, $file_new);
			}
			
			if ($driver_id != 'false')
			{
				$q = "SELECT * FROM `gs_user_object_drivers` WHERE `driver_id`='".$driver_id."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r,MYSQL_ASSOC);
				
				$img_file = $gsValues['PATH_ROOT'].'data/user/drivers/'.$row['driver_img_file'];
				if(is_file($img_file))
				{
					@unlink($img_file);
				}
				
				$q = "UPDATE `gs_user_object_drivers` SET `driver_img_file`='".$driver_img_file."' WHERE `driver_id`='".$driver_id."'";
				$r = mysqli_query($ms, $q);
			}
		}
		
		if ($driver_id == 'false')
		{
			$q = "INSERT INTO `gs_user_object_drivers`(	`user_id`,
									`driver_name`,
									`driver_assign_id`,
									`driver_idn`,
									`driver_address`,
									`driver_phone`,
									`driver_email`,
									`driver_desc`,
									`driver_img_file`)
									VALUES
									('".$user_id."',
									 '".$driver_name."',
									 '".$driver_assign_id."',
									 '".$driver_idn."',
									 '".$driver_address."',
									 '".$driver_phone."',
									 '".$driver_email."',
									 '".$driver_desc."',
									 '".$driver_img_file."')";
		}
		else
		{
			$q = "UPDATE `gs_user_object_drivers` SET  	`driver_name`='".$driver_name."',
									`driver_assign_id`='".$driver_assign_id."',
									`driver_idn`='".$driver_idn."',
									`driver_address`='".$driver_address."',
									`driver_phone`='".$driver_phone."',
									`driver_email`='".$driver_email."',
									`driver_desc`='".$driver_desc."'
									WHERE `driver_id`='".$driver_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_driver_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$driver_id = $row['driver_id'];
			$driver_name = $row['driver_name'];
			$driver_idn = $row["driver_idn"];
			$driver_desc = $row['driver_desc'];
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsObjectDriverProperties(\''.$driver_id.'\');"  title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
			$modify .= '<a href="#" onclick="settingsObjectDriverDelete(\''.$driver_id.'\');"  title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['cell']=array($driver_name,$driver_idn,$driver_desc,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_driver_values')
	{
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."' ORDER BY `driver_name` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$driver_id = $row['driver_id'];
			$result[$driver_id] = array(	'name' => $row['driver_name'],
							'assign_id' => $row['driver_assign_id'],
							'idn' => $row['driver_idn'],
							'address' => $row['driver_address'],
							'phone' => $row['driver_phone'],
							'email' => $row['driver_email'],
							'desc' => $row['driver_desc'],
							'img' => $row['driver_img_file']
							);
		}
		echo json_encode($result);
		die;
	}
?>