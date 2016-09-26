<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'load_template_values')
	{
		$user_id = $_SESSION["user_id"];
		
		$q = "SELECT * FROM `gs_user_templates` WHERE `user_id`='".$user_id."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$template_id = $row['template_id'];
			$result[$template_id] = array(	'name' => $row['name'],
							'desc' => $row['desc'],
							'subject' => $row['subject'],
							'message' => $row['message']
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_template_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$user_id = $_SESSION["user_id"];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_templates` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_templates` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC)) {
			$template_id = $row['template_id'];
			$name = $row['name'];
			$desc = $row['desc'];
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsTemplateProperties(\''.$template_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '</a><a href="#" onclick="settingsTemplateDelete(\''.$template_id.'\');"  title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['cell']=array($name,$desc,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_template')
	{
		$template_id = $_POST["template_id"];
		$user_id = $_SESSION["user_id"];
		
		$q = "DELETE FROM `gs_user_templates` WHERE `template_id`='".$template_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_template')
	{
		$template_id = $_POST["template_id"];
		$user_id = $_SESSION["user_id"];
		$name = $_POST["name"];
		$desc = $_POST["desc"];
		$subject = $_POST["subject"];
		$message = $_POST["message"];
		
		if ($template_id == 'false')
		{
			$q = "INSERT INTO `gs_user_templates` (`user_id`,
								`name`,
								`desc`,
								`subject`,
								`message`
								) VALUES (
								'".$user_id."',
								'".$name."',
								'".$desc."',
								'".$subject."',
								'".$message."')";
		}
		else
		{
			$q = "UPDATE `gs_user_templates` SET 	`name`='".$name."', 
								`desc`='".$desc."',
								`subject`='".$subject."',
								`message`='".$message."'
								WHERE `template_id`='".$template_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
	}
?>