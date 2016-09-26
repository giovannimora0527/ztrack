<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	include ('../tools/email.php');
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'load_subaccount_values')
	{
		$manager_id = $_SESSION["user_id"];
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$manager_id."' ORDER BY `email` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$privileges = json_decode($row['privileges'],true);
			
			$privileges = checkUserPrivilegesArray($privileges);
			
			$imei = $privileges['imei'];
			$marker = $privileges['marker'];
			$route = $privileges['route'];
			$zone = $privileges['zone'];
			$history = $privileges['history'];
			$reports = $privileges['reports'];
			$rilogbook = $privileges['rilogbook'];
			$object_control = $privileges['object_control'];
			$image_gallery = $privileges['image_gallery'];
			$chat = $privileges['chat'];
			
			if (!strtotime($row["account_expire_dt"]) > 0)
			{
				$row["account_expire_dt"] = '';
			}
			
			if (!isset($privileges['au_active'])) { $privileges['au_active'] = false; }
			$au_active = $privileges['au_active'];
			
			if (!isset($privileges['au'])) { $privileges['au'] = ''; }
			$au = $privileges['au'];
			
			$subaccount_id = $row['id'];
			$result[$subaccount_id] = array('email' => $row['email'],
							'active' => $row['active'],
							'account_expire' => $row['account_expire'],
							'account_expire_dt' => $row['account_expire_dt'],
							'imei' => $imei,
							'marker' => $marker,
							'route' => $route,
							'zone' => $zone,
							'history' => $history,
							'reports' => $reports,
							'rilogbook' => $rilogbook,
							'object_control' => $object_control,
							'image_gallery' => $image_gallery,
							'chat' => $chat,
							'au_active' => $au_active,
							'au' => $au
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_subaccount')
	{
		$subaccount_id= $_POST["subaccount_id"];
		$manager_id = $_SESSION["user_id"];
		
		$q = "DELETE FROM `gs_users` WHERE `id`='".$subaccount_id."' AND `manager_id`='".$manager_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_subaccount')
	{
		$result = '';
		
		$subaccount_id = $_POST["subaccount_id"];
		$email = strtolower($_POST["email"]);
		$password = $_POST["password"];
		$active = $_POST["active"];
		$account_expire = $_POST["account_expire"];
		$account_expire_dt = $_POST["account_expire_dt"];
		$privileges = $_POST["privileges"];
		
		$manager_id = $_SESSION["user_id"];
		
		if ($subaccount_id == 'false')
		{
			$manager_id = $_SESSION["user_id"];
			
			$result = addUser('true', $active, $account_expire, $account_expire_dt, $privileges, $manager_id, $email, $password, 'false', 'false', 'false', '', '');
		}
		else
		{			
			$q = "UPDATE `gs_users` SET 	`active`='".$active."',
							`account_expire`='".$account_expire."',
							`account_expire_dt`='".$account_expire_dt."',
							`username`='".$email."',
							`email`='".$email."',
							`privileges`='".$privileges."'
							WHERE `id`='".$subaccount_id."' AND `manager_id`='".$manager_id."'";
			$r = mysqli_query($ms, $q);
			
			if ($password != '')
			{
				$q = "UPDATE `gs_users` SET `password`='".md5($password)."' WHERE `id`='".$subaccount_id."' AND `manager_id`='".$manager_id."'";
				$r = mysqli_query($ms, $q);
			}
			
			$result = 'OK';
		}
		
		echo $result;
	}
	
	if(@$_GET['cmd'] == 'load_subaccount_list')
	{
		$manager_id = $_SESSION["user_id"];
		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx = 1;
		
		// get records number
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$manager_id."'";
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
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$manager_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$subaccount_id = $row["id"];
			$email = $row['email'];
			
			if ($row['active'] == 'true')
			{
				$active = '<img src="theme/images/green_bullet.png" />';
			}
			else
			{
				$active = '<img src="theme/images/red_cross.png" />';
			}
			
			$privileges = json_decode($row['privileges'],true);
			
			$imeis = count(explode(",", $privileges['imei']));
			
			$markers = explode(",", $privileges['marker']);
			if ($markers[0] == '')
			{
				$markers = 0;
			}
			else
			{
				$markers = count($markers);
			}
			
			$routes = explode(",", $privileges['route']);
			if ($routes[0] == '')
			{
				$routes = 0;
			}
			else
			{
				$routes = count($routes);
			}
			
			$zones = explode(",", $privileges['zone']);
			if ($zones[0] == '')
			{
				$zones = 0;
			}
			else
			{
				$zones = count($zones);
			}
			
			$places = $markers.'/'.$routes.'/'.$zones;
			
			// set modify buttons
			$modify = '<a href="#" onclick="settingsSubaccountProperties(\''.$subaccount_id.'\');"><img src="theme/images/pen_edit.png" title="'.$la['EDIT'].'"/></a>';
			$modify .= '<a href="#" onclick="settingsSubaccountDelete(\''.$subaccount_id.'\');"><img src="theme/images/trash.png" title="'.$la['DELETE'].'"/></a>';
			// set row
			$responce->rows[$i]['cell']=array($email,$active,$imeis,$places,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
?>