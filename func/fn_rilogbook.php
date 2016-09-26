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
	
	if(@$_GET['cmd'] == 'load_rilogbook_list')
	{
		$imei = @$_GET['imei'];
		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		 // get records number		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_rilogbook_data` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_rilogbook_data` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		if (isset($imei))
		{
			$q .= ' AND `imei`="'.$imei.'"';
		}
		
		$group = '';
		if ($_GET['drivers'] == 'true')
		{
			$group .= '"da",';
		}
		if ($_GET['passengers'] == 'true')
		{
			$group .= '"pa",';
		}
		if ($_GET['trailers'] == 'true')
		{
			$group .= '"ta",';
		}
		if ($group == '')
		{
			$group = '""';
		}
		$group = rtrim($group, ',');
		$q .= ' AND `group` IN ('.$group.')';	
		
		if (isset($_GET['dtf']) && isset($_GET['dtt']))
		{
			$q .= " AND dt_server BETWEEN '".convUserUTCTimezone($_GET['dtf'])."' AND '".convUserUTCTimezone($_GET['dtt'])."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
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
				
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_rilogbook_data` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_rilogbook_data` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		if (isset($imei))
		{
			$q .= ' AND `imei`="'.$imei.'"';
		}
		
		$group = '';
		if ($_GET['drivers'] == 'true')
		{
			$group .= '"da",';
		}
		if ($_GET['passengers'] == 'true')
		{
			$group .= '"pa",';
		}
		if ($_GET['trailers'] == 'true')
		{
			$group .= '"ta",';
		}
		if ($group == '')
		{
			$group = '""';
		}
		$group = rtrim($group, ',');
		$q .= ' AND `group` IN ('.$group.')';
		
		if (isset($_GET['dtf']) && isset($_GET['dtt']))
		{
			$q .= " AND dt_server BETWEEN '".convUserUTCTimezone($_GET['dtf'])."' AND '".convUserUTCTimezone($_GET['dtt'])."'";
		}
		
		$q .=  " ORDER BY $sidx $sord LIMIT $start, $limit";
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$rilogbook_id = $row['rilogbook_id'];
			$dt_tracker = convUserTimezone($row['dt_tracker']);
			$imei = $row['imei'];
			$group = $row["group"];
			$assign_id = strtoupper($row["assign_id"]);
			$lat = $row["lat"];
			$lng = $row["lng"];
			
			$object_name = getObjectName($imei);
			
			if ($group == 'da')
			{
				$q2 = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id."' AND `driver_assign_id`='".$assign_id."'";
				$r2 = mysqli_query($ms, $q2);
				$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				if ($row2)
				{
					$assign_id = '<a href="#" onclick="utilsShowDriverInfo(\''.$row2["driver_id"].'\');">';
					$assign_id .= $row2["driver_name"];
					$assign_id .= '</a>';
				}
				
				$group = $la['DRIVER'];
			}
			else if ($group == 'pa')
			{
				$q2 = "SELECT * FROM `gs_user_object_passengers` WHERE `user_id`='".$user_id."' AND `passenger_assign_id`='".$assign_id."'";
				$r2 = mysqli_query($ms, $q2);
				$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				if ($row2)
				{
					$assign_id = '<a href="#" onclick="utilsShowPassengerInfo(\''.$row2["passenger_id"].'\');">';
					$assign_id .= $row2["passenger_name"];
					$assign_id .= '</a>';
				}
				
				$group = $la['PASSENGER'];
			}
			else if ($group == 'ta')
			{
				$q2 = "SELECT * FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id."' AND `trailer_assign_id`='".$assign_id."'";
				$r2 = mysqli_query($ms, $q2);
				$row2 = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				if ($row2)
				{
					$assign_id = '<a href="#" onclick="utilsShowTrailerInfo(\''.$row2["trailer_id"].'\');">';
					$assign_id .= $row2["trailer_name"];
					$assign_id .= '</a>';
				}
				
				$group = $la['TRAILER'];
			}
			
			$lat = sprintf('%0.6f', $lat);
			$lng = sprintf('%0.6f', $lng);
			
			$position = '<a href="http://maps.google.com/maps?q='.$lat.','.$lng.'&t=m" target="_blank">'.$lat.' &deg;, '.$lng.' &deg;</a>';
			
			if ($row["address"] != '')
			{
				$position .= ' - '.$row["address"];	
			}
			
			// set modify buttons
			$modify = '</a><a href="#" onclick="rilogbookDelete(\''.$rilogbook_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			
			// set row
			$responce->rows[$i]['cell']=array($dt_tracker,$object_name,$group,$assign_id,$position,$modify);
			$i++;
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_records')
	{		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "DELETE FROM `gs_rilogbook_data` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "DELETE FROM `gs_rilogbook_data` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_record')
	{
		$rilogbook_id = $_POST["rilogbook_id"];
		
		$q = "DELETE FROM `gs_rilogbook_data` WHERE `rilogbook_id`='".$rilogbook_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
?>