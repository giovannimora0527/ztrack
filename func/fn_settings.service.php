<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
        
        if(@$_POST['cmd'] == 'delete_object_service')
	{
		$service_id = $_POST["service_id"];
		$imei = $_POST["imei"];
		
		$q = "DELETE FROM `gs_object_services` WHERE `service_id`='".$service_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_service')
	{
                $service_id = $_POST["service_id"];
                $imei = $_POST["imei"];
                $name = $_POST["name"];
                $odo = $_POST["odo"];
                $odo_interval = $_POST["odo_interval"];
                $odo_last = $_POST["odo_last"];
                $engh = $_POST["engh"];
                $engh_interval = $_POST["engh_interval"];
                $engh_last = $_POST["engh_last"];
                $days = $_POST["days"];
                $days_interval = $_POST["days_interval"];
                $days_last = $_POST["days_last"];
		
		$odo_left = $_POST["odo_left"];
		$odo_left_num = $_POST["odo_left_num"];
		$engh_left = $_POST["engh_left"];
		$engh_left_num = $_POST["engh_left_num"];
		$days_left = $_POST["days_left"];
		$days_left_num = $_POST["days_left_num"];
		
		$update_last = $_POST["update_last"];
                
                // save in km
		$odo_interval = convDistanceUnits($odo_interval, $_SESSION["unit_distance"], 'km');
		$odo_last = convDistanceUnits($odo_last, $_SESSION["unit_distance"], 'km');
		$odo_left_num = convDistanceUnits($odo_left_num, $_SESSION["unit_distance"], 'km');
		
		if ($service_id == 'false')
		{
			$q = "INSERT INTO `gs_object_services` 	(`imei`,
                                                                `name`,
                                                                `odo`,
                                                                `odo_interval`, 
                                                                `odo_last`,
                                                                `engh`,
                                                                `engh_interval`,
                                                                `engh_last`,
                                                                `days`,
                                                                `days_interval`,
                                                                `days_last`,
								`odo_left`,
								`odo_left_num`,
								`engh_left`,
								`engh_left_num`,
								`days_left`,
								`days_left_num`,
								`update_last`)
                                                                VALUES
                                                                ('".$imei."',
                                                                '".$name."',
                                                                '".$odo."',
                                                                '".$odo_interval."',
                                                                '".$odo_last."',
                                                                '".$engh."',
                                                                '".$engh_interval."',
                                                                '".$engh_last."',
                                                                '".$days."',
                                                                '".$days_interval."',
                                                                '".$days_last."',
								'".$odo_left."',
								'".$odo_left_num."',
								'".$engh_left."',
								'".$engh_left_num."',
								'".$days_left."',
								'".$days_left_num."',
								'".$update_last."')";
		}
		else
		{
			$q = "UPDATE `gs_object_services` SET 	`name`='".$name."',
								`odo`='".$odo."',
								`odo_interval`='".$odo_interval."',
								`odo_last`='".$odo_last."',
								`engh`='".$engh."',
								`engh_interval`='".$engh_interval."',
								`engh_last`='".$engh_last."',
								`days`='".$days."',
								`days_interval`='".$days_interval."',
								`days_last`='".$days_last."',
								`odo_left`='".$odo_left."',
								`odo_left_num`='".$odo_left_num."',
								`engh_left`='".$engh_left."',
								`engh_left_num`='".$engh_left_num."',
								`days_left`='".$days_left."',
								`days_left_num`='".$days_left_num."',
								`update_last`='".$update_last."'
								WHERE `service_id`='".$service_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
                die;
	}
        
        if(@$_GET['cmd'] == 'load_object_service_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$imei = $_GET['imei'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$imei."' ORDER BY $sidx $sord";
		$result = mysqli_query($ms, $q);
		
		$responce = new stdClass();
                
                // get real odometer and engine hours
                $odometer = getObjectOdometer($imei);
		$odometer = convDistanceUnits($odometer, 'km', $_SESSION["unit_distance"]);
                $odometer = round($odometer);
		
		$engine_hours = getObjectEngineHours($imei);
		
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) {
			$service_id = $row["service_id"];
			$name = $row['name'];
                        
                        $status = '';
                        
                        if ($row['odo'] == 'true')
                        {			    
			    $row['odo_interval'] = convDistanceUnits($row['odo_interval'], 'km', $_SESSION["unit_distance"]);
			    $row['odo_last'] = convDistanceUnits($row['odo_last'], 'km', $_SESSION["unit_distance"]);
                            
                            $row['odo_interval'] = round($row['odo_interval']);
                            $row['odo_last'] = round($row['odo_last']);
                            
                            $odo_diff = $odometer - $row['odo_last'];
                            $odo_diff = $row['odo_interval'] - $odo_diff;
                            
                            if ($odo_diff <= 0)
                            {
                                $odo_diff = abs($odo_diff);
                                $str = '<font color="red">'.$la['ODOMETER_EXPIRED'].' ('.$odo_diff.' '.$la["UNIT_DISTANCE"].').</font> ';
                            }
                            else
                            {
                                $str = $la['ODOMETER_LEFT'].' ('.$odo_diff.' '.$la["UNIT_DISTANCE"].'). ';
                            }
                            
                            $status .= $str;
                        }
                        
                        if ($row['engh'] == 'true')
                        {
                            $engh_diff = $engine_hours - $row['engh_last'];
                            $engh_diff = $row['engh_interval'] - $engh_diff;
                            
                            if ($engh_diff <= 0)
                            {
                                $engh_diff = abs($engh_diff);
                                $str = '<font color="red">'.$la['ENGINE_HOURS_EXPIRED'].' ('.$engh_diff.' h).</font> ';
                            }
                            else
                            {
                                $str = $la['ENGINE_HOURS_LEFT'].' ('.$engh_diff.' h). ';
                            }
                            
                            $status .= $str;
                        }
                        
                        if ($row['days'] == 'true')
                        {
                            $days_diff = strtotime(gmdate("Y-m-d")) - (strtotime($row['days_last']));
                            $days_diff = floor($days_diff/3600/24);
                            $days_diff = $row['days_interval'] - $days_diff;
                            
                            if ($days_diff <= 0)
                            {
                                $days_diff = abs($days_diff);
                                $str = '<font color="red">'.$la['DAYS_EXPIRED'].' ('.$days_diff.').</font>';
                            }
                            else
                            {
                                $str = $la['DAYS_LEFT'].' ('.$days_diff.').';
                            }
                            
                            $status .= $str;
                        }
                        
			// set modify buttons
			$modify = '<a href="#" onclick="settingsObjectServiceProperties(\''.$service_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/pen_edit.png" />';
			$modify .= '</a><a href="#" onclick="settingsObjectServiceDelete(\''.$service_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/trash.png" /></a>';
			// set row
			$responce->rows[$i]['cell']=array($name,$status,$modify);
			$i++;
		}
		
		$responce->page = 1;
		//$responce->total = $count;
		$responce->records = $count;
		
		header('Content-type: application/json');
		echo json_encode($responce);
		die;
	}

?>