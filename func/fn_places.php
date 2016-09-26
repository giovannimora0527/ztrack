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
	
	if(@$_POST['cmd'] == 'delete_all_place_groups')
	{		
		$q = "DELETE FROM `gs_user_places_groups` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in markers
		$q = "UPDATE `gs_user_markers` SET `group_id`='0' WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in routes
		$q = "UPDATE `gs_user_routes` SET `group_id`='0' WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in zones
		$q = "UPDATE `gs_user_zones` SET `group_id`='0' WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_place_group')
	{
		$group_id = $_POST["group_id"];
		
		$q = "DELETE FROM `gs_user_places_groups` WHERE `group_id`='".$group_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in markers
		$q = "UPDATE `gs_user_markers` SET `group_id`='0' WHERE `group_id`='".$group_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in routes
		$q = "UPDATE `gs_user_routes` SET `group_id`='0' WHERE `group_id`='".$group_id."'";
		$r = mysqli_query($ms, $q);
		
		// reset group_id in zones
		$q = "UPDATE `gs_user_zones` SET `group_id`='0' WHERE `group_id`='".$group_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_place_group')
	{
		$group_id = $_POST["group_id"];
		$group_name = $_POST["group_name"];
		$group_desc = $_POST["group_desc"];
		
		if ($group_id == 'false')
		{
			$q = "INSERT INTO `gs_user_places_groups` (`user_id`, `group_name`, `group_desc`) VALUES ('".$user_id."', '".$group_name."', '".$group_desc."')";
		}
		else
		{
			$q = "UPDATE `gs_user_places_groups` SET `group_name`='".$group_name."', `group_desc`='".$group_desc."' WHERE `group_id`='".$group_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
	}
	
	if(@$_GET['cmd'] == 'load_places_group_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_places_groups` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_places_groups` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
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
			
			// get marker/route/zone number in group
			$q2 = "SELECT * FROM `gs_user_markers` WHERE `group_id`='".$group_id."'";
			$r2 = mysqli_query($ms, $q2);
			$marker_number = mysqli_num_rows($r2);
			
			$q2 = "SELECT * FROM `gs_user_routes` WHERE `group_id`='".$group_id."'";
			$r2 = mysqli_query($ms, $q2);
			$route_number = mysqli_num_rows($r2);
			
			$q2 = "SELECT * FROM `gs_user_zones` WHERE `group_id`='".$group_id."'";
			$r2 = mysqli_query($ms, $q2);
			$zone_number = mysqli_num_rows($r2);
			
			$place_number = $marker_number.'/'.$route_number.'/'.$zone_number;
			
			// set modify buttons
			$modify = '<a href="#" onclick="placesGroupProperties(\''.$group_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '</a><a href="#" onclick="placesGroupDelete(\''.$group_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			
			// set row
			$responce->rows[$i]['cell']=array($group_name,$place_number,$group_desc,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_place_group_values')
	{
		$q = "SELECT * FROM `gs_user_places_groups` WHERE `user_id`='".$user_id."' ORDER BY `group_name` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		// add ungrouped group
		$result[] = array(	'name' => $la['UNGROUPED'],
					'desc' => '',
					'marker_visible' => true,
					'route_visible' => true,
					'zone_visible' => true
					);
		
		while($row=mysqli_fetch_array($r))
		{
			$group_id = $row['group_id'];
			$result[$group_id] = array(	'name' => $row['group_name'],
							'desc' => $row['group_desc'],
							'marker_visible' => true,
							'route_visible' => true,
							'zone_visible' => true
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_custom_icon')
	{
		$file = $_POST['file'];
		$path = $gsValues['PATH_ROOT'];
		
		$icon_file = $path.'/'.$file;
		if(is_file($icon_file))
		{
			@unlink($icon_file);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_custom_icons')
	{
		$filter = $_SESSION['user_id'].'_';
		
		$path = $gsValues['PATH_ROOT'].'data/user/places';
		$dh = opendir($path);
	    
		$result = array();
		    
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				if (0 === strpos($file, $filter))
				{
					$icon_file = $path.'/'.$file;
					if(is_file($icon_file))
					{
						@unlink($icon_file);
					}
				}
			}
		}
		
		closedir($dh);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_marker')
	{
		$marker_id = $_POST["marker_id"];
		$group_id = $_POST["group_id"];
		$marker_name = $_POST["marker_name"];
		$marker_desc = $_POST["marker_desc"];
		$marker_icon = $_POST["marker_icon"];
		$marker_visible = $_POST["marker_visible"];
		$marker_lat = $_POST["marker_lat"];
		$marker_lng = $_POST["marker_lng"];
		
		if ($marker_id == 'false')
		{
			$count = getUserNumberOfMarkers($user_id);
			
			if ($_SESSION["places_markers"] != '')
			{
				if ($count >= $_SESSION["places_markers"])
				{
					echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_MARKERS'];
					die;
				}
			}
			else
			{
				if ($count >= $gsValues['PLACES_MARKERS'])
				{
					echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_MARKERS'];
					die;
				}
			}
			
			$q = "INSERT INTO `gs_user_markers` (	`user_id`,
								`group_id`,
								`marker_name`,
								`marker_desc`,
								`marker_icon`,
								`marker_visible`,
								`marker_lat`,
								`marker_lng`)
							VALUES ('".$user_id."',
								'".$group_id."',
								'".$marker_name."',
								'".$marker_desc."',
								'".$marker_icon."',
								'".$marker_visible."',
								'".$marker_lat."',
								'".$marker_lng."')";
		}
		else
		{
			$q = "UPDATE `gs_user_markers` SET	`group_id`='".$group_id."',
								`marker_name`='".$marker_name."',
								`marker_desc`='".$marker_desc."',
								`marker_icon`='".$marker_icon."',
								`marker_visible`='".$marker_visible."',
								`marker_lat`='".$marker_lat."',
								`marker_lng`='".$marker_lng."'
						WHERE 	`marker_id`='".$marker_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_marker')
	{
		$marker_id = $_POST["marker_id"];
		
		$q = "DELETE FROM `gs_user_markers` WHERE `marker_id`='".$marker_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_markers')
	{
		$q = "DELETE FROM `gs_user_markers` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_zone')
	{
		$zone_id = $_POST["zone_id"];
		
		$q = "DELETE FROM `gs_user_zones` WHERE `zone_id`='".$zone_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_zones')
	{
		$q = "DELETE FROM `gs_user_zones` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}

	if(@$_POST['cmd'] == 'save_zone')
	{
		$zone_id = $_POST["zone_id"];
		$group_id = $_POST["group_id"];
		$zone_name = $_POST["zone_name"];
		$zone_color = $_POST["zone_color"];
		$zone_visible = $_POST["zone_visible"];
		$zone_name_visible = $_POST["zone_name_visible"];
		$zone_area = $_POST["zone_area"];
		$zone_vertices = $_POST["zone_vertices"];
		
		if ($zone_id == 'false')
		{
			$count = getUserNumberOfZones($user_id);
			
			if ($_SESSION["places_zones"] != '')
			{
				if ($count >= $_SESSION["places_zones"])
				{
					echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ZONES'];
					die;
				}
			}
			else
			{
				if ($count >= $gsValues['PLACES_ZONES'])
				{
					echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ZONES'];
					die;
				}
			}
			
			$q = "INSERT INTO `gs_user_zones` (	`user_id`,
								`group_id`,
								`zone_name`,
								`zone_color`,
								`zone_visible`,
								`zone_name_visible`,
								`zone_area`,
								`zone_vertices`)
							VALUES ('".$user_id."',
								'".$group_id."',
								'".$zone_name."',
								'".$zone_color."',
								'".$zone_visible."',
								'".$zone_name_visible."',
								'".$zone_area."',
								'".$zone_vertices."')";
		}
		else
		{
			$q = "UPDATE `gs_user_zones` SET 	`group_id`='".$group_id."',
								`zone_name`='".$zone_name."',
								`zone_color`='".$zone_color."',
								`zone_visible`='".$zone_visible."',
								`zone_name_visible`='".$zone_name_visible."',
								`zone_area`='".$zone_area."',
								`zone_vertices`='".$zone_vertices."'
								WHERE 	`zone_id`='".$zone_id."'";	
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_route')
	{
		$route_id = $_POST["route_id"];
		
		$q = "DELETE FROM `gs_user_routes` WHERE `route_id`='".$route_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_routes')
	{
		$q = "DELETE FROM `gs_user_routes` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_route')
	{
		$route_id = $_POST["route_id"];
		$group_id = $_POST["group_id"];
		$route_name = $_POST["route_name"];
		$route_color = $_POST["route_color"];
		$route_visible = $_POST["route_visible"];
		$route_name_visible = $_POST["route_name_visible"];
		$route_deviation = $_POST["route_deviation"];
		$route_points = $_POST["route_points"];
		
		if ($route_id == 'false')
		{
			$count = getUserNumberOfRoutes($user_id);
			
			if ($_SESSION["places_routes"] != '')
			{
				if ($count >= $_SESSION["places_routes"])
				{
					echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ROUTES'];
					die;
				}
			}
			else
			{
				if ($count >= $gsValues['PLACES_ROUTES'])
				{
					echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ROUTES'];
					die;
				}
			}
			
			$q = "INSERT INTO `gs_user_routes` (	`user_id`,
								`group_id`,
								`route_name`,
								`route_color`,
								`route_visible`,
								`route_name_visible`,
								`route_deviation`,
								`route_points`)
							VALUES ('".$user_id."',
								'".$group_id."',
								'".$route_name."',
								'".$route_color."',
								'".$route_visible."',
								'".$route_name_visible."',
								'".$route_deviation."',
								'".$route_points."')";
		}
		else
		{
			$q = "UPDATE `gs_user_routes` SET 	`group_id`='".$group_id."',
								`route_name`='".$route_name."',
								`route_color`='".$route_color."',
								`route_visible`='".$route_visible."',
								`route_name_visible`='".$route_name_visible."',
								`route_deviation`='".$route_deviation."',
								`route_points`='".$route_points."'
								WHERE 	`route_id`='".$route_id."'";	
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_marker_data')
	{
		$result = array();
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT * FROM `gs_user_markers`
			WHERE `user_id`='".$user_id."' AND `marker_id` IN (".$_SESSION["privileges_marker"].")
			ORDER BY `marker_name` ASC";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_markers`
			WHERE `user_id`='".$user_id."' ORDER BY `marker_name` ASC";
		}
		
		$r = mysqli_query($ms, $q);
		
		while($row=mysqli_fetch_array($r))
		{
			$marker_id = $row['marker_id'];
			$result[$marker_id]['visible'] = true;
			
			$result[$marker_id]['data'] = array(	'group_id' => $row['group_id'],
								'name' => $row['marker_name'],
								'desc' => $row['marker_desc'],
								'icon' => $row['marker_icon'],
								'visible' => $row['marker_visible'],
								'lat' => $row['marker_lat'],
								'lng' => $row['marker_lng'],
								);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_route_data')
	{
		$result = array();
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT * FROM `gs_user_routes`
			WHERE `user_id`='".$user_id."' AND `route_id` IN (".$_SESSION["privileges_route"].")
			ORDER BY `route_name` ASC";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_routes`
			WHERE `user_id`='".$user_id."' ORDER BY `route_name` ASC";
		}
		
		$r = mysqli_query($ms, $q);
		
		while($row=mysqli_fetch_array($r))
		{
			$route_id = $row['route_id'];
			$result[$route_id]['visible'] = true;
			
			$result[$route_id]['data'] = array(	'group_id' => $row['group_id'],
								'name' => $row['route_name'],
								'color' => $row['route_color'],
								'visible' => $row['route_visible'],
								'name_visible' => $row['route_name_visible'],
								'deviation' => $row['route_deviation'],
								'points' => $row['route_points'],
								);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_zone_data')
	{
		$result = array();
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT * FROM `gs_user_zones`
			WHERE `user_id`='".$user_id."' AND `zone_id` IN (".$_SESSION["privileges_zone"].")
			ORDER BY `zone_name` ASC";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_zones`
			WHERE `user_id`='".$user_id."' ORDER BY `zone_name` ASC";
		}
		
		$r = mysqli_query($ms, $q);
		
		while($row=mysqli_fetch_array($r))
		{
			$zone_id = $row['zone_id'];
			$result[$zone_id]['visible'] = true;
			
			$result[$zone_id]['data'] = array(	'group_id' => $row['group_id'],
								'name' => $row['zone_name'],
								'color' => $row['zone_color'],
								'visible' => $row['zone_visible'],
								'name_visible' => $row['zone_name_visible'],
								'area' => $row['zone_area'],
								'vertices' => $row['zone_vertices'],
								);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_marker_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = strtoupper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx =1;
		
		// get marker number
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT * FROM `gs_user_markers`
			WHERE `user_id`='".$user_id."' AND UPPER(marker_name) LIKE '%$search%'  AND `marker_id` IN (".$_SESSION["privileges_marker"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_markers`
			WHERE `user_id`='".$user_id."' AND UPPER(marker_name) LIKE '%$search%'";
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
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT gs_user_markers.*, gs_user_places_groups.*
				FROM gs_user_markers
				LEFT JOIN gs_user_places_groups ON gs_user_markers.group_id = gs_user_places_groups.group_id
				WHERE gs_user_markers.user_id='".$user_id."' AND UPPER(gs_user_markers.marker_name) LIKE '%$search%' AND `marker_id` IN (".$_SESSION["privileges_marker"].")
				ORDER BY gs_user_places_groups.group_name ASC, gs_user_markers.marker_name $sord LIMIT $start, $limit";
		}
		else
		{
			$q = "SELECT gs_user_markers.*, gs_user_places_groups.*
				FROM gs_user_markers
				LEFT JOIN gs_user_places_groups ON gs_user_markers.group_id = gs_user_places_groups.group_id
				WHERE gs_user_markers.user_id='".$user_id."' AND UPPER(gs_user_markers.marker_name) LIKE '%$search%'
				ORDER BY gs_user_places_groups.group_name ASC, gs_user_markers.marker_name $sord LIMIT $start, $limit";
		}
		
		// get marker list
		$r= mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$marker_id = $row['marker_id'];
			
			$group_name = $row['group_name'];
			
			if ($group_name == '')
			{
				$group_name = $la['UNGROUPED'];
			}
			
			$marker_show = '<input id="marker_visible_'.$marker_id.'" onClick="placesMarkerClickListCheckbox(\''.$marker_id.'\', this.checked);" class="checkbox" type="checkbox"/>';
			$marker_icon = '<img style="width: 14px; height: 16px;" src="'.$row['marker_icon'].'"/>';
			$marker_name = $row['marker_name'];
			$marker_visible = $row['marker_visible'];
			
			$modify = '<a href="#" onclick="placesMarkerProperties(\''.$marker_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '</a><a href="#" onclick="placesMarkerDelete(\''.$marker_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			
			// set row
			$responce->rows[$i]['cell']=array($marker_id,$group_name,$marker_show,$marker_icon,$marker_name,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_route_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = strtoupper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx =1;
		
		// get route number
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT * FROM `gs_user_routes`
			WHERE `user_id`='".$user_id."' AND UPPER(route_name) LIKE '%$search%'  AND `route_id` IN (".$_SESSION["privileges_route"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_routes`
			WHERE `user_id`='".$user_id."' AND UPPER(route_name) LIKE '%$search%'";
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
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT gs_user_routes.*, gs_user_places_groups.*
				FROM gs_user_routes
				LEFT JOIN gs_user_places_groups ON gs_user_routes.group_id = gs_user_places_groups.group_id
				WHERE gs_user_routes.user_id='".$user_id."' AND UPPER(gs_user_routes.route_name) LIKE '%$search%' AND `route_id` IN (".$_SESSION["privileges_route"].")
				ORDER BY gs_user_places_groups.group_name ASC, gs_user_routes.route_name $sord LIMIT $start, $limit";
		}
		else
		{
			$q = "SELECT gs_user_routes.*, gs_user_places_groups.*
				FROM gs_user_routes
				LEFT JOIN gs_user_places_groups ON gs_user_routes.group_id = gs_user_places_groups.group_id
				WHERE gs_user_routes.user_id='".$user_id."' AND UPPER(gs_user_routes.route_name) LIKE '%$search%'
				ORDER BY gs_user_places_groups.group_name ASC, gs_user_routes.route_name $sord LIMIT $start, $limit";
		}
		
		// get zone list
		$r= mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$route_id = $row['route_id'];
			
			$group_name = $row['group_name'];
			
			if ($group_name == '')
			{
				$group_name = $la['UNGROUPED'];
			}
			
			$route_show = '<input id="route_visible_'.$route_id.'" onClick="placesRouteClickListCheckbox(\''.$route_id.'\', this.checked);" class="checkbox" type="checkbox"/>';
			$route_icon = '<div style="margin:auto; width: 14px; height: 14px; background-color:'.$row['route_color'].';"></div>';
			$route_name = $row['route_name'];
			$route_points = $row['route_points'];
			
			$modify = '<a href="#" onclick="placesRouteProperties(\''.$route_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
			$modify .= '<a href="#" onclick="placesRouteDelete(\''.$route_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			
			// set row
			$responce->rows[$i]['cell']=array($route_id,$group_name,$route_show,$route_icon,$route_name,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_zone_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = strtoupper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx =1;
		
		// get zone number
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT * FROM `gs_user_zones`
			WHERE `user_id`='".$user_id."' AND UPPER(zone_name) LIKE '%$search%'  AND `zone_id` IN (".$_SESSION["privileges_zone"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_user_zones`
			WHERE `user_id`='".$user_id."' AND UPPER(zone_name) LIKE '%$search%'";
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
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{			
			$q = "SELECT gs_user_zones.*, gs_user_places_groups.*
				FROM gs_user_zones
				LEFT JOIN gs_user_places_groups ON gs_user_zones.group_id = gs_user_places_groups.group_id
				WHERE gs_user_zones.user_id='".$user_id."' AND UPPER(gs_user_zones.zone_name) LIKE '%$search%' AND `zone_id` IN (".$_SESSION["privileges_zone"].")
				ORDER BY gs_user_places_groups.group_name ASC, gs_user_zones.zone_name $sord LIMIT $start, $limit";
		}
		else
		{			
			$q = "SELECT gs_user_zones.*, gs_user_places_groups.*
				FROM gs_user_zones
				LEFT JOIN gs_user_places_groups ON gs_user_zones.group_id = gs_user_places_groups.group_id
				WHERE gs_user_zones.user_id='".$user_id."' AND UPPER(gs_user_zones.zone_name) LIKE '%$search%'
				ORDER BY gs_user_places_groups.group_name ASC, gs_user_zones.zone_name $sord LIMIT $start, $limit";
		}
		// get zone list
		$r= mysqli_query($ms, $q);
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$zone_id = $row['zone_id'];
			
			$group_name = $row['group_name'];
			
			if ($group_name == '')
			{
				$group_name = $la['UNGROUPED'];
			}
			
			
			$zone_show = '<input id="zone_visible_'.$zone_id.'" onClick="placesZoneClickListCheckbox(\''.$zone_id.'\', this.checked);" class="checkbox" type="checkbox"/>';
			$zone_icon = '<div style="margin:auto; width: 14px; height: 14px; background-color:'.$row['zone_color'].';"></div>';
			$zone_name = $row['zone_name'];
			$zone_visible = $row['zone_visible'];
			
			$modify = '<a href="#" onclick="placesZoneProperties(\''.$zone_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" /></a>';
			$modify .= '<a href="#" onclick="placesZoneDelete(\''.$zone_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			
			// set row
			$responce->rows[$i]['cell']=array($zone_id,$group_name,$zone_show,$zone_icon,$zone_name,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
?>