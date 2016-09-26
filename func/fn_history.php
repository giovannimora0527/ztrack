<?
	set_time_limit(300);
	
	session_start();	
	include ('../init.php');
	include ('fn_common.php');
	include ('fn_route.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'load_route_data')
	{		
		$imei = $_POST['imei'];
		$dtf = $_POST['dtf'];
		$dtt = $_POST['dtt'];
		$min_stop_duration = $_POST['min_stop_duration'];
		
		$result = getRoute($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $min_stop_duration, true);
		
		mysqli_close($ms);
		
		ob_start();
		header('Content-type: application/json');
		echo json_encode($result);
		header("Connection: close");
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_msg')
	{
		if($_SESSION["obj_history_clear"] == 'true')
		{
			$imei = $_POST['imei'];
			$dt_tracker = $_POST['dt_tracker'];
			
			$q = "DELETE FROM `gs_object_data_".$imei."` WHERE `dt_tracker`='".$dt_tracker."'";
			$r = mysqli_query($ms, $q);
			
			echo 'OK';	
		}
		
		die;
	}
	
	if(@$_GET['cmd'] == 'load_msg_list_empty')
	{
		$responce = new stdClass();
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 0;
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_msg_list')
	{
		$imei = $_GET['imei'];
		$dtf = convUserUTCTimezone($_GET['dtf']);
		$dtt = convUserUTCTimezone($_GET['dtt']);
		
		if (!checkUserToObjectPrivileges($imei))
		{
			return;
		}
		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT DISTINCT	dt_server,
					dt_tracker,
					lat,
					lng,
					altitude,
					angle,
					speed,
					params
					FROM `gs_object_data_".$imei."` WHERE dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
					
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
		
		$q = "SELECT DISTINCT	dt_server,
					dt_tracker,
					lat,
					lng,
					altitude,
					angle,
					speed,
					params
					FROM `gs_object_data_".$imei."` WHERE dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY $sidx $sord LIMIT $start, $limit";
		
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
				$dt_tracker = convUserTimezone($row['dt_tracker']);
				$dt_server = convUserTimezone($row['dt_server']);
				
				$row['speed'] = convSpeedUnits($row['speed'], 'km', $_SESSION["unit_distance"]);
				$row['altitude'] = convAltitudeUnits($row['altitude'], 'km', $_SESSION["unit_distance"]);
				
				if($_SESSION["obj_history_clear"] == 'true')
				{
					$modify = '<a href="#" onclick="historyRouteMsgDelete(\''.$row['dt_tracker'].'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
				}
				else
				{
					$modify = '';
				}
				
				if ($row['params'] == '')
				{
					$row['params'] = '';
				}
				else
				{
					// paramsToArray will be removed later
					$row['params'] = paramsToArray($row['params']);
					
					$arr_params = array();
					
					foreach ($row['params'] as $key => $value)
					{
						if (substr($key, 0, 2) != 'h_')
						{
							array_push($arr_params, $key.'='.$value);	
						}
					}
					
					$row['params'] = implode(', ', $arr_params);
				}
				
				$responce->rows[$i]['id'] = $i;
				$responce->rows[$i]['cell']=array($dt_tracker, $dt_server, $row['lat'], $row['lng'], $row['altitude'], $row['angle'], $row['speed'], $row['params'], $modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}
?>