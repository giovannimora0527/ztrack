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
	
	if(@$_POST['cmd'] == 'delete_all_object_groups')
	{		
		$q = "DELETE FROM `gs_user_object_groups` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in objects
		$q = "UPDATE `gs_user_objects` SET `group_id`='0' WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object_group')
	{
		$group_id = $_POST["group_id"];
		
		$q = "DELETE FROM `gs_user_object_groups` WHERE `group_id`='".$group_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in objects
		$q = "UPDATE `gs_user_objects` SET `group_id`='0' WHERE `group_id`='".$group_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_group')
	{
		$group_id = $_POST["group_id"];
		$group_name = $_POST["group_name"];
		$group_desc = $_POST["group_desc"];
		
		if ($group_id == 'false')
		{
			$q = "INSERT INTO `gs_user_object_groups` (`user_id`, `group_name`, `group_desc`) VALUES ('".$user_id."', '".$group_name."', '".$group_desc."')";
		}
		else
		{
			$q = "UPDATE `gs_user_object_groups` SET `group_name`='".$group_name."', `group_desc`='".$group_desc."' WHERE `group_id`='".$group_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
	}
	
	if(@$_GET['cmd'] == 'load_object_group_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_object_groups` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_object_groups` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
				
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$group_id = $row['group_id'];
			$group_name = $row['group_name'];
			$group_desc = $row['group_desc'];
			
			// get object number in group
			$q2 = "SELECT * FROM `gs_user_objects` WHERE `group_id`='".$group_id."'";
			$r2 = mysqli_query($ms, $q2);
			$object_number = mysqli_num_rows($r2);
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsObjectGroupProperties(\''.$group_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '</a><a href="#" onclick="settingsObjectGroupDelete(\''.$group_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			
			// set row
			$responce->rows[$i]['cell']=array($group_name,$object_number,$group_desc,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_group_values')
	{
		$q = "SELECT * FROM `gs_user_object_groups` WHERE `user_id`='".$user_id."' ORDER BY `group_name` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		// add ungrouped group
		$result[] = array(	'name' => $la['UNGROUPED'],
					'desc' => '',
					'visible' => true,
					'follow' => false
					);
		
		while($row=mysqli_fetch_array($r))
		{
			$group_id = $row['group_id'];
			$result[$group_id] = array(	'name' => $row['group_name'],
							'desc' => $row['group_desc'],
							'visible' => true,
							'follow' => false
							);
		}
		echo json_encode($result);
		die;
	}
?>