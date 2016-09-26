<? 
        session_start();
        include ('../init.php');
        include ('fn_common.php');
        checkUserSession();
        
        loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['format'] == 'cte')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['templates']); ++$i)
		{
			$name = $result['templates'][$i]['name'];
			$protocol = $result['templates'][$i]['protocol'];
			$gateway = $result['templates'][$i]['gateway'];
			$type = $result['templates'][$i]['type'];
			$cmd = $result['templates'][$i]['cmd'];
			
			$q = 'INSERT INTO `gs_user_cmd` (	`user_id`,
								`name`,
								`protocol`,
								`gateway`,
								`type`,
								`cmd`)
							VALUES ("'.$user_id.'",
								"'.$name.'",
								"'.$protocol.'",
								"'.$gateway.'",
								"'.$type.'",
								"'.$cmd.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'tem')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['templates']); ++$i)
		{
			$name = $result['templates'][$i]['name'];
			$desc = $result['templates'][$i]['desc'];
			$message = $result['templates'][$i]['message'];
			
			$q = 'INSERT INTO `gs_user_templates` (	`user_id`,
								`name`,
								`desc`,
								`message`)
							VALUES ("'.$user_id.'",
								"'.$name.'",
								"'.$desc.'",
								"'.$message.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'otr')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['trailers']); ++$i)
		{
			$trailer_name = $result['trailers'][$i]['trailer_name'];
			$trailer_assign_id = $result['trailers'][$i]['trailer_assign_id'];
			$trailer_model = $result['trailers'][$i]['trailer_model'];
			$trailer_vin = $result['trailers'][$i]['trailer_vin'];
			$trailer_plate_number = $result['trailers'][$i]['trailer_plate_number'];
			$trailer_desc = $result['trailers'][$i]['trailer_desc'];
			
			$q = 'INSERT INTO `gs_user_object_trailers` (	`user_id`,
									`trailer_name`,
									`trailer_assign_id`,
									`trailer_model`,
									`trailer_vin`,
									`trailer_plate_number`,
									`trailer_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$trailer_name.'",
								    "'.$trailer_assign_id.'",
								    "'.$trailer_model.'",
								    "'.$trailer_vin.'",
								    "'.$trailer_plate_number.'",
								    "'.$trailer_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'opa')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['passengers']); ++$i)
		{
			$passenger_name = $result['passengers'][$i]['passenger_name'];
			$passenger_assign_id = $result['passengers'][$i]['passenger_assign_id'];
			$passenger_idn = $result['passengers'][$i]['passenger_idn'];
			$passenger_address = $result['passengers'][$i]['passenger_address'];
			$passenger_phone = $result['passengers'][$i]['passenger_phone'];
			$passenger_email = $result['passengers'][$i]['passenger_email'];
			$passenger_desc = $result['passengers'][$i]['passenger_desc'];
			
			$q = 'INSERT INTO `gs_user_object_passengers` (	`user_id`,
									`passenger_name`,
									`passenger_assign_id`,
									`passenger_idn`,
									`passenger_address`,
									`passenger_phone`,
									`passenger_email`,
									`passenger_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$passenger_name.'",
								    "'.$passenger_assign_id.'",
								    "'.$passenger_idn.'",
								    "'.$passenger_address.'",
								    "'.$passenger_phone.'",
								    "'.$passenger_email.'",
								    "'.$passenger_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'odr')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['drivers']); ++$i)
		{
			$driver_name = $result['drivers'][$i]['driver_name'];
			$driver_assign_id = $result['drivers'][$i]['driver_assign_id'];
			$driver_idn = $result['drivers'][$i]['driver_idn'];
			$driver_address = $result['drivers'][$i]['driver_address'];
			$driver_phone = $result['drivers'][$i]['driver_phone'];
			$driver_email = $result['drivers'][$i]['driver_email'];
			$driver_desc = $result['drivers'][$i]['driver_desc'];
			$driver_img_file = $result['drivers'][$i]['driver_img_file'];
			
			$q = 'INSERT INTO `gs_user_object_drivers` (	`user_id`,
									`driver_name`,
									`driver_assign_id`,
									`driver_idn`,
									`driver_address`,
									`driver_phone`,
									`driver_email`,
									`driver_desc`,
									`driver_img_file`)
							    VALUES ("'.$user_id.'",
								    "'.$driver_name.'",
								    "'.$driver_assign_id.'",
								    "'.$driver_idn.'",
								    "'.$driver_address.'",
								    "'.$driver_phone.'",
								    "'.$driver_email.'",
								    "'.$driver_desc.'",
								    "'.$driver_img_file.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'ogr')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['groups']); ++$i)
		{
			$group_name = $result['groups'][$i]['group_name'];
			$group_desc = $result['groups'][$i]['group_desc'];
			
			$q = 'INSERT INTO `gs_user_object_groups` (	`user_id`,
									`group_name`,
									`group_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$group_name.'",
								    "'.$group_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'pgr')
        {
                $user_id = $_SESSION["user_id"];
		
		$result = json_decode(stripslashes($_POST['result']),true);
		
		for ($i=0; $i<count($result['groups']); ++$i)
		{
			$group_name = $result['groups'][$i]['group_name'];
			$group_desc = $result['groups'][$i]['group_desc'];
			
			$q = 'INSERT INTO `gs_user_places_groups` (	`user_id`,
									`group_name`,
									`group_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$group_name.'",
								    "'.$group_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['format'] == 'ser')
        {
                $imei = $_POST["imei"];
                
                if (!checkUserToObjectPrivileges($imei))
		{
			die;
		}
                
                $result = json_decode(stripslashes($_POST['result']),true);
                
                for ($i=0; $i<count($result['services']); ++$i)
                {
                        $name = $result['services'][$i]['name'];
                        $odo = $result['services'][$i]['odo'];
                        $odo_interval = $result['services'][$i]['odo_interval'];
                        $odo_last = $result['services'][$i]['odo_last'];
                        $engh = $result['services'][$i]['engh'];
                        $engh_interval = $result['services'][$i]['engh_interval'];
                        $engh_last = $result['services'][$i]['engh_last'];
                        $days = $result['services'][$i]['days'];
                        $days_interval = $result['services'][$i]['days_interval'];
                        $days_last = $result['services'][$i]['days_last'];
                        $odo_left = $result['services'][$i]['odo_left'];
                        $odo_left_num = $result['services'][$i]['odo_left_num'];
                        $engh_left = $result['services'][$i]['engh_left'];
                        $engh_left_num = $result['services'][$i]['engh_left_num'];
                        $days_left = $result['services'][$i]['days_left'];
                        $days_left_num = $result['services'][$i]['days_left_num'];
                        $update_last = $result['services'][$i]['update_last'];
                        
                        $q = 'INSERT INTO `gs_object_services`  (`imei`,
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
                                                VALUES ("'.$imei.'",
                                                        "'.$name.'",
                                                        "'.$odo.'",
                                                        "'.$odo_interval.'",
                                                        "'.$odo_last.'",
                                                        "'.$engh.'",
                                                        "'.$engh_interval.'",
                                                        "'.$engh_last.'",
                                                        "'.$days.'",
                                                        "'.$days_interval.'",
                                                        "'.$days_last.'",
                                                        "'.$odo_left.'",
                                                        "'.$odo_left_num.'",
                                                        "'.$engh_left.'",
                                                        "'.$engh_left_num.'",
                                                        "'.$days_left.'",
                                                        "'.$days_left_num.'",
                                                        "'.$update_last.'")';
                        $r = mysqli_query($ms, $q);
                }
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['format'] == 'sen')
        {
                $imei = $_POST["imei"];
                
                if (!checkUserToObjectPrivileges($imei))
		{
			die;
		}
                
                $result = json_decode(stripslashes($_POST['result']),true);
                
                for ($i=0; $i<count($result['sensors']); ++$i)
                {
                        $name = $result['sensors'][$i]['name'];
                        $type = $result['sensors'][$i]['type'];
                        $param = $result['sensors'][$i]['param'];
                        $popup = $result['sensors'][$i]['popup'];
                        $result_type = $result['sensors'][$i]['result_type'];
                        $text_1 = $result['sensors'][$i]['text_1'];
                        $text_0 = $result['sensors'][$i]['text_0'];
                        $units = $result['sensors'][$i]['units'];
                        $lv = $result['sensors'][$i]['lv'];
                        $hv = $result['sensors'][$i]['hv'];
                        $formula = $result['sensors'][$i]['formula'];
                        $calibration = $result['sensors'][$i]['calibration'];
			
			if ($type == 'acc')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'engh')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'odo')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'da')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'pa')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'ta')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
                        
                        $q = 'INSERT INTO `gs_object_sensors`  (`imei`,
                                                                `name`,
                                                                `type`,
                                                                `param`,
                                                                `popup`,
                                                                `result_type`,
                                                                `text_1`,
                                                                `text_0`,
                                                                `units`,
                                                                `lv`,
                                                                `hv`,
                                                                `formula`,
                                                                `calibration`)
                                                VALUES ("'.$imei.'",
                                                        "'.$name.'",
                                                        "'.$type.'",
                                                        "'.$param.'",
                                                        "'.$popup.'",
                                                        "'.$result_type.'",
                                                        "'.$text_1.'",
                                                        "'.$text_0.'",
                                                        "'.$units.'",
                                                        "'.$lv.'",
                                                        "'.$hv.'",
                                                        "'.$formula.'",
                                                        "'.$calibration.'")';
                        $r = mysqli_query($ms, $q);
                }
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['format'] == 'plc')
        {
                $result = json_decode(stripslashes($_POST['result']),true);
                
                $user_id = $_SESSION["user_id"];
                
                // check marker limits
                if ($_POST['markers'] == 'true')
                {
                        $count = getUserNumberOfMarkers($user_id);
                        $count += count($result['markers']);
                        
                        if ($_SESSION["places_markers"] != '')
                        {
                                if ($count > $_SESSION["places_markers"])
                                {
                                        echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_MARKERS'];
                                        die;
                                }
                        }
                        else
                        {
                                if ($count > $gsValues['PLACES_MARKERS'])
                                {
                                        echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_MARKERS'];
                                        die;
                                }
                        }  
                }
                
                // check route limits
                if ($_POST['routes'] == 'true')
                {
                        $count = getUserNumberOfRoutes($user_id);
                        $count += count($result['routes']);
                        
                        if ($_SESSION["places_routes"] != '')
                        {
                                if ($count > $_SESSION["places_routes"])
                                {
                                        echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ROUTES'];
                                        die;
                                }
                        }
                        else
                        {
                                if ($count > $gsValues['PLACES_ROUTES'])
                                {
                                        echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ROUTES'];
                                        die;
                                }
                        }
                }
                
                // check zone limits
                if ($_POST['zones'] == 'true')
                {
                        $count = getUserNumberOfZones($user_id);
                        $count += count($result['zones']);
                        
                        if ($_SESSION["places_zones"] != '')
                        {
                                if ($count > $_SESSION["places_zones"])
                                {
                                        echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ZONES'];
                                        die;
                                }
                        }
                        else
                        {
                                if ($count > $gsValues['PLACES_ZONES'])
                                {
                                        echo $la['YOU_HAVE_REACHED_THE_LIMIT_OF_ZONES'];
                                        die;
                                }
                        }
                }
                
                if ($_POST['markers'] == 'true')
                {                        
                        for ($i=0; $i<count($result['markers']); ++$i)
                        {
                                $marker_name = $result['markers'][$i]['name'];
                                $marker_desc = $result['markers'][$i]['desc'];
                                $marker_icon = $result['markers'][$i]['icon'];
                                $marker_visible = $result['markers'][$i]['visible'];
                                $marker_lat = $result['markers'][$i]['lat'];
                                $marker_lng = $result['markers'][$i]['lng'];
                                
				 $q = 'INSERT INTO `gs_user_markers` (`user_id`,
                                                                        `marker_name`,
                                                                        `marker_desc`,
                                                                        `marker_icon`,
                                                                        `marker_visible`,
                                                                        `marker_lat`,
                                                                        `marker_lng`)
                                                        VALUES ("'.$user_id.'",
                                                                "'.$marker_name.'",
                                                                "'.$marker_desc.'",
                                                                "'.$marker_icon.'",
                                                                "'.$marker_visible.'",
                                                                "'.$marker_lat.'",
                                                                "'.$marker_lng.'")';
                                $r = mysqli_query($ms, $q);
                        }  
                }
                
                if ($_POST['routes'] == 'true')
                {  
                        for ($i=0; $i<count($result['routes']); ++$i)
                        {
                                $route_name = $result['routes'][$i]['name'];
                                $route_color = $result['routes'][$i]['color'];
                                $route_visible = $result['routes'][$i]['visible'];
                                $route_name_visible = $result['routes'][$i]['name_visible'];
                                $route_deviation = $result['routes'][$i]['deviation'];
                                $route_points = $result['routes'][$i]['points'];
                                
                                $q = 'INSERT INTO `gs_user_routes` (`user_id`,
                                                                    `route_name`,
                                                                    `route_color`,
                                                                    `route_visible`,
                                                                    `route_name_visible`,
                                                                    `route_deviation`,
                                                                    `route_points`)
                                                        VALUES ("'.$user_id.'",
                                                                "'.$route_name.'",
                                                                "'.$route_color.'",
                                                                "'.$route_visible.'",
                                                                "'.$route_name_visible.'",
                                                                "'.$route_deviation.'",
                                                                "'.$route_points.'")';
                                $r = mysqli_query($ms, $q);
                        }  
                }
                
                if ($_POST['zones'] == 'true')
                {  
                        for ($i=0; $i<count($result['zones']); ++$i)
                        {
				$zone_name = $result['zones'][$i]['name'];
				$zone_color = $result['zones'][$i]['color'];
				$zone_visible = $result['zones'][$i]['visible'];
				$zone_name_visible = $result['zones'][$i]['name_visible'];
				
				if (isset($result['zones'][$i]['area']))
				{
					$area = $result['zones'][$i]['area'];
				}
				else
				{
					$area = 0;
				}
				
				$zone_vertices = $result['zones'][$i]['vertices'];
				
				$q = 'INSERT INTO `gs_user_zones` (`user_id`,
								    `zone_name`,
								    `zone_color`,
								    `zone_visible`,
								    `zone_name_visible`,
								    `zone_area`,
								    `zone_vertices`)
							VALUES ("'.$user_id.'",
								"'.$zone_name.'",
								"'.$zone_color.'",
								"'.$zone_visible.'",
								"'.$zone_name_visible.'",
								"'.$area.'",
								"'.$zone_vertices.'")';
				$r = mysqli_query($ms, $q);
                        }  
                }
                
                echo 'OK';
                die;
        }
?>