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
	
	if(@$_POST['cmd'] == 'delete_all_object_trailers')
	{		
		$q = "DELETE FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset trailer_id in objects
		$q = "UPDATE `gs_user_objects` SET `trailer_id`='0' WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object_trailer')
	{
		$trailer_id = $_POST["trailer_id"];
		
		$q = "DELETE FROM `gs_user_object_trailers` WHERE `trailer_id`='".$trailer_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset trailer_id in objects
		$q = "UPDATE `gs_user_objects` SET `trailer_id`='0' WHERE `trailer_id`='".$trailer_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_trailer')
	{
		$trailer_id = $_POST["trailer_id"];
		$trailer_name = $_POST["trailer_name"];
		$trailer_assign_id = strtoupper($_POST["trailer_assign_id"]);
		$trailer_model = $_POST["trailer_model"];
		$trailer_vin = $_POST["trailer_vin"];
		$trailer_plate_number = $_POST["trailer_plate_number"];
		$trailer_desc = $_POST["trailer_desc"];
		
		if ($trailer_id == 'false')
		{
			$q = "INSERT INTO `gs_user_object_trailers`(	`user_id`,
									`trailer_name`,
									`trailer_assign_id`,
									`trailer_model`,
									`trailer_vin`,
									`trailer_plate_number`,
									`trailer_desc`)
									VALUES
									('".$user_id."',
									 '".$trailer_name."',
									 '".$trailer_assign_id."',
									 '".$trailer_model."',
									 '".$trailer_vin."',
									 '".$trailer_plate_number."',
									 '".$trailer_desc."')";
		}
		else
		{
			$q = "UPDATE `gs_user_object_trailers` SET  	`trailer_name`='".$trailer_name."',
									`trailer_assign_id`='".$trailer_assign_id."',
									`trailer_model`='".$trailer_model."',
									`trailer_vin`='".$trailer_vin."',
									`trailer_plate_number`='".$trailer_plate_number."',
									`trailer_desc`='".$trailer_desc."'
									WHERE `trailer_id`='".$trailer_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_trailer_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$trailer_id = $row["trailer_id"];
			$trailer_name = $row["trailer_name"];
			$trailer_desc = $row["trailer_desc"];
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsObjectTrailerProperties(\''.$trailer_id.'\');"  title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
			$modify .= '<a href="#" onclick="settingsObjectTrailerDelete(\''.$trailer_id.'\');"  title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['cell']=array($trailer_name,$trailer_desc,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_trailer_values')
	{
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id."' ORDER BY `trailer_name` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$trailer_id = $row['trailer_id'];
			$result[$trailer_id] = array(	'name' => $row['trailer_name'],
							'assign_id' => $row['trailer_assign_id'],
							'model' => $row['trailer_model'],
							'vin' => $row['trailer_vin'],
							'plate_number' => $row['trailer_plate_number'],
							'desc' => $row['trailer_desc']
							);
		}
		echo json_encode($result);
		die;
	}
?>