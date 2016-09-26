<?
	// $loc - location data array
	// $ed - event data array
	// $ud - user data array
	// $od - object data array
	
	function check_events($loc, $loc_events)
	{
		global $ms;
		
		$q = "SELECT gs_objects.*, gs_user_objects.*
				FROM gs_objects
				INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
				WHERE gs_user_objects.imei='".$loc['imei']."'";
				
		$r = mysqli_query($ms, $q);
		
		while($od = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			// get user data
			$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$od['user_id']."'";
			$r2 = mysqli_query($ms, $q2);
			$ud = mysqli_fetch_array($r2,MYSQL_ASSOC);
			
			// events loop
			$q2 = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$od['user_id']."' AND UPPER(`imei`) LIKE '%".$loc['imei']."%'";
			$r2 = mysqli_query($ms, $q2);
			
			while($ed = mysqli_fetch_array($r2,MYSQL_ASSOC))
			{			
				if ($ed['active'] == 'true')
				{
					// get current date and time for week days and day time check
					$dt_check = date("Y-m-d H:i:s", strtotime($loc['dt_server'].$ud["timezone"]));
					
					if (!check_event_week_days($dt_check, $ed['week_days']))
					{
						continue;
					}
					
					if (!check_event_day_time($dt_check, $ed['day_time']))
					{
						continue;
					}
					
					if(!check_event_route_trigger($ed, $ud, $loc))
					{
						continue;
					}
					
					if(!check_event_zone_trigger($ed, $ud, $loc))
					{
						continue;
					}
					
					// check for loc events
					if ($loc_events == true)
					{
						if ($ed['type'] == 'overspeed')
						{
							event_overspeed($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'underspeed')
						{
							event_underspeed($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'route_in')
						{
							event_route_in($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'route_out')
						{
							event_route_out($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'zone_in')
						{
							event_zone_in($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'zone_out')
						{
							event_zone_out($ed,$ud,$od,$loc);
						}
					}
					
					// check for events without loc
					if ($ed['type'] == 'param')
					{
						event_param($ed,$ud,$od,$loc);
					}
					
					if ($ed['type'] == 'sensor')
					{
						event_sensor($ed,$ud,$od,$loc);
					}
					
					// check for GPS tracker events
					if (!isset($loc['event']))
					{
						continue;
					}
					
					if (($ed['type'] == 'sos') && ($loc['event'] == 'sos'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'bracon') && ($loc['event'] == 'bracon'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'bracoff') && ($loc['event'] == 'bracoff'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'mandown') && ($loc['event'] == 'mandown'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'shock') && ($loc['event'] == 'shock'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'tow') && ($loc['event'] == 'tow'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'haccel') && ($loc['event'] == 'haccel'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'hbrake') && ($loc['event'] == 'hbrake'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'hcorn') && ($loc['event'] == 'hcorn'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'pwrcut') && ($loc['event'] == 'pwrcut'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'gpsantcut') && ($loc['event'] == 'gpscut'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'lowdc') && ($loc['event'] == 'lowdc'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'lowbat') && ($loc['event'] == 'lowbat'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'jamming') && ($loc['event'] == 'jamming'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
				}
			}
		}
	}
	
	function event_tracker($ed,$ud,$od,$loc)
	{
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		$ed['event_desc'] = $ed['name'];
		
		event_notify($ed,$ud,$od,$loc);
	}
	
	function event_route_in($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if route still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_routes` WHERE `route_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$ed['user_id']."' AND `route_id` IN (".$ed['routes'].")";
		$r = mysqli_query($ms, $q);
		
		while($route = mysqli_fetch_array($r,MYSQL_ASSOC))
		{	
			$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
			
			// get user units and convert if needed
			$units = explode(",", $ud['units']);
			$dist = convDistanceUnits($dist, 'km', $units[0]);
			
			if ($dist <= $route['route_deviation'])
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $route['route_id']);
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$route['route_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}			
			}
			else
			{
				if ($event_status == $route['route_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
	}
	
	function event_route_out($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if route still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_routes` WHERE `route_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$ed['user_id']."' AND `route_id` IN (".$ed['routes'].")";
		$r = mysqli_query($ms, $q);
		
		while($route = mysqli_fetch_array($r,MYSQL_ASSOC))
		{			
			$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
			
			// get user units and convert if needed
			$units = explode(",", $ud['units']);
			$dist = convDistanceUnits($dist, 'km', $units[0]);
			
			if ($dist < $route['route_deviation'])
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $route['route_id']);
				}	
			}
			else
			{
				if ($event_status == $route['route_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$route['route_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}	
			}
		}
	}
	
	function event_zone_in($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if zone still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_zones` WHERE `zone_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$ed['user_id']."' AND `zone_id` IN (".$ed['zones'].")";
		$r = mysqli_query($ms, $q);
		
		if (!$r) { return;}
		
		while($zone = mysqli_fetch_array($r,MYSQL_ASSOC))
		{	
			$in_zone = isPointInPolygon($zone['zone_vertices'], $loc['lat'], $loc['lng']);
			
			if ($in_zone)
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $zone['zone_id']);
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$zone['zone_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}			
			}
			else
			{
				if ($event_status == $zone['zone_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
	}
	
	function event_zone_out($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if zone still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_zones` WHERE `zone_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$ed['user_id']."' AND `zone_id` IN (".$ed['zones'].")";
		$r = mysqli_query($ms, $q);
		
		if (!$r) { return;}
		
		while($zone = mysqli_fetch_array($r,MYSQL_ASSOC))
		{			
			$in_zone = isPointInPolygon($zone['zone_vertices'], $loc['lat'], $loc['lng']);
			
			if ($in_zone)
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $zone['zone_id']);
				}	
			}
			else
			{
				if ($event_status == $zone['zone_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$zone['zone_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}	
			}
		}
	}
	
	function event_param($ed,$ud,$od,$loc)
	{		
		$condition = false;
		
		$pc = explode("|", $ed['checked_value']); // split param condition $pc[0] - param, $pc[1] - eq, gr, lw, $pc[2] - value
		
		$params = $loc['params'];
		
		// check if param exits, if not skip this event
		if (!isset($params[$pc[0]]))
		{
			return;
		}
		
		// check conditions
		if ($pc[1] == 'eq')
		{
			if ($params[$pc[0]] == $pc[2]) $condition = true;
		}
		
		if ($pc[1] == 'gr')
		{
			if ($params[$pc[0]] > $pc[2]) $condition = true;
		}
		
		if ($pc[1] == 'lw')
		{
			if ($params[$pc[0]] < $pc[2]) $condition = true;
		}
		
		if ($condition)
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_sensor($ed,$ud,$od,$loc)
	{
		$condition = false;
		
		$sc = explode("|", $ed['checked_value']); // split param condition $sc[0] - param, $sc[1] - eq, gr, lw, $sc[2] - value
		
		$sensor_name = $sc[0];
		$sensor = false;
		$sensors = getSensors($loc['imei']);
		
		// check if sensors exits, if not skip this event
		if (!$sensors)
		{
			return;
		}
		
		for ($i=0; $i<count($sensors); ++$i)
		{
			if ($sensor_name == $sensors[$i]['name'])
			{
				$sensor = $sensors[$i];
			}
		}
		
		// check if sensor exits, if not skip this event
		if (!$sensor)
		{
			return;
		}
		
		$params = $loc['params'];
		
		// check if param exits, if not skip this event
		if (!isset($params[$sensor['param']]))
		{
			return;
		}
		
		// calc sensor value
		$sensor_value = getSensorValue($params, $sensor);
		
		// check conditions
		if ($sc[1] == 'eq')
		{
			if ($sensor_value['value'] == $sc[2]) $condition = true;
		}
		
		if ($sc[1] == 'gr')
		{
			if ($sensor_value['value'] > $sc[2]) $condition = true;
		}
		
		if ($sc[1] == 'lw')
		{
			if ($sensor_value['value'] < $sc[2]) $condition = true;
		}
		
		if ($condition)
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}

	function event_overspeed($ed,$ud,$od,$loc)
	{
		$speed = $loc['speed'];
		
		// get user speed unit and convert if needed
		$units = explode(",", $ud['units']);
		$speed = convSpeedUnits($speed, 'km', $units[0]);
		
		if ($speed > $ed['checked_value'])
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];;
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_underspeed($ed,$ud,$od,$loc)
	{
		$speed = $loc['speed'];
		
		// get user speed unit and convert if needed
		$units = explode(",", $ud['units']);
		$speed = convSpeedUnits($speed, 'km', $units[0]);
		
		if ($speed < $ed['checked_value'])
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];;
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_notify($ed,$ud,$od,$loc)
	{
		global $ms, $gsValues;
		
		$imei = $loc['imei'];
		
		// duration from last event
		if(!check_event_duration_from_last($ed, $imei))
		{
			
			return;
		}
		else
		{
			$q = "UPDATE `gs_user_events_status` SET `dt_server`='".gmdate("Y-m-d H:i:s")."' WHERE `event_id`='".$ed['event_id']."' AND `imei`='".$imei."'";	
			$r = mysqli_query($ms, $q);	
		}
		
		// insert event into list
		$q = "INSERT INTO `gs_user_events_data` (	user_id,
								type,
								event_desc,
								notify_system,
								imei,
								obj_name,
								dt_server,
								dt_tracker,
								lat,
								lng,
								altitude,
								angle,
								speed,
								params
								) VALUES (
								'".$ed['user_id']."',
								'".$ed['type']."',
								'".$ed['event_desc']."',
								'".$ed['notify_system']."',
								'".$od['imei']."',
								'".$od['name']."',
								'".$loc['dt_server']."',
								'".$loc['dt_tracker']."',
								'".$loc['lat']."',
								'".$loc['lng']."',
								'".$loc['altitude']."',
								'".$loc['angle']."',
								'".$loc['speed']."',
								'".json_encode($loc['params'])."')";
		$r = mysqli_query($ms, $q);
		
		// send cmd
		if ($ed['cmd_send'] == 'true')
		{			
			if ($ed['cmd_gateway'] == 'gprs')
			{				
				sendObjectGPRSCommand($ed['user_id'], $imei, $ed['event_desc'], $ed['cmd_type'], $ed['cmd_string']);
			}
			else if ($ed['cmd_gateway'] == 'sms')
			{
				sendObjectSMSCommand($ed['user_id'], $imei, $ed['event_desc'], $ed['cmd_string']);
			}
		}
		
		// send email notification
		if (($ed['notify_email'] == 'true') && ($ed['notify_email_address'] != ''))
		{			
			$email = $ed['notify_email_address'];
			
			$template = event_notify_template('email',$ed,$ud,$od,$loc);
			
			sendEmail($email, $template['subject'], $template['message'], true);
		}
		
		// send SMS notification
		if (($ed['notify_sms'] == 'true') && ($ed['notify_sms_number'] != ''))
		{
			$number = $ed['notify_sms_number'];
			
			$template = event_notify_template('sms',$ed,$ud,$od,$loc);
			
			if ($ud['sms_gateway'] == 'true')
			{
				if ($ud['sms_gateway_type'] == 'http')
				{
					sendSMSHTTP($ud['sms_gateway_url'], '', $number, $template['message']);	
				}
				else if ($ud['sms_gateway_type'] == 'app')
				{
					sendSMSAPP($ud['sms_gateway_identifier'], '', $number, $template['message']);
				}
			}
			else
			{
				if (($ud['sms_gateway_server'] == 'true') && ($gsValues['SMS_GATEWAY'] == 'true'))
				{
					if ($gsValues['SMS_GATEWAY_TYPE'] == 'http')
					{
						sendSMSHTTP($gsValues['SMS_GATEWAY_URL'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $template['message']);	
					}
					else if ($gsValues['SMS_GATEWAY_TYPE'] == 'app')
					{
						sendSMSAPP($gsValues['SMS_GATEWAY_IDENTIFIER'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $template['message']);
					}
				}
			}
		}
	}
	
	function event_notify_template($type,$ed,$ud,$od,$loc)
	{
		global $ms, $la;
		
		// load language
		loadLanguage($ud["language"], $ud["units"]);
		
		// get template
		$template = array();
		$template['subject'] = '';
		$template['message'] = '';
		
		if ($type == 'email')
		{
			$template = getDefaultTemplate('event_email', $ud["language"]);
		}
		else if ($type == 'sms')
		{
			$template = getDefaultTemplate('event_sms', $ud["language"]);
		}
		
		if ($ed[$type.'_template_id'] != 0)
		{
			$q = "SELECT * FROM `gs_user_templates` WHERE `template_id`='".$ed[$type.'_template_id']."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if ($row)
			{
				if ($row['subject'] != '')
				{
					$template['subject'] = $row['subject'];
				}
				
				if ($row['message'] != '')
				{
					$template['message'] = $row['message'];
				}
			}
		}
		
		// modify template variables
		$g_map = 'http://maps.google.com/maps?q='.$loc['lat'].','.$loc['lng'].'&t=m';
		
		// add timezone to dt_tracker and dt_server
		$dt_server = date("Y-m-d H:i:s", strtotime($loc['dt_server'].$ud["timezone"]));
		$dt_tracker = date("Y-m-d H:i:s", strtotime($loc['dt_tracker'].$ud["timezone"]));
		
		$speed = $loc['speed'];
		$units = explode(",", $ud['units']);
		$speed = convSpeedUnits($speed, 'km', $units[0]);
		$speed = $speed.' '.$la["UNIT_SPEED"];
		
		$driver = getObjectDriver($ud['id'], $od['imei'], $loc['params']);
		
		$trailer = getObjectTrailer($ud['id'], $od['imei'], $loc['params']);
		
		// check if there is address variable
		if ((strpos($template['subject'], "%ADDRESS%") !== "") || (strpos($template['message'], "%ADDRESS%") !== ""))
		{
			$address = geocoderGetAddress($loc["lat"], $loc["lng"]);
		}
		
		foreach ($template as $key => $value)
		{
			$value = str_replace("%NAME%", $od["name"], $value);
			$value = str_replace("%IMEI%", $od["imei"], $value);
			$value = str_replace("%EVENT%", $ed['event_desc'], $value);
			$value = str_replace("%LAT%", $loc["lat"], $value);
			$value = str_replace("%LNG%", $loc["lng"], $value);
			$value = str_replace("%SPEED%", $speed, $value);
			$value = str_replace("%ALT%", $loc["altitude"], $value);
			$value = str_replace("%ANGLE%", $loc["angle"], $value);
			$value = str_replace("%DT_POS%", $dt_tracker, $value);
			$value = str_replace("%DT_SER%", $dt_server, $value);
			$value = str_replace("%G_MAP%", $g_map, $value);
			$value = str_replace("%TR_MODEL%", $od["model"], $value);
			$value = str_replace("%PL_NUM%", $od["plate_number"], $value);
			$value = str_replace("%DRIVER%", $driver['driver_name'], $value);
			$value = str_replace("%TRAILER%", $trailer['trailer_name'], $value);
			$value = str_replace("%ADDRESS%", $address, $value);
			
			$template[$key] = $value;
		}
		
		return $template;
	}
	
	function get_event_status($event_id, $imei)
	{
		global $ms;
		
		$result = '-1';
		
		$q = "SELECT * FROM `gs_user_events_status` WHERE `event_id`='".$event_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if ($row)
		{	
			$result = $row['event_status'];
		}
		else
		{
			$q = "INSERT INTO `gs_user_events_status` (`event_id`,`imei`,`event_status`) VALUES ('".$event_id."','".$imei."','-1')";
			$r = mysqli_query($ms, $q);
		}
		
		return $result;	
	}
	
	function set_event_status($event_id, $imei, $value)
	{
		global $ms;
		
		$q = "UPDATE `gs_user_events_status` SET `event_status`='".$value."' WHERE `event_id`='".$event_id."' AND `imei`='".$imei."'";	
		$r = mysqli_query($ms, $q);
	}
	
	function check_event_duration_from_last($ed, $imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_events_status` WHERE `event_id`='".$ed['event_id']."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if ($row)
		{
			if($ed['duration_from_last_event'] == 'true')
			{				
				if(strtotime($row['dt_server']) >= strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['duration_from_last_event_minutes']." minutes"))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	function check_event_week_days($dt_check, $week_days)
	{
		$day_of_week = date('w', strtotime($dt_check));
		$week_days = explode(';', $week_days);
		
		if ($week_days[$day_of_week] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function check_event_day_time($dt_check, $day_time)
	{
		$day_of_week = date('w', strtotime($dt_check));
		$day_time = json_decode($day_time, true);
		
		if ($day_time != null)
		{
			if ($day_time['dt'] == true)
			{
				if (($day_time['sun'] == true) && ($day_of_week == 0))
				{
					$from = $day_time['sun_from'];
					$to = $day_time['sun_to'];
				}
				else if (($day_time['mon'] == true) && ($day_of_week == 1))
				{
					$from = $day_time['mon_from'];
					$to = $day_time['mon_to'];
				}
				else if (($day_time['tue'] == true) && ($day_of_week == 2))
				{
					$from = $day_time['tue_from'];
					$to = $day_time['tue_to'];
				}
				else if (($day_time['wed'] == true) && ($day_of_week == 3))
				{
					$from = $day_time['wed_from'];
					$to = $day_time['wed_to'];
				}
				else if (($day_time['thu'] == true) && ($day_of_week == 4))
				{
					$from = $day_time['thu_from'];
					$to = $day_time['thu_to'];
				}
				else if (($day_time['fri'] == true) && ($day_of_week == 5))
				{
					$from = $day_time['fri_from'];
					$to = $day_time['fri_to'];
				}
				else if (($day_time['sat'] == true) && ($day_of_week == 6))
				{
					$from = $day_time['sat_from'];
					$to = $day_time['sat_to'];
				}
				else
				{
					return false;
				}
				
				if (isset($from) && isset($to))
				{
					$dt_check = strtotime(date("H:i", strtotime($dt_check)));
					$from = strtotime($from);
					$to = strtotime($to);
					
					if (($from < $dt_check) && ($to > $dt_check))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return true;
				}
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}
	
	function check_event_route_trigger($ed, $ud, $loc)
	{
		global $ms;
		
		$user_id = $ed['user_id'];
		$route_trigger = $ed['route_trigger'];
		$routes = $ed['routes'];
		$lat = $loc['lat'];
		$lng = $loc['lng'];
		
		if (($route_trigger == '') || ($route_trigger == 'off'))
		{
			return true;
		}
		
		if ($route_trigger == 'in')
		{
			$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$user_id."' AND `route_id` IN (".$routes.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			while($route = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
			
				// get user units and convert if needed
				$units = explode(",", $ud['units']);
				$dist = convDistanceUnits($dist, 'km', $units[0]);
				
				if ($dist <= $route['route_deviation'])
				{
					return true;		
				}
			}
		}
		
		if ($route_trigger == 'out')
		{
			$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$user_id."' AND `route_id` IN (".$routes.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			$in_routes = false;
			
			while($route = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
				
				// get user units and convert if needed
				$units = explode(",", $ud['units']);
				$dist = convDistanceUnits($dist, 'km', $units[0]);
				
				if ($dist <= $route['route_deviation'])
				{
					$in_routes = true;
					break;
				}
			}
			
			if ($in_routes == false)
			{
				return true;
			}
		}
		
		return false;
	}
	
	function check_event_zone_trigger($ed, $ud, $loc)
	{
		global $ms;
		
		$user_id = $ed['user_id'];
		$zone_trigger = $ed['zone_trigger'];
		$zones = $ed['zones'];
		$lat = $loc['lat'];
		$lng = $loc['lng'];
		
		if (($zone_trigger == '') || ($zone_trigger == 'off'))
		{
			return true;
		}
		
		if ($zone_trigger == 'in')
		{
			$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."' AND `zone_id` IN (".$zones.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			while($zone = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$in_zone = isPointInPolygon($zone['zone_vertices'], $lat, $lng);
				
				if ($in_zone)
				{				
					return true;
				}
			}
		}
		
		if ($zone_trigger == 'out')
		{
			$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."' AND `zone_id` IN (".$zones.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			$in_zones = false;
			
			while($zone = mysqli_fetch_array($r,MYSQL_ASSOC))
			{	
				$in_zone = isPointInPolygon($zone['zone_vertices'], $lat, $lng);
				
				if ($in_zone)
				{
					$in_zones = true;
					break;
				}
			}
			
			if ($in_zones == false)
			{
				return true;
			}
		}
		
		return false;
	}
?>