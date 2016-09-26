<?
	set_time_limit(900);

	// check if reports are called by user or service
	if (!isset($_POST['schedule']))
	//{
	//	header("Connection: close");
	//	header("Content-length: 0");
	//	flush();
	//}
	//else
	{
		session_start();
	}
	
	include ('../init.php');
	include ('fn_common.php');
	include ('fn_route.php');
	include ('../tools/gc_func.php');
	include ('../tools/email.php');
	include ('../tools/html2pdf.php');
	
	// check if reports are called by user or service
	if (isset($_POST['schedule']))
	{
		$_SESSION = getUserData($_POST['user_id']);
		loadLanguage($_SESSION["language"], $_SESSION["units"]);
	}
	else
	{
		checkUserSession();
		loadLanguage($_SESSION["language"], $_SESSION["units"]);
	}
	
	$zones_addr = array();
	$zones_addr_loaded = false;
	
	if(@$_POST['cmd'] == 'report')
	{
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{
			$user_id = $_SESSION["manager_id"];
		}
		else
		{
			$user_id = $_SESSION["user_id"];
		}
		
		// generate or send report to e-mail
		if (isset($_POST['schedule']))
		{
			reportsSend();
		}
		else
		{
			echo reportsGenerate();
		}
	}

	function reportsSend()
	{
		global $_POST, $la;
		
		$subject = $la['REPORT'].' - '.$_POST['name'];
		
		$message = $la['HELLO'].",\r\n\r\n";
		$message .=  $la['THIS_IS_REPORT_MESSAGE']."\r\n";
		
		$filename = $_POST['type'].'_'.$_POST['dtf'].'_'.$_POST['dtt'].'.'.$_POST['format'];
		$file_str = reportsGenerate();
		
		sendEmail($_POST['email'], $subject, $message, true, $filename, $file_str);
		
		die;
	}
	
	function reportsGenerate()
	{
		global $_POST, $gsValues;
		
		$name = $_POST['name'];
		$type = $_POST['type'];
		$format = $_POST['format'];
		$show_addresses = $_POST['show_addresses'];
		$zones_addresses = $_POST['zones_addresses'];
		$stop_duration = $_POST['stop_duration'];
		$speed_limit = $_POST['speed_limit'];
		$imei = $_POST['imei'];
		$zone_ids = $_POST['zone_ids'];
		$sensor_names = $_POST['sensor_names'];
		$data_items = $_POST['data_items'];
		$dtf = $_POST['dtf'];
		$dtt = $_POST['dtt'];
		
		$result = '';
		
		$data_items = explode(',', $data_items);
		
		$result .= reportsAddHeaderStart($format);
		$result .= reportsAddStyle($format);
		$result .= reportsAddJS($type);
		$result .= reportsAddHeaderEnd();
		
		if (($format == 'html') || ($format == 'pdf'))
		{
			$result .= '<img style="border:0px" src="'.$gsValues['URL_LOGO'].'" /><hr/>';
		}
		
		$result .= reportsGenerateLoop($type, $imei, $dtf, $dtt, $speed_limit, $stop_duration, $show_addresses, $zones_addresses, $zone_ids, $sensor_names, $data_items);
		
		$result .= '</body></html>';
		
		if ($format == 'pdf')
		{
			$result = html2pdf($result);
		}
		
		if (!isset($_POST['schedule']))
		{
			$result = base64_encode($result);	
		}
		
		return $result;
	}
	
	function reportsGenerateLoop($type, $imei, $dtf, $dtt, $speed_limit, $stop_duration, $show_addresses, $zones_addresses, $zone_ids, $sensor_names, $data_items)
	{
		global $la;
		
		$imeis = explode(",", $imei);
		$result = '';
		
		for ($i=0; $i<count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			// check if object is not removed from system and also if it is active
			if ((!checkObjectExistsUser($imei)) || (!checkObjectActive($imei)))
			{
				continue;
			}
			
			if ($type == "general") //GENERAL_INFO
			{
				$result .= '<h3>'.$la['GENERAL_INFO'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateGenInfo($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $speed_limit, $stop_duration, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "drives_stops") //DRIVES_AND_STOPS
			{
				$result .= '<h3>'.$la['DRIVES_AND_STOPS'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateDrivesAndStops($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $stop_duration, $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "travel_sheet") //TRAVEL_SHEET
			{
				$result .= '<h3>'.$la['TRAVEL_SHEET'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateTravelSheet($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $stop_duration, $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "overspeed") //OVERSPEED
			{
				$result .= '<h3>'.$la['OVERSPEEDS'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateOverspeed($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $speed_limit, $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "underspeed") //UNDERSPEED
			{
				$result .= '<h3>'.$la['UNDERSPEEDS'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateUnderspeed($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $speed_limit, $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "zone_in_out") //ZONE_IN_OUT
			{
				$result .= '<h3>'.$la['ZONE_IN_OUT'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateZoneInOut($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $show_addresses, $zones_addresses, $zone_ids, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "events") //EVENTS
			{
				$result .= '<h3>'.$la['EVENTS'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateEvents($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "service") //SERVICE
			{
				$result .= '<h3>'.$la['SERVICE'].'</h3>';
				$result .= reportsAddReportHeader($imei);
				$result .= reportsGenerateService($imei, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "fuelfillings") //FUEL_FILLINGS
			{
				$result .= '<h3>'.$la['FUEL_FILLINGS'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateFuelFillings($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "fuelthefts") //FUEL_THEFTS
			{
				$result .= '<h3>'.$la['FUEL_THEFTS'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateFuelThefts($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "logic_sensor_info") //LOGIC_SENSOR_INFO
			{
				$sensors = getSensors($imei);
				$sensors_ = array();
				
				$sensor_names_ = explode(",", $sensor_names);				
				for ($j=0; $j<count($sensor_names_); ++$j)
				{
					for ($k=0; $k<count($sensors); ++$k)
					{
						if ($sensors[$k]['result_type'] == 'logic')
						{
							if ($sensor_names_[$j] == $sensors[$k]['name'])
							{
								$sensors_[] = $sensors[$k];
							}
						}
					}
				}
				
				$result .= '<h3>'.$la['LOGIC_SENSOR_INFO'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateLogicSensorInfo($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $sensors_, $show_addresses, $zones_addresses, $data_items);
				$result .= '<br/><hr/>';
			}
			else if ($type == "acc_graph") //ACC
			{
				$sensors = getSensorFromType($imei, 'acc');
				
				$result .= '<h3>'.$la['IGNITION_GRAPH'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateSensorGraph($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $sensors);
				$result .= '<br/><hr/>';
			}
			else if ($type == "fuellevel_graph") //FUEL_LEVEL
			{
				$sensors = getSensorFromType($imei, 'fuel');
				
				$result .= '<h3>'.$la['FUEL_LEVEL_GRAPH'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateSensorGraph($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $sensors);
				$result .= '<br/><hr/>';
			}
			else if ($type == "temperature_graph") //TEMPERATURE
			{
				$sensors = getSensorFromType($imei, 'temp');
				
				$result .= '<h3>'.$la['TEMPERATURE_GRAPH'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateSensorGraph($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $sensors);
				$result .= '<br/><hr/>';
			}			
			else if ($type == "sensor_graph") //SENSOR
			{
				$sensors = getSensors($imei);
				$sensors_ = array();
				
				$sensor_names_ = explode(",", $sensor_names);				
				for ($j=0; $j<count($sensor_names_); ++$j)
				{
					for ($k=0; $k<count($sensors); ++$k)
					{
						if ($sensor_names_[$j] == $sensors[$k]['name'])
						{
							$sensors_[] = $sensors[$k];
						}
					}
				}
				
				$result .= '<h3>'.$la['SENSOR_GRAPH'].'</h3>';
				$result .= reportsAddReportHeader($imei, $dtf, $dtt);
				$result .= reportsGenerateSensorGraph($imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $sensors_);
				$result .= '<br/><hr/>';
			}
		}
		
		if ($type == "general_merged") //GENERAL_INFO_MERGED
		{
			$result .= '<h3>'.$la['GENERAL_INFO_MERGED'].'</h3>';
			$result .= reportsAddReportHeader('', $dtf, $dtt);
			$result .= reportsGenerateGenInfoMerged($imeis, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $speed_limit, $stop_duration, $data_items);
			$result .= '<br/><hr/>';
		}
		
		if ($type == "object_info") //OBJECT_INFO
		{
			$result .= '<h3>'.$la['OBJECT_INFO'].'</h3>';
			$result .= reportsAddReportHeader('', $dtf, $dtt);
			$result .= reportsGenerateObjectInfo($imeis, $data_items);
			$result .= '<br/><hr/>';
		}
		
		if ($type == "rag") //RAG
		{
			$result .= '<h3>'.$la['DRIVER_BEHAVIOR_RAG'].'</h3>';
			$result .= reportsAddReportHeader('', $dtf, $dtt);
			$result .= reportsGenerateRag($imeis, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $speed_limit, $data_items);
			$result .= '<br/><hr/>';
		}
		
		return $result;
	}

	function reportsGenerateGenInfo($imei, $dtf, $dtt, $speed_limit, $stop_duration, $data_items) //GENERAL_INFO
	{
		global $_SESSION, $la, $user_id;
		
		$result = '';		
		$data = getRoute($imei, $dtf, $dtt, $stop_duration, true);
		
		if ($speed_limit > 0)
		{
			$overspeeds = getRouteOverspeeds($data['route'], $speed_limit);
			$overspeeds_count = count($overspeeds);
		}
		else
		{
			$overspeeds_count = 0;
		}
		
		if (count($data['route']) == 0)
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$odometer = getObjectOdometer($imei);
		$odometer = convDistanceUnits($odometer, 'km', $_SESSION["unit_distance"]);
                $odometer  = floor($odometer);
		
		$result .= '<table>';
		if (in_array("route_start", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ROUTE_START'].':</strong></td>
					<td>'.$data['route'][0][0].'</td>
				</tr>';
		}
		
		if (in_array("route_end", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ROUTE_END'].':</strong></td>
					<td>'.$data['route'][count($data['route'])-1][0].'</td>
				</tr>';
		}
		
		if (in_array("route_length", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ROUTE_LENGTH'].':</strong></td>
					<td>'.$data['route_length'].' '.$la["UNIT_DISTANCE"].'</td>
				</tr>';
		}
		
		if (in_array("move_duration", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['MOVE_DURATION'].':</strong></td>
					<td>'.$data['drives_duration'].'</td>
				</tr>';
		}
		
		if (in_array("stop_duration", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['STOP_DURATION'].':</strong></td>
					<td>'.$data['stops_duration'].'</td>
				</tr>';
		}
		
		if (in_array("top_speed", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['TOP_SPEED'].':</strong></td>
					<td>'.$data['top_speed'].' '.$la["UNIT_SPEED"].'</td>
				</tr>';
		}
		
		if (in_array("avg_speed", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['AVG_SPEED'].':</strong></td>
					<td>'.$data['avg_speed'].' '.$la["UNIT_SPEED"].'</td>
				</tr>';
		}
		
		if (in_array("overspeed_count", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['OVERSPEED_COUNT'].':</strong></td>
					<td>'.$overspeeds_count.'</td>
				</tr>';
		}
		
		if (in_array("fuel_consumption", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['FUEL_CONSUMPTION'].':</strong></td>
					<td>'.$data['fuel_consumption'].' '.$la["UNIT_CAPACITY"].'</td>
				</tr>';
		}
		
		if (in_array("fuel_cost", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['FUEL_COST'].':</strong></td>
					<td>'.$data['fuel_cost'].' '.$_SESSION["currency"].'</td>
				</tr>';
		}
		
		if (in_array("engine_work", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ENGINE_WORK'].':</strong></td>
					<td>'.$data['engine_work'].'</td>
				</tr>';
		}
		
		if (in_array("engine_idle", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ENGINE_IDLE'].':</strong></td>
					<td>'.$data['engine_idle'].'</td>
				</tr>';
		}
		
		if (in_array("odometer", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ODOMETER'].':</strong></td>
					<td>'.$odometer.' '.$la["UNIT_DISTANCE"].'</td>
				</tr>';
		}
		
		if (in_array("engine_hours", $data_items))
		{
			$result .= '<tr>
					<td><strong>'.$la['ENGINE_HOURS'].':</strong></td>
					<td>'.getObjectEngineHours($imei).' '.$la['UNIT_H'].'</td>
				</tr>';
		}
		
		if (in_array("driver", $data_items))
		{
			$result .= '<tr>';
			
			$params = $data['route'][count($data['route'])-1][6];
			
			$driver = getObjectDriver($user_id, $imei, $params);
			if ($driver['driver_name'] == ''){$driver['driver_name'] = $la['NA'];}
			
			$result .= 	'<td><strong>'.$la['DRIVER'].':</strong></td>
					<td>'.$driver['driver_name'].'</td>
					</tr>';
		}
		
		if (in_array("trailer", $data_items))
		{
			$result .= '<tr>';
			
			$params = $data['route'][count($data['route'])-1][6];
			$trailer = getObjectTrailer($user_id, $imei, $params);
			if ($trailer['trailer_name'] == ''){$trailer['trailer_name'] = $la['NA'];}
			
			$result .= 	'<td><strong>'.$la['TRAILER'].':</strong></td>
					<td>'.$trailer['trailer_name'].'</td>
					</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateGenInfoMerged($imeis, $dtf, $dtt, $speed_limit, $stop_duration, $data_items) //GENERAL_INFO_MERGED
	{
		global $_SESSION, $la, $user_id;
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("object", $data_items))
		{
			$result .= '<th>'.$la['OBJECT'].'</th>';
		}
		
		if (in_array("route_start", $data_items))
		{
			$result .= '<th>'.$la['ROUTE_START'].'</th>';
		}
		
		if (in_array("route_end", $data_items))
		{
			$result .= '<th>'.$la['ROUTE_END'].'</th>';
		}
		
		if (in_array("route_length", $data_items))
		{
			$result .= '<th>'.$la['ROUTE_LENGTH'].'</th>';
		}
		
		if (in_array("move_duration", $data_items))
		{
			$result .= '<th>'.$la['MOVE_DURATION'].'</th>';
		}
		
		if (in_array("stop_duration", $data_items))
		{
			$result .= '<th>'.$la['STOP_DURATION'].'</th>';
		}
		
		if (in_array("top_speed", $data_items))
		{
			$result .= '<th>'.$la['TOP_SPEED'].'</th>';
		}
		
		if (in_array("avg_speed", $data_items))
		{
			$result .= '<th>'.$la['AVG_SPEED'].'</th>';
		}
		
		if (in_array("overspeed_count", $data_items))
		{
			$result .= '<th>'.$la['OVERSPEED_COUNT'].'</th>';
		}
		
		if (in_array("fuel_consumption", $data_items))
		{
			$result .= '<th>'.$la['FUEL_CONSUMPTION'].'</th>';
		}
		
		if (in_array("fuel_cost", $data_items))
		{
			$result .= '<th>'.$la['FUEL_COST'].'</th>';
		}
		
		if (in_array("engine_work", $data_items))
		{
			$result .= '<th>'.$la['ENGINE_WORK'].'</th>';
		}
		
		if (in_array("engine_idle", $data_items))
		{
			$result .= '<th>'.$la['ENGINE_IDLE'].'</th>';
		}
		
		if (in_array("odometer", $data_items))
		{
			$result .= '<th>'.$la['ODOMETER'].'</th>';
		}
		
		if (in_array("engine_hours", $data_items))
		{
			$result .= '<th>'.$la['ENGINE_HOURS'].'</th>';
		}
		
		if (in_array("driver", $data_items))
		{
			$result .= '<th>'.$la['DRIVER'].'</th>';
		}
		
		if (in_array("trailer", $data_items))
		{
			$result .= '<th>'.$la['TRAILER'].'</th>';
		}
		
		$result .= '</tr>';
		
		$total_route_length = 0;
		$total_drives_duration = 0;
		$total_stops_duration = 0;
		$total_top_speed = 0;
		$total_avg_speed = 0;
		$total_overspeed_count = 0;
		$total_fuel_consumption = 0;
		$total_fuel_cost = 0;
		$total_engine_work = 0;
		$total_engine_idle = 0;
		$total_odometer = 0;
		$total_engine_hours = 0;
		
		for ($i=0; $i<count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			// check if object is not removed from system and also if it is active
			if ((!checkObjectExistsUser($imei)) || (!checkObjectActive($imei)))
			{
				continue;
			}
			
			$data = getRoute($imei, $dtf, $dtt, $stop_duration, true);
					
			if (count($data['route']) == 0)
			{
				$result .= '<tr><td align="center">'.getObjectName($imei).'</td>
						<td align="center" colspan="16">'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td>
						</tr>';
			}
			else
			{
				if ($speed_limit > 0)
				{
					$overspeeds = getRouteOverspeeds($data['route'], $speed_limit);
					$overspeed_count = count($overspeeds);
				}
				else
				{
					$overspeed_count = 0;
				}
				
				$odometer = getObjectOdometer($imei);
				$odometer = convDistanceUnits($odometer, 'km', $_SESSION["unit_distance"]);
				$odometer  = floor($odometer);
				
				$result .= '<tr>';
				
				if (in_array("object", $data_items))
				{
					$result .= '<td align="center">'.getObjectName($imei).'</td>';
				}
				
				if (in_array("route_start", $data_items))
				{
					$result .= '<td align="center">'.$data['route'][0][0].'</td>';
				}
				
				if (in_array("route_end", $data_items))
				{
					$result .= '<td align="center">'.$data['route'][count($data['route'])-1][0].'</td>';
				}
				
				if (in_array("route_length", $data_items))
				{
					$result .= '<td align="center">'.$data['route_length'].' '.$la["UNIT_DISTANCE"].'</td>';
					
					$total_route_length += $data['route_length'];
				}
				
				if (in_array("move_duration", $data_items))
				{
					$result .= '<td align="center">'.$data['drives_duration'].'</td>';
					
					$total_drives_duration += $data['drives_duration_time'];
				}
				
				if (in_array("stop_duration", $data_items))
				{
					$result .= '<td align="center">'.$data['stops_duration'].'</td>';
					
					$total_stops_duration += $data['stops_duration_time'];
				}
				
				if (in_array("top_speed", $data_items))
				{
					$result .= '<td align="center">'.$data['top_speed'].' '.$la["UNIT_SPEED"].'</td>';
				}
				
				if (in_array("avg_speed", $data_items))
				{
					$result .= '<td align="center">'.$data['avg_speed'].' '.$la["UNIT_SPEED"].'</td>';
				}
				
				if (in_array("overspeed_count", $data_items))
				{
					$result .= '<td align="center">'.$overspeed_count.'</td>';
					
					$total_overspeed_count += $overspeed_count;
				}
				
				if (in_array("fuel_consumption", $data_items))
				{
					$result .= '<td align="center">'.$data['fuel_consumption'].' '.$la["UNIT_CAPACITY"].'</td>';
					
					$total_fuel_consumption += $data['fuel_consumption'];
				}
				
				if (in_array("fuel_cost", $data_items))
				{
					$result .= '<td align="center">'.$data['fuel_cost'].' '.$_SESSION["currency"].'</td>';
					
					$total_fuel_cost += $data['fuel_cost'];
				}
				
				if (in_array("engine_work", $data_items))
				{
					$result .= '<td align="center">'.$data['engine_work'].'</td>';
					
					$total_engine_work += $data['engine_work_time'];
				}
				
				if (in_array("engine_idle", $data_items))
				{
					$result .= '<td align="center">'.$data['engine_idle'].'</td>';
					
					$total_engine_idle += $data['engine_idle_time'];
				}
				
				if (in_array("odometer", $data_items))
				{
					$result .= '<td align="center">'.$odometer.' '.$la["UNIT_DISTANCE"].'</td>';
					
					$total_odometer += $odometer;
				}
				
				if (in_array("engine_hours", $data_items))
				{
					$engine_hours = getObjectEngineHours($imei);
					
					$result .= '<td align="center">'.$engine_hours.' '.$la['UNIT_H'].'</td>';
					
					$total_engine_hours += $engine_hours;
				}
				
				if (in_array("driver", $data_items))
				{
					$params = $data['route'][count($data['route'])-1][6];
					$driver = getObjectDriver($user_id, $imei, $params);
					if ($driver['driver_name'] == ''){$driver['driver_name'] = $la['NA'];}
						
					$result .= '<td align="center">'.$driver['driver_name'].'</td>';
				}
				
				if (in_array("trailer", $data_items))
				{
					$params = $data['route'][count($data['route'])-1][6];
					$trailer = getObjectTrailer($user_id, $imei, $params);
					if ($trailer['trailer_name'] == ''){$trailer['trailer_name'] = $la['NA'];}
						
					$result .= '<td align="center">'.$trailer['trailer_name'].'</td>';
				}
				
				$result .= '</tr>';
			}
		}
		
		if (in_array("total", $data_items))
		{
			if (in_array("object", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
			
			if (in_array("route_start", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
			
			if (in_array("route_end", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
			
			if (in_array("route_length", $data_items))
			{
				$result .= '<td align="center">'.$total_route_length.' '.$la["UNIT_DISTANCE"].'</td>';
			}
			
			if (in_array("move_duration", $data_items))
			{
				$result .= '<td align="center">'.getTimeDetails($total_drives_duration).'</td>';
			}
			
			if (in_array("stop_duration", $data_items))
			{
				$result .= '<td align="center">'.getTimeDetails($total_stops_duration).'</td>';
			}
			
			if (in_array("top_speed", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
			
			if (in_array("avg_speed", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
			
			if (in_array("overspeed_count", $data_items))
			{
				$result .= '<td align="center">'.$total_overspeed_count.'</td>';
			}
			
			if (in_array("fuel_consumption", $data_items))
			{
				$result .= '<td align="center">'.$total_fuel_consumption.' '.$la["UNIT_CAPACITY"].'</td>';
			}
			
			if (in_array("fuel_cost", $data_items))
			{
				$result .= '<td align="center">'.$total_fuel_cost.' '.$_SESSION["currency"].'</td>';
			}
			
			if (in_array("engine_work", $data_items))
			{
				$result .= '<td align="center">'.getTimeDetails($total_engine_work).'</td>';
			}
			
			if (in_array("engine_idle", $data_items))
			{
				$result .= '<td align="center">'.getTimeDetails($total_engine_idle).'</td>';
			}
			
			if (in_array("odometer", $data_items))
			{
				$result .= '<td align="center">'.$total_odometer.' '.$la["UNIT_DISTANCE"].'</td>';
			}
			
			if (in_array("engine_hours", $data_items))
			{
				$result .= '<td align="center">'.$total_engine_hours.' '.$la["UNIT_H"].'</td>';
			}
			
			if (in_array("driver", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
			
			if (in_array("trailer", $data_items))
			{
				$result .= '<td align="center"></td>';
			}
		}

		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateObjectInfo($imeis, $data_items)
	{
		global $ms, $_SESSION, $la, $user_id;
		
		$result = '<table class="report" width="100%" ><tr align="center">';
				
		if (in_array("object", $data_items))
		{
			$result .= '<th>'.$la['OBJECT'].'</th>';
		}
		
		if (in_array("imei", $data_items))
		{
			$result .= '<th>'.$la['IMEI'].'</th>';
		}
		
		if (in_array("transport_model", $data_items))
		{
			$result .= '<th>'.$la['TRANSPORT_MODEL'].'</th>';
		}
		
		if (in_array("vin", $data_items))
		{
			$result .= '<th>'.$la['VIN'].'</th>';
		}
		
		if (in_array("plate_number", $data_items))
		{
			$result .= '<th>'.$la['PLATE_NUMBER'].'</th>';
		}
		
		if (in_array("odometer", $data_items))
		{
			$result .= '<th>'.$la['ODOMETER'].'</th>';
		}
		
		if (in_array("engine_hours", $data_items))
		{
			$result .= '<th>'.$la['ENGINE_HOURS'].'</th>';
		}
		
		if (in_array("driver", $data_items))
		{
			$result .= '<th>'.$la['DRIVER'].'</th>';
		}
		
		if (in_array("trailer", $data_items))
		{
			$result .= '<th>'.$la['TRAILER'].'</th>';
		}
		
		if (in_array("gps_device", $data_items))
		{
			$result .= '<th>'.$la['GPS_DEVICE'].'</th>';
		}
		
		if (in_array("sim_card_number", $data_items))
		{
			$result .= '<th>'.$la['SIM_CARD_NUMBER'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			// check if object is not removed from system and also if it is active
			if ((!checkObjectExistsUser($imei)) || (!checkObjectActive($imei)))
			{
				continue;
			}
			
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r,MYSQL_ASSOC);
			
			$odometer = getObjectOdometer($imei);
			$odometer = convDistanceUnits($odometer, 'km', $_SESSION["unit_distance"]);
			$odometer  = floor($odometer);
			
			$result .= '<tr>';
			
			if (in_array("object", $data_items))
			{
				$result .= '<td align="center">'.$row['name'].'</td>';
			}
			
			if (in_array("imei", $data_items))
			{
				$result .= '<td align="center">'.$row['imei'].'</td>';
			}
			
			if (in_array("transport_model", $data_items))
			{
				$result .= '<td align="center">'.$row['model'].'</td>';
			}
			
			if (in_array("vin", $data_items))
			{
				$result .= '<td align="center">'.$row['vin'].'</td>';
			}
			
			if (in_array("plate_number", $data_items))
			{
				$result .= '<td align="center">'.$row['plate_number'].'</td>';
			}
			
			if (in_array("odometer", $data_items))
			{
				$result .= '<td align="center">'.$odometer.' '.$la["UNIT_DISTANCE"].'</td>';
			}
			
			if (in_array("engine_hours", $data_items))
			{
				$result .= '<td align="center">'.getObjectEngineHours($imei).' '.$la['UNIT_H'].'</td>';
			}
			
			if (in_array("driver", $data_items))
			{
				$params = paramsToArray($row['params']);
				$driver = getObjectDriver($user_id, $imei, $params);
				if ($driver['driver_name'] == ''){$driver['driver_name'] = $la['NA'];}
				
				$result .= '<td align="center">'.$driver['driver_name'].'</td>';
			}
			
			if (in_array("trailer", $data_items))
			{
				$params = paramsToArray($row['params']);
				$trailer = getObjectTrailer($user_id, $imei, $params);
				if ($trailer['trailer_name'] == ''){$trailer['trailer_name'] = $la['NA'];}
				
				$result .= '<td align="center">'.$trailer['trailer_name'].'</td>';
			}
			
			if (in_array("gps_device", $data_items))
			{
				$result .= '<td align="center">'.$row['device'].'</td>';
			}
			
			if (in_array("sim_card_number", $data_items))
			{
				$result .= '<td align="center">'.$row['sim_number'].'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}

	function reportsGenerateDrivesAndStops($imei, $dtf, $dtt, $stop_duration, $show_addresses, $zones_addresses, $data_items) //DRIVES_AND_STOPS
	{
		global $_SESSION, $la, $user_id;
		
		$result = '';		
		$data = getRoute($imei, $dtf, $dtt, $stop_duration, true);
		
		if (count($data['route']) < 2)
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("status", $data_items))
		{
			$result .= '<th rowspan="2">'.$la['STATUS'].'</th>';
		}
		
		if (in_array("start", $data_items))
		{
			$result .= '<th rowspan="2">'.$la['START'].'</th>';
		}
		
		if (in_array("end", $data_items))
		{
			$result .= '<th rowspan="2">'.$la['END'].'</th>';
		}
		
		if (in_array("duration", $data_items))
		{
			$result .= '<th rowspan="2">'.$la['DURATION'].'</th>';
		}
		
		$result .= 	'<th colspan="4">'.$la['STOP_POSITION'].'</th>
				<th colspan="1">'.$la['ENGINE_IDLE'].'</th>
				</tr>
				<tr align="center">
					<th>'.$la['LENGTH'].'</th>
					<th>'.$la['TOP_SPEED'].'</th>
					<th>'.$la['AVG_SPEED'].'</th>
					<th>'.$la['FUEL_CONSUMPTION'].'</th>
					<th>'.$la['FUEL_COST'].'</th>
				</tr>';
			
		$dt_sort = array();
		for ($i=0; $i<count($data['stops']); ++$i)
		{
			$dt_sort[] = $data['stops'][$i][6];
		}
		for ($i=0; $i<count($data['drives']); ++$i)
		{
			$dt_sort[] = $data['drives'][$i][4];
		}
		sort($dt_sort);	
		
		for ($i=0; $i<count($dt_sort); ++$i)
		{			
			for ($j=0; $j<count($data['stops']); ++$j)
			{
				if ($data['stops'][$j][6] == $dt_sort[$i])
				{
					$lat = sprintf("%01.6f", $data['stops'][$j][2]);
					$lng = sprintf("%01.6f", $data['stops'][$j][3]);
					
					$result .= '<tr align="center">';
					
					if (in_array("status", $data_items))
					{
						$result .= '<td>'.$la['STOPPED'].'</td>';
					}
					
					if (in_array("start", $data_items))
					{
						$result .= '<td>'.$data['stops'][$j][6].'</td>';
					}
					
					if (in_array("end", $data_items))
					{
						$result .= '<td>'.$data['stops'][$j][7].'</td>';
					}
					
					if (in_array("duration", $data_items))
					{
						$result .= '<td>'.$data['stops'][$j][8].'</td>';
					}
					
					$result .= '<td colspan="4">'.reportsGetPossition($lat, $lng, $show_addresses, $zones_addresses).'</td>
							<td>'.$data['stops'][$j][9].'</td></tr>';
				}
			}
			for ($j=0; $j<count($data['drives']); ++$j)
			{
				if ($data['drives'][$j][4] == $dt_sort[$i])
				{					
					$result .= '<tr align="center">';
					
					if (in_array("status", $data_items))
					{
						$result .= '<td>'.$la['MOVING'].'</td>';
					}
					
					if (in_array("start", $data_items))
					{
						$result .= '<td>'.$data['drives'][$j][4].'</td>';
					}
					
					if (in_array("end", $data_items))
					{
						$result .= '<td>'.$data['drives'][$j][5].'</td>';
					}
					
					if (in_array("duration", $data_items))
					{
						$result .= '<td>'.$data['drives'][$j][6].'</td>';
					}
					
					$result .= 	'<td>'.$data['drives'][$j][7].' '.$la["UNIT_DISTANCE"].'</td>
							<td>'.$data['drives'][$j][8].' '.$la["UNIT_SPEED"].'</td>
							<td>'.$data['drives'][$j][9].' '.$la["UNIT_SPEED"].'</td>
							<td>'.$data['drives'][$j][10].' '.$la["UNIT_CAPACITY"].'</td>
							<td>'.$data['drives'][$j][11].' '.$_SESSION["currency"].'</td>
							</tr>';
				}
			}
		}
		$result .= '</table><br/>';
		
		$result .= '<table>';
		
		if (in_array("route_length", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['ROUTE_LENGTH'].':</strong></td>
						<td>'.$data['route_length'].' '.$la["UNIT_DISTANCE"].'</td>
					</tr>';
		}
		
		if (in_array("move_duration", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['MOVE_DURATION'].':</strong></td>
						<td>'.$data['drives_duration'].'</td>
					</tr>';
		}
		
		if (in_array("stop_duration", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['STOP_DURATION'].':</strong></td>
						<td>'.$data['stops_duration'].'</td>
					</tr>';
		}
		
		if (in_array("top_speed", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['TOP_SPEED'].':</strong></td>
						<td>'.$data['top_speed'].' '.$la["UNIT_SPEED"].'</td>
					</tr>';
		}
		
		if (in_array("avg_speed", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['AVG_SPEED'].':</strong></td>
						<td>'.$data['avg_speed'].' '.$la["UNIT_SPEED"].'</td>
					</tr>';
		}
		
		if (in_array("fuel_consumption", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['FUEL_CONSUMPTION'].':</strong></td>
						<td>'.$data['fuel_consumption'].' '.$la["UNIT_CAPACITY"].'</td>
					</tr>';
		}
		
		if (in_array("fuel_cost", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['FUEL_COST'].':</strong></td>
						<td>'.$data['fuel_cost'].' '.$_SESSION["currency"].'</td>
					</tr>';
		}
		
		if (in_array("engine_work", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['ENGINE_WORK'].':</strong></td>
						<td>'.$data['engine_work'].'</td>
					</tr>';
		}
		
		if (in_array("engine_idle", $data_items))
		{
			$result .= 	'<tr>
						<td><strong>'.$la['ENGINE_IDLE'].':</strong></td>
						<td>'.$data['engine_idle'].'</td>
					</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateTravelSheet($imei, $dtf, $dtt, $stop_duration, $show_addresses, $zones_addresses, $data_items) //TRAVEL_SHEET
	{
		global $_SESSION, $la, $user_id;
		
		$result = '';		
		$data = getRoute($imei, $dtf, $dtt, $stop_duration, true);
		
		if (count($data['drives']) < 1)
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("date", $data_items))
		{
			$result .= '<th>'.$la['DATE'].'</th>';
		}
		
		if (in_array("position_a", $data_items))
		{
			$result .= '<th>'.$la['POSITION_A'].'</th>';
		}
		
		if (in_array("position_b", $data_items))
		{
			$result .= '<th>'.$la['POSITION_B'].'</th>';
		}
		
		if (in_array("length", $data_items))
		{
			$result .= '<th>'.$la['LENGTH'].'</th>';
		}
		
		if (in_array("fuel_consumption", $data_items))
		{
			$result .= '<th>'.$la['FUEL_CONSUMPTION'].'</th>';
		}
		
		if (in_array("fuel_cost", $data_items))
		{
			$result .= '<th>'.$la['FUEL_COST'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($j=0; $j<count($data['drives']); ++$j)
		{			
			$route_id_a = $data['drives'][$j][0];
			$route_id_b = $data['drives'][$j][2];
			
			$lat1 = sprintf("%01.6f", $data['route'][$route_id_a][1]);
			$lng1 = sprintf("%01.6f", $data['route'][$route_id_a][2]);
			$lat2 = sprintf("%01.6f", $data['route'][$route_id_b][1]);
			$lng2 = sprintf("%01.6f", $data['route'][$route_id_b][2]);
			
			$date = substr($data['drives'][$j][4], 0, 10);
			
			// this prevents double geocoder calling
			if(!isset($position_a))
			{
				$position_a = reportsGetPossition($lat1, $lng1, $show_addresses, $zones_addresses);
			}
			else
			{
				$position_a = $position_b;
			}
			
			$position_b = reportsGetPossition($lat2, $lng2, $show_addresses, $zones_addresses);
			
			$route_length = $data['drives'][$j][7];			
			$fuel_consumption = $data['drives'][$j][10];			
			$fuel_cost = $data['drives'][$j][11];
			
			$result .= '<tr align="center">';
			
			if (in_array("date", $data_items))
			{
				$result .= '<td>'.$date.'</td>';
			}
			
			if (in_array("position_a", $data_items))
			{
				$result .= '<td>'.$position_a.'</td>';
			}
			
			if (in_array("position_b", $data_items))
			{
				$result .= '<td>'.$position_b.'</td>';
			}
			
			if (in_array("length", $data_items))
			{
				$result .= '<td>'.$route_length.' '.$_SESSION["unit_distance"].'</td>';
			}
			
			if (in_array("fuel_consumption", $data_items))
			{
				$result .= '<td>'.$fuel_consumption.' '.$la["UNIT_CAPACITY"].'</td>';
			}
			
			if (in_array("fuel_cost", $data_items))
			{
				$result .= '<td>'.$fuel_cost.' '.$_SESSION["currency"].'</td>';
			}
			
			$result .= '</tr>';
		}
			
		$result .= '</table><br/>';
		
		return $result;
	}
	
	function reportsGenerateOverspeed($imei, $dtf, $dtt, $speed_limit, $show_addresses, $zones_addresses, $data_items) //OVERSPEED
	{
		global $_SESSION, $la, $user_id;
		
		$accuracy = getObjectAccuracy($imei);
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		//$route = removeRouteFakeCoordinates($route, array());
		$overspeeds = getRouteOverspeeds($route, $speed_limit);
		
		if ((count($route) == 0) || (count($overspeeds) == 0))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("start", $data_items))
		{
			$result .= '<th>'.$la['START'].'</th>';
		}
		
		if (in_array("end", $data_items))
		{
			$result .= '<th>'.$la['END'].'</th>';
		}
		
		if (in_array("duration", $data_items))
		{
			$result .= '<th>'.$la['DURATION'].'</th>';
		}
		
		if (in_array("top_speed", $data_items))
		{
			$result .= '<th>'.$la['TOP_SPEED'].'</th>';
		}
		
		if (in_array("avg_speed", $data_items))
		{
			$result .= '<th>'.$la['AVG_SPEED'].'</th>';
		}
		
		if (in_array("overspeed_position", $data_items))
		{
			$result .= '<th>'.$la['OVERSPEED_POSITION'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($overspeeds); ++$i)
		{
			$result .= '<tr align="center">';
			
			if (in_array("start", $data_items))
			{
				$result .= '<td>'.$overspeeds[$i][0].'</td>';
			}
			
			if (in_array("end", $data_items))
			{
				$result .= '<td>'.$overspeeds[$i][1].'</td>';
			}
			
			if (in_array("duration", $data_items))
			{
				$result .= '<td>'.$overspeeds[$i][2].'</td>';
			}
			
			if (in_array("top_speed", $data_items))
			{
				$result .= '<td>'.$overspeeds[$i][3].' '.$la["UNIT_SPEED"].'</td>';
			}
			
			if (in_array("avg_speed", $data_items))
			{
				$result .= '<td>'.$overspeeds[$i][4].' '.$la["UNIT_SPEED"].'</td>';
			}
			
			if (in_array("overspeed_position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($overspeeds[$i][5], $overspeeds[$i][6], $show_addresses, $zones_addresses).'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateUnderspeed($imei, $dtf, $dtt, $speed_limit, $show_addresses, $zones_addresses, $data_items) //UNDERSPEED
	{
		global $_SESSION, $la, $user_id;
		
		$accuracy = getObjectAccuracy($imei);
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		//$route = removeRouteFakeCoordinates($route, array());
		$underpeeds = getRouteUnderspeeds($route, $speed_limit);
		
		if ((count($route) == 0) || (count($underpeeds) == 0))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("start", $data_items))
		{
			$result .= '<th>'.$la['START'].'</th>';
		}
		
		if (in_array("end", $data_items))
		{
			$result .= '<th>'.$la['END'].'</th>';
		}
		
		if (in_array("duration", $data_items))
		{
			$result .= '<th>'.$la['DURATION'].'</th>';
		}
		
		if (in_array("top_speed", $data_items))
		{
			$result .= '<th>'.$la['TOP_SPEED'].'</th>';
		}
		
		if (in_array("avg_speed", $data_items))
		{
			$result .= '<th>'.$la['AVG_SPEED'].'</th>';
		}
		
		if (in_array("underspeed_position", $data_items))
		{
			$result .= '<th>'.$la['UNDERSPEED_POSITION'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($underpeeds); ++$i)
		{
			$result .= '<tr align="center">';
			
			if (in_array("start", $data_items))
			{
				$result .= '<td>'.$underpeeds[$i][0].'</td>';
			}
			
			if (in_array("end", $data_items))
			{
				$result .= '<td>'.$underpeeds[$i][1].'</td>';
			}
			
			if (in_array("duration", $data_items))
			{
				$result .= '<td>'.$underpeeds[$i][2].'</td>';
			}
			
			if (in_array("top_speed", $data_items))
			{
				$result .= '<td>'.$underpeeds[$i][3].' '.$la["UNIT_SPEED"].'</td>';
			}
			
			if (in_array("avg_speed", $data_items))
			{
				$result .= '<td>'.$underpeeds[$i][4].' '.$la["UNIT_SPEED"].'</td>';
			}
			
			if (in_array("underspeed_position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($underpeeds[$i][5], $underpeeds[$i][6], $show_addresses, $zones_addresses).'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateZoneInOut($imei, $dtf, $dtt, $show_addresses, $zones_addresses, $zone_ids, $data_items) //ZONE_IN_OUT
	{
		global $ms, $_SESSION, $la, $user_id;
		
		$zone_ids = explode(",", $zone_ids);
		
		$accuracy = getObjectAccuracy($imei);
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		//$route = removeRouteFakeCoordinates($route, array());
		
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$zones = array();
		
		while($row=mysqli_fetch_array($r))
		{
			if(in_array($row['zone_id'], $zone_ids))
			{
				$zones[] = array($row['zone_id'],$row['zone_name'], $row['zone_vertices']);	
			}
		}
		
		if ((count($route) == 0) || (count($zones) == 0))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$in_zones = array();
		$in_zone = 0;
		for ($i=0; $i<count($route); ++$i)
		{
			$point_lat = $route[$i][1];
			$point_lng = $route[$i][2];
			
			for ($j=0; $j<count($zones); ++$j)
			{
				$zone_id = $zones[$j][0];
				$zone_name = $zones[$j][1];
				$zone_vertices = $zones[$j][2];
				
				$isPointInPolygon = isPointInPolygon($zone_vertices, $point_lat, $point_lng);
				
				if ($isPointInPolygon)
				{
					if ($in_zone == 0)
					{
						$in_zone_start = $route[$i][0];
						$in_zone_name = $zone_name;
						$in_zone_lat = $point_lat;
						$in_zone_lng = $point_lng;
						$in_zone = $zone_id;
					}
				}
				else
				{
					if ($in_zone == $zone_id)
					{
						$in_zone_end = $route[$i][0];
						$in_zone_duration = getTimeDifferenceDetails($in_zone_start, $in_zone_end);
						$in_zone = 0;
						
						$in_zones[] = array($in_zone_start,
									$in_zone_end,
									$in_zone_duration,
									$in_zone_name,
									$in_zone_lat,
									$in_zone_lng
									);
					}
				}
			}
		}
		
		// add last zone record if it did not leave
		if ($in_zone != 0)
		{
			$in_zones[] = array($in_zone_start,
						$la['NA'],
						$la['NA'],
						$in_zone_name,
						$in_zone_lat,
						$in_zone_lng
						);	
		}
		
		if (count($in_zones) == 0)
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("zone_in", $data_items))
		{
			$result .= '<th>'.$la['ZONE_IN'].'</th>';
		}
		
		if (in_array("zone_out", $data_items))
		{
			$result .= '<th>'.$la['ZONE_OUT'].'</th>';
		}
		
		if (in_array("duration", $data_items))
		{
			$result .= '<th>'.$la['DURATION'].'</th>';
		}
		
		if (in_array("zone_name", $data_items))
		{
			$result .= '<th>'.$la['ZONE_NAME'].'</th>';
		}
		
		if (in_array("zone_position", $data_items))
		{
			$result .= '<th>'.$la['ZONE_POSITION'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($in_zones); ++$i)
		{
			$result .= '<tr align="center">';
			
			if (in_array("zone_in", $data_items))
			{
				$result .= '<td>'.$in_zones[$i][0].'</td>';
			}
			
			if (in_array("zone_out", $data_items))
			{
				$result .= '<td>'.$in_zones[$i][1].'</td>';
			}
			
			if (in_array("duration", $data_items))
			{
				$result .= '<td>'.$in_zones[$i][2].'</td>';
			}
			
			if (in_array("zone_name", $data_items))
			{
				$result .= '<td>'.$in_zones[$i][3].'</td>';
			}
			
			if (in_array("zone_position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($in_zones[$i][4], $in_zones[$i][5], $show_addresses, $zones_addresses).'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateEvents($imei, $dtf, $dtt, $show_addresses, $zones_addresses, $data_items) //EVENTS
	{
		global $ms, $_SESSION, $la, $user_id;
		
		$q = "SELECT * FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."' AND dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if ($count == 0)
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("time", $data_items))
		{
			$result .= '<th>'.$la['TIME'].'</th>';
		}
		
		if (in_array("event", $data_items))
		{
			$result .= '<th>'.$la['EVENT'].'</th>';
		}
		
		if (in_array("event_position", $data_items))
		{
			$result .= '<th>'.$la['EVENT_POSITION'].'</th>';
		}
		
		$result .= '</tr>';	
		
		$total_events = array();
		
		while($event_data=mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$result .= '<tr align="center">';
			
			if (in_array("time", $data_items))
			{
				$result .= '<td>'.convUserTimezone($event_data['dt_tracker']).'</td>';
			}
			
			if (in_array("event", $data_items))
			{
				$result .= '<td>'.$event_data['event_desc'].'</td>';
			}
			
			if (in_array("event_position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($event_data['lat'], $event_data['lng'], $show_addresses, $zones_addresses).'</td>';
			}
			
			$result .= '</tr>';
			
			if (isset($total_events[$event_data['event_desc']]))
			{
				$total_events[$event_data['event_desc']]++;
			}
			else
			{
				$total_events[$event_data['event_desc']] = 1;
			}
		}
		
		$result .= '</table>';
		
		if (in_array("total", $data_items))
		{
			$result .= '<br/>';
			
			ksort($total_events);
			
			$result .= '<table>';
			foreach ($total_events as $key=>$value)
			{
				$result .= '<tr>
					<td><strong>'.$la['TOTAL'].' '.$key.':</strong></td>
					<td>'.$value.'</td>
				</tr>';
			}
			$result .= '</table>';
		}
		
		return $result;
	}
	
	function reportsGenerateService($imei, $data_items) //SERVICE
	{
		global $ms, $_SESSION, $la, $user_id;
		
		$result = '';
		
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$imei."' ORDER BY name asc";
		$r = mysqli_query($ms, $q);		
		$count = mysqli_num_rows($r);
		
		if ($count == 0)
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("service", $data_items))
		{
			$result .= '<th width="20%">'.$la['SERVICE'].'</th>';
		}
		
		if (in_array("last_service", $data_items))
		{
			$result .= 	'<th width="15%">'.$la['LAST_SERVICE'].' ('.$la["UNIT_DISTANCE"].')</th>
					<th width="15%">'.$la['LAST_SERVICE'].' (h)</th>
					<th width="15%">'.$la['LAST_SERVICE'].'</th>';
		}
		
		if (in_array("status", $data_items))
		{
			$result .= '<th width="35%">'.$la['STATUS'].'</th>';
		}
		
		$result .= '</tr>';
		
		// get real odometer and engine hours
                $odometer = getObjectOdometer($imei);
		$odometer = convDistanceUnits($odometer, 'km', $_SESSION["unit_distance"]);
                $odometer  = round($odometer);
		
		$engine_hours = getObjectEngineHours($imei);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC)) {
			$service_id = $row["service_id"];
			$name = $row['name'];
                        $odo_last = $la['NA'];
			$engh_last = $la['NA'];
			$days_last = $la['NA'];
			$status = '';
			
                        if ($row['odo'] == 'true')
                        {
				$row['odo_interval'] = convDistanceUnits($row['odo_interval'], 'km', $_SESSION["unit_distance"]);
				$row['odo_last'] = convDistanceUnits($row['odo_last'], 'km', $_SESSION["unit_distance"]);
				
				$row['odo_interval']  = round($row['odo_interval']);
				$row['odo_last']  = round($row['odo_last']);
				
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
				
				$odo_last = $row['odo_last'];
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
				
				$engh_last = $row['engh_last'];
				$status .= $str;
                        }
                        
                        if ($row['days'] == 'true')
                        {
				$days_diff = strtotime(gmdate("M d Y ")) - (strtotime($row['days_last']));
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
				
				$days_last = $row['days_last'];
				$status .= $str;
                        }
			
			if (in_array("service", $data_items))
			{
				$result .= '<tr><td>'.$name.'</td>';
			}
			
			if (in_array("last_service", $data_items))
			{
				$result .= '<td align="center">'.$odo_last.'</td>
					<td align="center">'.$engh_last.'</td>
					<td align="center">'.$days_last.'</td>';
			}
			
			if (in_array("status", $data_items))
			{
				$result .= '<td>'.$status.'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	//function reportsGenerateRag($imeis, $dtf, $dtt, $speed_limit, $data_items)
	//{
	//	
	//}
	
	function reportsGenerateRag($imeis, $dtf, $dtt, $speed_limit, $data_items)
	{
		global $ms, $_SESSION, $la, $user_id;
		
		$result = '<table class="report" width="100%" ><tr align="center">';
				
		$result .= '<th>'.$la['DRIVER'].'</th>';
		$result .= '<th>'.$la['OBJECT'].'</th>';
		$result .= '<th>'.$la['ROUTE_LENGTH'].'</th>';
		
		if (in_array("overspeed_score", $data_items))
		{
			$result .= '<th>'.$la['OVERSPEED_DURATION'].'</th>';
			$result .= '<th>'.$la['OVERSPEED_SCORE'].'</th>';
		}
		
		if (in_array("harsh_acceleration_score", $data_items))
		{
			$result .= '<th>'.$la['HARSH_ACCELERATION_COUNT'].'</th>';
			$result .= '<th>'.$la['HARSH_ACCELERATION_SCORE'].'</th>';
		}
		
		if (in_array("harsh_braking_score", $data_items))
		{
			$result .= '<th>'.$la['HARSH_BRAKING_COUNT'].'</th>';
			$result .= '<th>'.$la['HARSH_BRAKING_SCORE'].'</th>';
		}
		
		if (in_array("harsh_cornering_score", $data_items))
		{
			$result .= '<th>'.$la['HARSH_CORNERING_COUNT'].'</th>';
			$result .= '<th>'.$la['HARSH_CORNERING_SCORE'].'</th>';
		}
		
		$result .= '<th>'.$la['RAG'].'</th>';
		$result .= '</tr>';
		
		$rag = array();
		
		for ($i=0; $i<count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			// check if object is not removed from system and also if it is active
			if ((!checkObjectExistsUser($imei)) || (!checkObjectActive($imei)))
			{
				continue;
			}
			
			$accuracy = getObjectAccuracy($imei);
			$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
			
			if (count($route) == 0)
			{
				continue;
			}
			
			$haccel_count = 0;
			$hbrake_count = 0;
			$hcorn_count = 0;
			
			$q = "SELECT * FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'
			AND `type`='haccel' AND dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
			$r = mysqli_query($ms, $q);
			
			$haccel_count = mysqli_num_rows($r);
			
			$q = "SELECT * FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'
			AND `type`='hbrake' AND dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
			$r = mysqli_query($ms, $q);
			
			$hbrake_count = mysqli_num_rows($r);
			
			$q = "SELECT * FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'
			AND `type`='hcorn' AND dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
			$r = mysqli_query($ms, $q);
			
			$hcorn_count = mysqli_num_rows($r);
			
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r,MYSQL_ASSOC);
			$params = paramsToArray($row['params']);
			$driver = getObjectDriver($user_id, $imei, $params);
						
			if ($driver == false)
			{
				continue;
			}
			
			$route_length = getRouteLength($route, 0, count($route)-1);
			
			$overspeed_duration = 0;
			$overspeed = 0;
			
			for ($j=0; $j<count($route); ++$j)
			{
				$speed = $route[$j][5];
				
				if ($speed > $speed_limit)
				{	
					if($overspeed == 0)
					{
						$overspeed_start = $route[$j][0];
						$overspeed = 1;
					}
				}
				else
				{
					if ($overspeed == 1)
					{
						$overspeed_end = $route[$j][0];
						$overspeed_duration += strtotime($overspeed_end) - strtotime($overspeed_start);
						$overspeed = 0;
					}
				}
			}
			
			if ($route_length > 0 )
			{
				$overspeed_score = $overspeed_duration / 10 / $route_length * 100;
				$overspeed_score = sprintf('%0.2f', $overspeed_score);
				
				$haccel_score = $haccel_count / $route_length * 100;
				$haccel_score = sprintf('%0.2f', $haccel_score);
				
				$hbrake_score = $hbrake_count / $route_length * 100;
				$hbrake_score = sprintf('%0.2f', $hbrake_score);
				
				$hcorn_score = $hcorn_count / $route_length * 100;
				$hcorn_score = sprintf('%0.2f', $hcorn_score);	
			}
			else
			{
				$overspeed_score = 0;
				$haccel_score = 0;
				$hbrake_score = 0;
				$hcorn_score = 0;
			}
			
			$rag_score = 0;
			
			if (in_array("overspeed_score", $data_items))
			{
				$rag_score += $overspeed_score;
			}
			
			if (in_array("harsh_acceleration_score", $data_items))
			{
				$rag_score += $haccel_score;
			}
			
			if (in_array("harsh_braking_score", $data_items))
			{
				$rag_score += $hbrake_score;
			}
			
			if (in_array("harsh_cornering_score", $data_items))
			{
				$rag_score += $hcorn_score;
			}
			
			$rag_score = sprintf('%0.2f', $rag_score);
			
			$rag[] = array('driver_name' => $driver['driver_name'],
				       'object_name' => getObjectName($imei),
				       'route_length' => $route_length,
				       'overspeed_duration' => $overspeed_duration,
				       'overspeed_score' => $overspeed_score,
				       'haccel_count' => $haccel_count,
				       'haccel_score' => $haccel_score,
				       'hbrake_count' => $hbrake_count,
				       'hbrake_score' => $hbrake_score,
				       'hcorn_count' => $hcorn_count,
				       'hcorn_score' => $hcorn_score,
				       'rag_score' => $rag_score
			);
		}
		
		if (count($rag) == 0)
		{
			$result .= '<tr><td align="center" colspan="12">'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr>';
		}
		
		// list all drivers
		for ($i=0; $i<count($rag); ++$i)
		{
			$result .= '<tr>';
			
			$result .= '<td align="center">'.$rag[$i]['driver_name'].'</td>';
			$result .= '<td align="center">'.$rag[$i]['object_name'].'</td>';
			$result .= '<td align="center">'.$rag[$i]['route_length'].' '.$la['UNIT_DISTANCE'].'</td>';
			
			if (in_array("overspeed_score", $data_items))
			{
				$result .= '<td align="center">'.$rag[$i]['overspeed_duration'].' '.$la['UNIT_S'].'</td>';
				$result .= '<td align="center">'.$rag[$i]['overspeed_score'].'</td>';
			}
			
			if (in_array("harsh_acceleration_score", $data_items))
			{
				$result .= '<td align="center">'.$rag[$i]['haccel_count'].'</td>';
				$result .= '<td align="center">'.$rag[$i]['haccel_score'].'</td>';
			}
			
			if (in_array("harsh_braking_score", $data_items))
			{
				$result .= '<td align="center">'.$rag[$i]['hbrake_count'].'</td>';
				$result .= '<td align="center">'.$rag[$i]['hbrake_score'].'</td>';
			}
			
			if (in_array("harsh_cornering_score", $data_items))
			{
				$result .= '<td align="center">'.$rag[$i]['hcorn_count'].'</td>';
				$result .= '<td align="center">'.$rag[$i]['hcorn_score'].'</td>';
			}
			
			if ($rag[$i]['rag_score'] <= 2)
			{
				$rag_color = '#00FF00';
			}
			else if (($rag[$i]['rag_score'] > 2) && ($rag[$i]['rag_score'] <= 5))
			{
				$rag_color = '#FFFF00';
			}
			else if ($rag[$i]['rag_score'] > 5)
			{
				$rag_color = '#FF0000';
			}
			
			$result .= '<td align="center" bgcolor="'.$rag_color.'">'.$rag[$i]['rag_score'].'</td>';
			
			$result .= '</tr>';	
		}
		
		$result .= '</table>';
		
		return $result;
	}

	
	function reportsGenerateFuelFillings($imei, $dtf, $dtt, $show_addresses, $zones_addresses, $data_items) //FUEL_FILLINGS
	{
		global $_SESSION, $la, $user_id;
		
		$result = '';
		
		$accuracy = getObjectAccuracy($imei);
		$fuel_sensors = getSensorFromType($imei, 'fuel');
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		$ff = getRouteFuelFillings($route, $accuracy, $fuel_sensors);
		
		if ((count($route) == 0) || (count($ff['fillings']) == 0))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("time", $data_items))
		{
			$result .= '<th>'.$la['TIME'].'</th>';
		}
		
		if (in_array("position", $data_items))
		{
			$result .= '<th>'.$la['POSITION'].'</th>';
		}
		
		if (in_array("before", $data_items))
		{
			$result .= '<th>'.$la['BEFORE'].'</th>';
		}
		
		if (in_array("after", $data_items))
		{
			$result .= '<th>'.$la['AFTER'].'</th>';
		}
		
		if (in_array("filled", $data_items))
		{
			$result .= '<th>'.$la['FILLED'].'</th>';
		}
		
		if (in_array("sensor", $data_items))
		{
			$result .= '<th>'.$la['SENSOR'].'</th>';
		}
		
		if (in_array("driver", $data_items))
		{
			$result .= '<th>'.$la['DRIVER'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($ff['fillings']); ++$i)
		{
			$lat = $ff['fillings'][$i][1];
			$lng = $ff['fillings'][$i][2];
			
			$params = $ff['fillings'][$i][7];
			$driver = getObjectDriver($user_id, $imei, $params);
			if ($driver['driver_name'] == ''){$driver['driver_name'] = $la['NA'];}
			
			$result .= '<tr align="center">';
			
			if (in_array("time", $data_items))
			{
				$result .= '<td>'.$ff['fillings'][$i][0].'</td>';
			}
			
			if (in_array("position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($lat, $lng, $show_addresses, $zones_addresses).'</td>';
			}
			
			if (in_array("before", $data_items))
			{
				$result .= '<td>'.$ff['fillings'][$i][3].'</td>';
			}
			
			if (in_array("after", $data_items))
			{
				$result .= '<td>'.$ff['fillings'][$i][4].'</td>';
			}
			
			if (in_array("filled", $data_items))
			{
				$result .= '<td>'.$ff['fillings'][$i][5].'</td>';
			}
			
			if (in_array("sensor", $data_items))
			{
				$result .= '<td>'.$ff['fillings'][$i][6].'</td>';
			}
			
			if (in_array("driver", $data_items))
			{
				$result .= '<td>'.$driver['driver_name'].'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		if (in_array("total_filled", $data_items))
		{
			$result .= '<br/>';
			$result .= '<table>';
			$result .= '<tr><td><strong>'.$la['TOTAL_FILLED'].':</strong></td><td>'.$ff['total_filled'].'</td></tr>';
			$result .= '</table>';
		}
		
		return $result;
	}
	
	function reportsGenerateFuelThefts($imei, $dtf, $dtt, $show_addresses, $zones_addresses, $data_items) //FUEL_THEFTS
	{
		global $_SESSION, $la, $user_id;
		
		$result = '';
		
		$accuracy = getObjectAccuracy($imei);
		$fuel_sensors = getSensorFromType($imei, 'fuel');
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		$ft = getRouteFuelThefts($route, $accuracy, $fuel_sensors);
		
		if ((count($route) == 0) || (count($ft['thefts']) == 0))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("time", $data_items))
		{
			$result .= '<th>'.$la['TIME'].'</th>';
		}
		
		if (in_array("position", $data_items))
		{
			$result .= '<th>'.$la['POSITION'].'</th>';
		}
		
		if (in_array("before", $data_items))
		{
			$result .= '<th>'.$la['BEFORE'].'</th>';
		}
		
		if (in_array("after", $data_items))
		{
			$result .= '<th>'.$la['AFTER'].'</th>';
		}
		
		if (in_array("stolen", $data_items))
		{
			$result .= '<th>'.$la['STOLEN'].'</th>';
		}
		
		if (in_array("sensor", $data_items))
		{
			$result .= '<th>'.$la['SENSOR'].'</th>';
		}
		
		if (in_array("driver", $data_items))
		{
			$result .= '<th>'.$la['DRIVER'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($ft['thefts']); ++$i)
		{
			$lat = $ft['thefts'][$i][1];
			$lng = $ft['thefts'][$i][2];
			
			$params = $ft['thefts'][$i][7];
			$driver = getObjectDriver($user_id, $imei, $params);
			if ($driver['driver_name'] == ''){$driver['driver_name'] = $la['NA'];}
			
			$result .= '<tr align="center">';
			
			if (in_array("time", $data_items))
			{
				$result .= '<td>'.$ft['thefts'][$i][0].'</td>';
			}
			
			if (in_array("position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($lat, $lng, $show_addresses, $zones_addresses).'</td>';
			}
			
			if (in_array("before", $data_items))
			{
				$result .= '<td>'.$ft['thefts'][$i][3].'</td>';
			}
			
			if (in_array("after", $data_items))
			{
				$result .= '<td>'.$ft['thefts'][$i][4].'</td>';
			}
			
			if (in_array("stolen", $data_items))
			{
				$result .= '<td>'.$ft['thefts'][$i][5].'</td>';
			}
			
			if (in_array("sensor", $data_items))
			{
				$result .= '<td>'.$ft['thefts'][$i][6].'</td>';
			}
			
			if (in_array("driver", $data_items))
			{
				$result .= '<td>'.$driver['driver_name'].'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		if (in_array("total_stolen", $data_items))
		{
			$result .= '<br/>';
			$result .= '<table>';
			$result .= '<tr><td><strong>'.$la['TOTAL_STOLEN'].':</strong></td><td>'.$ft['total_stolen'].'</td></tr>';
			$result .= '</table>';
		}
		
		return $result;
	}
	
	function reportsGenerateLogicSensorInfo($imei, $dtf, $dtt, $sensors, $show_addresses, $zones_addresses, $data_items) //LOGIC_SENSOR_INFO
	{
		global $_SESSION, $gsValues, $la, $user_id;
		
		$accuracy = getObjectAccuracy($imei);		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);		
		$lsi = getRouteLogicSensorInfo($route, $accuracy, $sensors);
		
		if ((count($route) == 0) || (count($lsi) == 0) || ($sensors == false))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		$result = '<table class="report" width="100%"><tr align="center">';
		
		if (in_array("sensor", $data_items))
		{
			$result .= '<th>'.$la['SENSOR'].'</th>';
		}
		
		if (in_array("activation_time", $data_items))
		{
			$result .= '<th>'.$la['ACTIVATION_TIME'].'</th>';
		}
		
		if (in_array("deactivation_time", $data_items))
		{
			$result .= '<th>'.$la['DEACTIVATION_TIME'].'</th>';
		}
		
		if (in_array("duration", $data_items))
		{
			$result .= '<th>'.$la['DURATION'].'</th>';
		}
		
		if (in_array("activation_position", $data_items))
		{
			$result .= '<th>'.$la['ACTIVATION_POSITION'].'</th>';
		}
		
		if (in_array("deactivation_position", $data_items))
		{
			$result .= '<th>'.$la['DEACTIVATION_POSITION'].'</th>';
		}
		
		$result .= '</tr>';
		
		for ($i=0; $i<count($lsi); ++$i)
		{
			$sensor_name = $lsi[$i][0];
			$lsi_activation_time = $lsi[$i][1];
			$lsi_deactivation_time = $lsi[$i][2];
			$lsi_duration = $lsi[$i][3];
			$lsi_activation_lat = $lsi[$i][4];
			$lsi_activation_lng = $lsi[$i][5];
			$lsi_deactivation_lat = $lsi[$i][6];
			$lsi_deactivation_lng = $lsi[$i][7];
			
			$result .= '<tr align="center">';
			
			if (in_array("sensor", $data_items))
			{
				$result .= '<td>'.$sensor_name.'</td>';
			}
			
			if (in_array("activation_time", $data_items))
			{
				$result .= '<td>'.$lsi_activation_time.'</td>';
			}
			
			if (in_array("deactivation_time", $data_items))
			{
				$result .= '<td>'.$lsi_deactivation_time.'</td>';
			}
			
			if (in_array("duration", $data_items))
			{
				$result .= '<td>'.$lsi_duration.'</td>';
			}
			
			if (in_array("activation_position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($lsi_activation_lat, $lsi_activation_lng, $show_addresses, $zones_addresses).'</td>';
			}
			
			if (in_array("deactivation_position", $data_items))
			{
				$result .= '<td>'.reportsGetPossition($lsi_deactivation_lat, $lsi_deactivation_lng, $show_addresses, $zones_addresses).'</td>';
			}
			
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		
		return $result;
	}
	
	function reportsGenerateSensorGraph($imei, $dtf, $dtt, $sensors) //SENSOR GRAPH
	{
		global $_SESSION, $gsValues, $la, $user_id;
		
		$result = '';
		
		$accuracy = getObjectAccuracy($imei);
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		
		if ((count($route) == 0) || ($sensors == false))
		{
			return '<table><tr><td>'.$la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST'].'</td></tr></table>';
		}
		
		// loop per sensors
		for ($i=0; $i<count($sensors); ++$i)
		{
			$graph = array();
			$graph['data'] = array();
			$graph['data_index'] = array();
			
			// prepare graph plot id
			$graph_plot_id = $imei.'_'.$i;
			
			// prepare data
			$sensor = $sensors[$i];
			
			for ($j=0; $j<count($route); ++$j)
			{				
				$dt_tracker = $route[$j][0];
				$dt_tracker_timestamp = strtotime($dt_tracker) * 1000;
				
				$data = getSensorValue($route[$j][6], $sensor);
				
				$graph['data'][] = array($dt_tracker_timestamp, $data['value']);
				$graph['data_index'][$dt_tracker_timestamp] = $j;
			}
			
			// set units
			if ($sensor['type'] == 'odo')
			{
				$graph['units'] = $la['UNIT_DISTANCE'];
				$graph['result_type'] = $sensor['result_type'];
			}
			else if ($sensor['type'] == 'engh')
			{
				$graph['units'] = $la['UNIT_H'];
				$graph['result_type'] = $sensor['result_type'];
			}
			else
			{
				$graph['units'] = $sensor['units'];
				$graph['result_type'] = $sensor['result_type'];
			}
			
			$result .= 	'<script type="text/javascript">
						$(document).ready(function () {
							var graph = '.json_encode($graph).';
							graphInit("'.$graph_plot_id.'", graph);
						})
					</script>';
					
					
			$result .= 	'<div style="position:relative; height: 180px;">
						<div style="float: left;">
							<b>'.$la['SENSOR'].':</b> '.$sensor['name'].'
						</div>
						
						<div class="graph-controls" style="float: right">
							<a href="#" onclick="graphPanLeft(\''.$graph_plot_id.'\');" title="'.$la['PAN_LEFT'].'">
							<div class="graph_button">
							<img src="'.$gsValues['URL_ROOT'].'/theme/images/arrow_left.gif" border="0"/>
							</div>
							</a>
							
							<a href="#" onclick="graphPanRight(\''.$graph_plot_id.'\');" title="'.$la['PAN_RIGHT'].'">
							<div class="graph_button">
							<img src="'.$gsValues['URL_ROOT'].'/theme/images/arrow_right.gif" border="0"/>
							</div>
							</a>
							  
							<a href="#" onclick="graphZoomIn(\''.$graph_plot_id.'\');" title="'.$la['ZOOM_IN'].'">
							<div class="graph_button">
							<img src="'.$gsValues['URL_ROOT'].'/theme/images/zoom_in.png" border="0"/>
							</div>
							</a>
							
							<a href="#" onclick="graphZoomOut(\''.$graph_plot_id.'\');" title="'.$la['ZOOM_OUT'].'">
							<div class="graph_button">
							<img src="'.$gsValues['URL_ROOT'].'/theme/images/zoom_out.png" border="0"/>
							</div>
							</a>
						</div>
						
						<div id="graph_label_'.$graph_plot_id.'" class="graph_label" style="float: right"></div>
						
						<div id="graph_plot_'.$graph_plot_id.'" style="top:22px; height: 150px; width:100%;"></div>
					</div>';
		}
		
		return $result;
	}
	
	function reportsGetPossition($lat, $lng, $show_addresses, $zones_addresses)
	{
		global $ms, $user_id, $zones_addr, $zones_addr_loaded;
		
		$lat = sprintf('%0.6f', $lat);
		$lng = sprintf('%0.6f', $lng);
		
		$position = '<a href="http://maps.google.com/maps?q='.$lat.','.$lng.'&t=m" target="_blank">'.$lat.' &deg;, '.$lng.' &deg;</a>';
		
		if ($zones_addresses == 'true')
		{
			if ($zones_addr_loaded == false)
			{
				$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."'";
				$r = mysqli_query($ms, $q);
				
				while($row=mysqli_fetch_array($r))
				{
					$zones_addr[] = array($row['zone_id'],$row['zone_name'], $row['zone_vertices']);	
				}
				
				$zones_addr_loaded = true;
			}
			
			for ($j=0; $j<count($zones_addr); ++$j)
			{
				$zone_name = $zones_addr[$j][1];
				$zone_vertices = $zones_addr[$j][2];
				
				$isPointInPolygon = isPointInPolygon($zone_vertices, $lat, $lng);
				
				if ($isPointInPolygon)
				{
					$position .= ' - '.$zone_name;
					
					return $position;
				}
			}
		}
		
		if ($show_addresses == 'true')
		{
			usleep(150000);
			$position .= ' - '.geocoderGetAddress($lat, $lng);
		}
		
		return $position;
	}
	
	function reportsAddReportHeader($imei, $dtf = false, $dtt = false)
	{
		global $la, $user_id;
		
		$result = '<table>';
		
		if ($imei != "")
		{
			$result .= '<tr>
					<td><strong>'.$la['OBJECT'].':</strong></td>
					<td>'.getObjectName($imei).'</td>
					</tr>';
		}
		
		if (($dtf != false) && ($dtt != false))
		{
			$result .= '<tr>
					<td><strong>'.$la['PERIOD'].':</strong></td>
					<td>'.$dtf.' - '.$dtt.'</td>
					</tr>';
		}
		
		$result .= '</table><br/>';
		
		return $result;
	}
	
	function reportsAddHeaderStart($format)
	{
		global $ms, $gsValues;
		
		$result = '';
		
		if (($format == 'html') || ($format == 'pdf'))
		{
			$result = 	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>'.$gsValues['NAME'].' '.$gsValues['VERSION'].'</title>';
		}
		else if ($format == 'xls')
		{
			$result = 	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
					<title></title>';
		}
		
		return $result;
	}
	
	function reportsAddHeaderEnd()
	{
		$result = '</head><body>';
		
		return $result;
	}
	
	function reportsAddStyle($format)
	{
		$result = "<style type='text/css'>";
		
		if ($format == 'html')
		{
		$result .= "@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700&subset=latin,greek,greek-ext,cyrillic,cyrillic-ext,latin-ext,vietnamese);
				
				html, body {
					text-align: left; 
					margin: 10px;
					padding: 0px;
					font-size: 11px;
					font-family: 'open sans';
					color: #444444;
				}";
		}
		else if ($format == 'pdf')
		{
		$result .= "	html, body {
					text-align: left; 
					margin: 10px;
					padding: 0px;
					font-size: 11px;
					font-family: 'DejaVu Sans';
					color: #444444;
				}";
		}
		else if ($format == 'xls')
		{
		$result .= "	html, body {
					text-align: left; 
					margin: 10px;
					padding: 0px;
					font-size: 11px;
					color: #444444;
				}";
		}
		
		$result .= "h3 { 
					font-size: 13px;
					color: #444444;
					font-weight: 600;
				}
				
				hr {
					border-color:#cccccc;
					border-style:solid none none;
					border-width:1px 0 0;
					height:1px;
					margin-left:1px;
					margin-right:1px;
				}
				
				b, strong{ font-weight: 600; }
				
				.graph-controls { margin: 0 0 0 15px; }
				.graph-controls a {
					display: inline-block;
					-webkit-transition: all 0.3s ease;
					-moz-transition: all 0.3s ease;
					-ms-transition: all 0.3s ease;
					-o-transition: all 0.3s ease;
					transition: all 0.3s ease;
				}
				.graph-controls a:hover { opacity: 0.6; }
				
				a:link,
				a:visited {
					text-decoration: none;
					color: #004c8c;
				}
				a:active { color: #004c8c; }
				a:hover { text-decoration: underline; color: #004c8c; }
				
				caption,
				th,
				td { vertical-align: middle; }
				
				table.report {
					color:#333333;
					border: 1px solid #eeeeee;
					border-collapse: collapse;
				}
				
				table.report th {
					font-weight: 600;
					padding: 2px;
					border: 1px solid #eeeeee;
					background-color: #eeeeee;
				}
				
				table.report td {
					padding: 2px;
					border: 1px solid #eeeeee;
				}
				
				table.report tr:hover { background-color: #F6F6F6; }
				
				td { mso-number-format:'\@';/*force text*/ }
				
			</style>";
			
		return $result;
	}
	
	function reportsAddJS($type)
	{
		global $ms, $gsValues;
		
		$result = '';
		
		if (($type == 'acc_graph') ||($type == 'fuellevel_graph') || ($type == 'temperature_graph') || ($type == 'sensor_graph'))
		{
			$result .= '<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery-2.1.4.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery-migrate-1.2.1.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery.flot.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery.flot.crosshair.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery.flot.navigate.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery.flot.selection.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery.flot.time.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/jquery.flot.resize.min.js"></script>
				<script type="text/javascript" src="'.$gsValues['URL_ROOT'].'/js/gs.common.js"></script>
					
				<script type="text/javascript">
					var graphPlot = new Array();
					
					function graphInit(id, graph)
					{
						if (!graph)
						{
							var data = []; // if no data, just create array for empty graph
							var units = "";
							var steps_flag = false;
							var points_flag = false;
						} 
						else
						{
							var data = graph["data"];
							var units = graph["units"];
							
							if (graph["result_type"] == "logic")
							{
								var steps_flag = true;
								var points_flag = false;
							}
							else
							{
								var steps_flag = false;
								var points_flag = false;
							}
						}
						
						var minzoomRange = 30000;//	min zoom in is within 1 minute range (1*60*1000 = 60000)
						var maxzoomRange = 30 * 86400000;//	max zoom out is 5 times greater then chosen period (default is equal to 30 days 30 * 24*60*60*1000 = 86400000 )
						
						var options = {
							xaxis: {
								mode: "time", 
								zoomRange: [minzoomRange, maxzoomRange]
								},
							yaxis: {
								//min: 0, 
								tickFormatter: function (v) {
										var result = "";
										if (graph)
										{
											result = Math.round(v * 100)/100  + " " + units;
										}
										return result;
									}, 
								zoomRange: [0, 0], 
								panRange: false
								},
							selection: {mode: "x"},
							crosshair: {mode: "x"},
							lines: {show: true, lineWidth: 1, fill: true, fillColor: "rgba(0, 76, 140, 0.3)", steps: steps_flag},
							series: {lines: {show: true} , points: { show: points_flag, radius: 1 }},
							colors: ["#004c8c"],
							grid: {hoverable: true, autoHighlight: true, clickable: true},
							zoom: {
								//interactive: true,
								animate: true,
								trigger: "dblclick", // or "click" for single click
								amount: 3         // 2 = 200% (zoom in), 0.5 = 50% (zoom out)
							},
							pan: {interactive: false, animate: true}
						};
						
						graphPlot[id] = $.plot($("#graph_plot_"+id), [data], options);
					
						$("#graph_plot_"+id).unbind("plothover");
						$("#graph_plot_"+id).bind("plothover", function (event, pos, item) {
							if (item)
							{
								var dt_tracker = getDatetimeFromTimestamp(item.datapoint[0]);
								
								var value = item.datapoint[1];
								document.getElementById("graph_label_"+id).innerHTML = "<strong>"+value + " " + units + "</strong> - " + dt_tracker;			
							}
						});
						
						$("#graph_plot_"+id).unbind("plotselected");
						$("#graph_plot_"+id).bind("plotselected", function (event, ranges) {
							graphPlot[id] = $.plot($("#graph_plot_"+id), 
							[data],
							$.extend(true, {}, options, {
								xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
							}));
							
							// dont fire event on the overview to prevent eternal loop
							overview.setSelection(ranges, true);
						});
					}
					
					function graphPanLeft(id)
					{
						graphPlot[id].pan({left: -100})
					}
					
					function graphPanRight(id)
					{
						graphPlot[id].pan({left: +100})
					}
					
					function graphZoomIn(id)
					{
						graphPlot[id].zoom();
					}
					
					function graphZoomOut(id)
					{
						graphPlot[id].zoomOut();
					}
				</script>';
		}
		
		return $result;
	}
?>