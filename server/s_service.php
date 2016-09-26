<?
	set_time_limit(0);
	
	ob_start();
	
	include ('s_init.php');
	include ('s_events.php');
	include ('../func/fn_common.php');
	include ('../func/fn_cleanup.php');
	include ('../tools/gc_func.php');
	
	// opperations which do not require key verification
	if (@$_GET["op"] == "sms_gateway_app")
  	{
		if (!isset($_GET["identifier"])) { die; }
		
		if ($_GET["identifier"] == '') { die; }
		
		$format = strtolower(@$_GET["format"]);
		
		$q = "SELECT * FROM `gs_sms_gateway_app` WHERE `identifier`='".$_GET["identifier"]."' ORDER BY `dt_server` ASC";
		$r = mysqli_query($ms, $q);
		
		if($format == 'json')
		{
			$result = array();
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result[] = array($row['dt_server'], $row['number'], $row['message']);
			}
			
			echo json_encode($result);
		}
		else
		{
			$result = '';
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result.= $row['dt_server'].chr(30).$row['number'].chr(30).$row['message'].chr(29);
			}
			
			echo $result;
		}
		
		$q2 = "DELETE FROM `gs_sms_gateway_app` WHERE `identifier`='".$_GET['identifier']."'";
		$r2 = mysqli_query($ms, $q2);
		
		die;
	}
	
	if (@$_GET["op"] == "chat_new_messages")
  	{
		$imei = $_GET["imei"];
		
		// get unread messages number
		$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' AND `side`='S' AND `status`=0";
		$r = mysqli_query($ms, $q);
		$msg_num = mysqli_num_rows($r);
		
		// set messages to delivered
		$q = "UPDATE `gs_object_chat` SET `status`=1 WHERE `imei`='".$imei."' AND `side`='S' AND `status`=0";
		$r = mysqli_query($ms, $q);
		
		echo $msg_num;
		die;
	}	
	
	if ((@$_GET["op"] == "object_exists_system") || (@$_GET["op"] == "check_object_exists_system"))
  	{
		$imei = $_GET["imei"];
		echo checkObjectExistsSystem($imei);
		die;
	}	
	
	if (@$_GET["op"] == "cmd_exec_imei_get")
  	{
		$format = strtolower(@$_GET["format"]);
		
		$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `status`='0' AND `imei`='".$_GET["imei"]."'";
		$r = mysqli_query($ms, $q);
		
		if($format == 'json')
		{
			$result = array();
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result[] = array($row['cmd_id'], $row['cmd']);
				
				$q2 = "UPDATE `gs_object_cmd_exec` SET `status`='1' WHERE `cmd_id`='".$row["cmd_id"]."'";
				$r2 = mysqli_query($ms, $q2);
			}
			
			echo json_encode($result);
		}
		else
		{
			$result = '';
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result.= $row['cmd_id'].chr(30).$row['cmd'].chr(29);
				
				$q2 = "UPDATE `gs_object_cmd_exec` SET `status`='1' WHERE `cmd_id`='".$row["cmd_id"]."'";
				$r2 = mysqli_query($ms, $q2);
			}
			
			echo $result;
		}
		
		die;
	}
	
	if (@$_GET["op"] == "cmd_exec_get")
  	{
		$format = strtolower(@$_GET["format"]);
		
		$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `status`='0' ORDER BY `cmd_id` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = '';
		
		if($format == 'json')
		{
			$result = array();
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result[] = array($row['cmd_id'], $row['imei'], $row['type'], $row['cmd']);
			}
			
			echo json_encode($result);
		}
		else
		{
			$result = '';
			
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result.= $row['cmd_id'].chr(30).$row['imei'].chr(30).$row['type'].chr(30).$row['cmd'].chr(29);
			}
			
			echo $result;
		}
		
		die;
	}
	
	if (@$_GET["op"] == "cmd_exec_set")
  	{
		if (isset($_GET["re_hex"]))
		{
			$q = "UPDATE `gs_object_cmd_exec` SET `status`='".$_GET["status"]."', `re_hex`='".$_GET["re_hex"]."' WHERE `cmd_id`='".$_GET["cmd_id"]."'";
		}
		else
		{
			$q = "UPDATE `gs_object_cmd_exec` SET `status`='".$_GET["status"]."' WHERE `cmd_id`='".$_GET["cmd_id"]."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo "OK";
		die;
	}
	// opperations which do not require key verification
	
	if ($gsValues['HW_KEY'] != @$_GET["key"])
	{
		echo 'Incorrect hardware key.';
		die;
	}
	else
	{
		echo "OK";
	}
	
	header("Connection: close");
	header("Content-length: " . (string)ob_get_length());
	ob_end_flush();
	
	if (@$_GET["op"] == "clear_demo_history")
	{
		$q = "DELETE FROM `gs_object_data_".$_GET['imei']."`";
  		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `imei`='".$_GET['imei']."'";
  		$r = mysqli_query($ms, $q);
	}
	
	if (@$_GET["op"] == "service24h")
	{
		serviceDbBackup();
	}
	
	if (@$_GET["op"] == "service12h")
	{
		serviceServerCleanup();
	}
	
	if (@$_GET["op"] == "service1h")
	{
		serviceCheckAccountDateLimit();
		serviceCheckObjectDateLimit();
		serviceClearVarious();
		serviceClearHistory();
	}
	
	if (@$_GET["op"] == "service30min")
	{
		if ($gsValues['REPORTS_SCHEDULE'] == 'true')
		{
			serviceSendReportDaily();
			serviceSendReportWeekly();
		}
	}
	
	if (@$_GET["op"] == "service5min")
	{
		serviceEventConnection();
		serviceEventGPS();
		serviceEventService();
	}
	
	if (@$_GET["op"] == "service1min")
	{
		serviceEventStoppedMovingIdle();
	}
	
	// service 24h
	function serviceDbBackup()
	{
		global $ms, $gsValues;
		
		$email = $gsValues['DB_BACKUP_EMAIL'];
		
		if ($email == '')
		{
		    die;
		}
		
		//get all of the tables
		$tables = array();
		$result = mysqli_query($ms, 'SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
		
		$return = '';
		
		//cycle through
		foreach($tables as $table)
		{
			$row2 = mysqli_fetch_row(mysqli_query($ms, 'SHOW CREATE TABLE '.$table));
			$return.= $row2[1].";\n";
			
			if ((stristr($table, 'gs_object_data') == false) &&
				(stristr($table, 'gs_object_cmd_exec') == false) &&
				(stristr($table, 'gs_user_events_data') == false) &&
				(stristr($table, 'gs_user_events_status') == false) &&
				(stristr($table, 'gs_geocoder_cache') == false) &&
				(stristr($table, 'gs_object_img') == false) &&
				(stristr($table, 'gs_object_chat') == false) &&
				(stristr($table, 'gs_sms_gateway_app') == false))
			{
				$return.="\n";
				
				$result = mysqli_query($ms, 'SELECT * FROM '.$table);
				$num_fields = mysqli_num_fields($result);
				
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = mysqli_fetch_row($result))
					{
						$return.= 'INSERT INTO '.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
							if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
							if ($j<($num_fields-1)) { $return.= ','; }
						}
						$return.= ");\n";
					}
				}
			}
			$return.="\n";
		}
		
		//save file
		$file = 'db_backup.sql';
		
		//$handle = fopen($file,'w+');
		//fwrite($handle,$return);
		//fclose($handle);
		
		//send file via email
		$subject = 'Database backup';
		
		$message = "Hello,\r\n\r\n";
		$message .= "This is database backup, please do not reply to this e-mail.\r\n";
		
		sendEmail($email, $subject, $message, false, $file, $return);
	}
	
	// service 12h
	function serviceServerCleanup()
	{
		global $ms, $gsValues;
		
		if ($gsValues['SERVER_CLEANUP_USERS_AE'] == "true")
		{
			$days = $gsValues['SERVER_CLEANUP_USERS_DAYS'];
			$result = serverCleanupUsers($days);
		}
		
		if ($gsValues['SERVER_CLEANUP_OBJECTS_NOT_ACTIVATED_AE'] == "true")
		{
			$days = $gsValues['SERVER_CLEANUP_OBJECTS_NOT_ACTIVATED_DAYS'];
			$result = serverCleanupObjectsNotActivated($days);
		}
		
		if ($gsValues['SERVER_CLEANUP_OBJECTS_NOT_USED_AE'] == "true")
		{
			$result = serverCleanupObjectsNotUsed();
		}
		
		if ($gsValues['SERVER_CLEANUP_DB_JUNK_AE'] == "true")
		{
			$result = serverCleanupDbJunk();
		}
	}
	
	// service 1h
	function serviceCheckAccountDateLimit()
  	{
		global $ms, $gsValues, $la;
		
		// deactivate expired accounts
		$q = "UPDATE gs_users SET `active`='false' WHERE account_expire ='true' AND account_expire_dt <= UTC_DATE()";
		$r = mysqli_query($ms, $q);
		
		// remind about object expiry
		if ($gsValues['NOTIFY_ACCOUNT_EXPIRE'] == 'true')
		{
			$q = "SELECT * FROM `gs_users`";
			$r = mysqli_query($ms, $q); 
			
			while ($ud = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$user_id = $ud["id"];
				$account_expire = $ud["account_expire"];
				$account_expire_dt = $ud["account_expire_dt"];
				$email = $ud["email"];
				$notify_account_expire = $ud['notify_account_expire'];
				
				if ($account_expire == 'true')
				{
					$notify = false;
					
					$diff = strtotime($account_expire_dt) - strtotime(gmdate("Y-m-d"));
					$days = $diff / 86400;
					
					if ($days <= $gsValues['NOTIFY_ACCOUNT_EXPIRE_PERIOD'])
					{
						$notify = true;
					}
					
					if ($notify == true)
					{
						if ($notify_account_expire != 'true')
						{
							$template = getDefaultTemplate('expiring_account', $ud["language"]);
							
							$subject = $template['subject'];
							$message = $template['message'];
							
							$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
							$subject = str_replace("%URL_SHOP%", $gsValues['URL_SHOP'], $subject);
							
							$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
							$message = str_replace("%URL_SHOP%", $gsValues['URL_SHOP'], $message);
							
							if (sendEmail($email, $subject, $message))
							{					
								$q4 = "UPDATE gs_users SET `notify_account_expire`='true' WHERE `id`='".$user_id."'";
								$r4 = mysqli_query($ms, $q4);
							}	
						}
					}
					else
					{
						$q4 = "UPDATE gs_users SET `notify_account_expire`='false' WHERE `id`='".$user_id."'";
						$r4 = mysqli_query($ms, $q4);
					}	
				}
			}
		}
	}
	
	function serviceCheckObjectDateLimit()
  	{
		global $ms, $gsValues, $la;
		
		// deactivate expired objects
		$q = "UPDATE gs_objects SET `active`='false' WHERE active_dt <= UTC_DATE()";
		$r = mysqli_query($ms, $q);
		
		// remind about object expiry
		if ($gsValues['NOTIFY_OBJ_EXPIRE'] == 'true')
		{
			$q = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%')";
			$r = mysqli_query($ms, $q); 
			
			while ($ud = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$notify = false;
				
				$user_id = $ud["id"];
				$email = $ud["email"];
				$notify_object_expire = $ud['notify_object_expire'];
				
				$q2 = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";
				$r2 = mysqli_query($ms, $q2);
				
				while ($row2 = mysqli_fetch_array($r2,MYSQL_ASSOC))
				{
					$imei = $row2['imei'];
					
					$q3 = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
					$r3 = mysqli_query($ms, $q3);
					$row3 = mysqli_fetch_array($r3,MYSQL_ASSOC);
					
					$active_dt = $row3['active_dt'];
					
					$diff = strtotime($active_dt) - strtotime(gmdate("Y-m-d"));
					$days = $diff / 86400;
					
					if ($days <= $gsValues['NOTIFY_OBJ_EXPIRE_PERIOD'])
					{
						$notify = true;
						break;
					}	
				}
				
				if ($notify == true)
				{
					if ($notify_object_expire != 'true')
					{
						$template = getDefaultTemplate('expiring_objects', $ud["language"]);
						
						$subject = $template['subject'];
						$message = $template['message'];
						
						$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
						$subject = str_replace("%URL_SHOP%", $gsValues['URL_SHOP'], $subject);
						
						$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
						$message = str_replace("%URL_SHOP%", $gsValues['URL_SHOP'], $message);
						
						if (sendEmail($email, $subject, $message))
						{					
							$q4 = "UPDATE gs_users SET `notify_object_expire`='true' WHERE `id`='".$user_id."'";
							$r4 = mysqli_query($ms, $q4);
						}	
					}
				}
				else
				{
					$q4 = "UPDATE gs_users SET `notify_object_expire`='false' WHERE `id`='".$user_id."'";
					$r4 = mysqli_query($ms, $q4);
				}
			}
		}
	}
	
	function serviceClearHistory()
	{
		global $ms, $gsValues;
		
		if (!isset($gsValues['HISTORY_PERIOD']))
		{
			die;
		}
		
		if ($gsValues['HISTORY_PERIOD'] < 30)
		{
			die;
		}
		
		$q = "SELECT * FROM `gs_objects` ORDER BY `imei` ASC";
  		$r = mysqli_query($ms, $q);  
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC)) 
		{
			$q2 = "DELETE FROM `gs_object_data_".$row['imei']."` WHERE dt_tracker < DATE_SUB(UTC_DATE(), INTERVAL ".$gsValues['HISTORY_PERIOD']." DAY)";
  			$r2 = mysqli_query($ms, $q2);
		}
	}
	
	function serviceClearVarious()
	{
		global $ms, $gsValues;
		
		if (!isset($gsValues['HISTORY_PERIOD']))
		{
			die;
		}
		
		if ($gsValues['HISTORY_PERIOD'] < 30)
		{
			die;
		}
		
		$q = "DELETE FROM `gs_sms_gateway` WHERE dt_server < DATE_SUB(UTC_DATE(), INTERVAL 1 DAY)";
  		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_cmd_exec` WHERE dt_cmd < DATE_SUB(UTC_DATE(), INTERVAL 1 DAY)";
  		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rfid_swipe_data` WHERE dt_swipe < DATE_SUB(UTC_DATE(), INTERVAL ".$gsValues['HISTORY_PERIOD']." DAY)";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rilogbook_data` WHERE dt_tracker < DATE_SUB(UTC_DATE(), INTERVAL ".$gsValues['HISTORY_PERIOD']." DAY)";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE dt_tracker < DATE_SUB(UTC_DATE(), INTERVAL ".$gsValues['HISTORY_PERIOD']." DAY)";
  		$r = mysqli_query($ms, $q);
		
		$q = "SELECT * FROM `gs_object_img` WHERE dt_tracker < DATE_SUB(UTC_DATE(), INTERVAL ".$gsValues['HISTORY_PERIOD']." DAY)";
  		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$q2 = "DELETE FROM `gs_object_img` WHERE `img_id`='".$row['img_id']."'";
			$r2 = mysqli_query($ms, $q2);
			
			$img_file = $gsValues['PATH_ROOT'].'data/img/'.$row['img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}
		}
		
		$q = "SELECT * FROM `gs_object_chat` WHERE dt_server < DATE_SUB(UTC_DATE(), INTERVAL ".$gsValues['HISTORY_PERIOD']." DAY)";
  		$r = mysqli_query($ms, $q);
	}
	
	function serviceSendReportWeekly()
	{
		global $ms, $gsValues;
		
		// wait 2 hours before and after start of new date to make sure all devices sent data from buffer	
		if ((gmdate("H") < 2) || (gmdate("H") > 22))
		{	
			die;
		}
		
		// get weekly reports
		$q = "SELECT * FROM `gs_user_reports` WHERE schedule_period LIKE '%w%' AND dt_schedule_w < DATE_SUB(UTC_DATE(), INTERVAL 6 DAY)";
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$reports = array();
		
		while($report = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$previous_week = strtotime("-1 week +1 day");
			
			// get prev week monday
			$start_week = strtotime("last monday", $previous_week);
			
			// get next week monday
			$end_week = strtotime("next monday", $start_week);
			
			$report['dtf'] = date("Y-m-d", $start_week).' 00:00:00';
			$report['dtt'] = date("Y-m-d", $end_week).' 00:00:00';
			
			//// get prev week monday
			//$report['dtf'] = date('Y-m-d', strtotime('monday'));
			//$report['dtf'] = date('Y-m-d', strtotime($report['dtf'].' -7 days')).' 00:00:00';
			//
			//// get prev week sunday
			//$report['dtt'] = date('Y-m-d', strtotime('last sunday'));
			//$report['dtt'] = date('Y-m-d', strtotime($report['dtt'].' +1 days')).' 00:00:00';
			
			$dt_schedule_w = date('Y-m-d', strtotime('monday')).' 00:00:00';
			
			$q2 = 'UPDATE gs_user_reports SET `dt_schedule_w` = "'.$dt_schedule_w.'" WHERE report_id="'.$report['report_id'].'"';
			$r2 = mysqli_query($ms, $q2);
			
			if ($r2)
			{
				$reports[] = $report;
			}
			
			// generate 5 reports at once
			if (count($reports) > 4)
			{
				if ($gsValues['CURL'] == true)
				{
					serviceSendReportsCURL($reports);
				}
				else
				{
					serviceSendReports($reports);
				}
				
				// reset previous reports
				$reports = array();
			}
		}
		
		// generate left reports
		if (count($reports) > 0)
		{
			if ($gsValues['CURL'] == true)
			{
				serviceSendReportsCURL($reports);
			}
			else
			{
				serviceSendReports($reports);
			}
			
			// reset previous reports
			$reports = array();
		}
	}
	
	function serviceSendReportDaily()
	{
		global $ms, $gsValues;
		
		// wait 2 hours before and after start of new date to make sure all devices sent data from buffer
		if ((gmdate("H") < 2) || (gmdate("H") > 22))
		{	
			die;
		}
		
		// get daily reports
		$q = "SELECT * FROM `gs_user_reports` WHERE schedule_period LIKE '%d%' AND dt_schedule_d < UTC_DATE()";
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$reports = array();
		
		while($report = mysqli_fetch_array($r,MYSQL_ASSOC))
		{		
			$report['dtf'] = date('Y-m-d',strtotime("-1 days")).' 00:00:00'; // yesterday
			$report['dtt'] = date('Y-m-d').' 00:00:00'; // today
			
			$dt_schedule_d = gmdate("Y-m-d H:i:s");
			
			$q2 = 'UPDATE gs_user_reports SET `dt_schedule_d` = "'.$dt_schedule_d.'" WHERE report_id="'.$report['report_id'].'"';
			$r2 = mysqli_query($ms, $q2);
			
			if ($r2)
			{
				$reports[] = $report;
			}
			
			// generate 5 reports at once
			if (count($reports) > 4)
			{
				if ($gsValues['CURL'] == true)
				{
					serviceSendReportsCURL($reports);
				}
				else
				{
					serviceSendReports($reports);
				}
				
				// reset previous reports
				$reports = array();
			}
		}
		
		// generate left reports
		if (count($reports) > 0)
		{
			if ($gsValues['CURL'] == true)
			{
				serviceSendReportsCURL($reports);
			}
			else
			{
				serviceSendReports($reports);
			}
				
			// reset previous reports
			$reports = array();
		}
	}
	
	function serviceSendReports($reports)
	{
		global $ms, $gsValues;
		
		$url = $gsValues['URL_ROOT'].'/func/fn_reports.gen.php';
		
		$reports_count = count($reports);
		
		for($i = 0; $i < $reports_count; $i++)
		{
			$postdata = http_build_query(
							array(
								'cmd' => 'report',
								'schedule' => true,
								'user_id' => $reports[$i]['user_id'],
								'email' => $reports[$i]['schedule_email_address'],
								'name' => $reports[$i]['name'],
								'type' => $reports[$i]['type'],
								'format' => $reports[$i]['format'],
								'show_addresses' => $reports[$i]['show_addresses'],
								'zones_addresses' => $reports[$i]['zones_addresses'],
								'stop_duration' => $reports[$i]['stop_duration'],
								'speed_limit' => $reports[$i]['speed_limit'],
								'imei' => $reports[$i]['imei'],
								'zone_ids' => $reports[$i]['zone_ids'],
								'sensor_names' => $reports[$i]['sensor_names'],
								'data_items' => $reports[$i]['data_items'],
								'dtf' => $reports[$i]['dtf'],
								'dtt' => $reports[$i]['dtt']
							));
			
			$opts = array('http' =>
					array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata
					)
			);
			
			$context  = stream_context_create($opts);
			
			$result = file_get_contents($url, false, $context);
			
			$result = null;
			unset($result);
		}
	}
	
	function serviceSendReportsCURL($reports)
	{
		global $ms, $gsValues;
		
		$url = $gsValues['URL_ROOT'].'/func/fn_reports.gen.php';
		
		$reports_count = count($reports);
		
		$curl_arr = array();
		$master = curl_multi_init();
		
		for($i = 0; $i < $reports_count; $i++)
		{
			$postdata = http_build_query(
							array(
								'cmd' => 'report',
								'schedule' => true,
								'user_id' => $reports[$i]['user_id'],
								'email' => $reports[$i]['schedule_email_address'],
								'name' => $reports[$i]['name'],
								'type' => $reports[$i]['type'],
								'format' => $reports[$i]['format'],
								'show_addresses' => $reports[$i]['show_addresses'],
								'zones_addresses' => $reports[$i]['zones_addresses'],
								'stop_duration' => $reports[$i]['stop_duration'],
								'speed_limit' => $reports[$i]['speed_limit'],
								'imei' => $reports[$i]['imei'],
								'zone_ids' => $reports[$i]['zone_ids'],
								'sensor_names' => $reports[$i]['sensor_names'],
								'data_items' => $reports[$i]['data_items'],
								'dtf' => $reports[$i]['dtf'],
								'dtt' => $reports[$i]['dtt']
							));
			
			$curl_arr[$i] = curl_init($url);
			curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_arr[$i], CURLOPT_POST, 1);
			curl_setopt($curl_arr[$i], CURLOPT_POSTFIELDS, $postdata);
			curl_multi_add_handle($master, $curl_arr[$i]);
		}
		
		do
		{
			curl_multi_exec($master, $running);
		}
		while ($running > 0);
		
		for ($i = 0; $i < $reports_count; $i++)
		{
			$result = curl_multi_getcontent($curl_arr[$i]);
		}
		
		unset($curl_arr);
	}
	
	// service 5min
	function serviceEventService()
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_events` WHERE `type`='service'";
		$r = mysqli_query($ms, $q);
		
		while($ed = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			if ($ed['active'] == 'true')
			{
				// get user data
				$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$ed['user_id']."'";
				$r2 = mysqli_query($ms, $q2);
				$ud = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				// get current date and time for week days and day time check
				$dt_server = gmdate("Y-m-d H:i:s");
				$dt_check = date("Y-m-d H:i:s", strtotime($dt_server.$ud["timezone"]));
				
				if (!check_event_week_days($dt_check, $ed['week_days']))
				{
					continue;
				}
				
				if (!check_event_day_time($dt_check, $ed['day_time']))
				{
					continue;
				}
				
				// prepare imei's
				$imeis = explode(",", $ed['imei']);
				
				// get all imei services
				for ($i=0; $i<count($imeis); ++$i)
				{					
					$imei = $imeis[$i];
					
					$q2 = "SELECT * FROM `gs_object_services` WHERE `imei`='".$imei."'";
					$r2 = mysqli_query($ms, $q2);
					
					while($sd = mysqli_fetch_array($r2,MYSQL_ASSOC))
					{
						$event = false;
						
						// check if odo is expired
						if (($sd['odo'] == 'true') && ($sd['odo_left'] == 'true'))
						{
							$odometer = getObjectOdometer($imei);
							
							$odometer = round($odometer);
							$sd['odo_interval'] = round($sd['odo_interval']);
							$sd['odo_last'] = round($sd['odo_last']);
							
							$odo_diff = $odometer - $sd['odo_last'];
							$odo_diff = $sd['odo_interval'] - $odo_diff;
							
							if ($odo_diff <= $sd['odo_left_num'])
							{
								$event = true;
								
								if ($sd['update_last'] == 'true')
								{
									$q3 = "UPDATE gs_object_services SET `odo_last` = odo_last + ".$sd['odo_interval']." WHERE `service_id`='".$sd['service_id']."'";
									$r3 = mysqli_query($ms, $q3);	
								}
							}
						}
						
						// check if engh is expired
						if (($sd['engh'] == 'true') && ($sd['engh_left'] == 'true'))
						{
							$engine_hours = getObjectEngineHours($imei);
							
							$engh_diff = $engine_hours - $sd['engh_last'];
							$engh_diff = $sd['engh_interval'] - $engh_diff;
							
							if ($engh_diff <= $sd['engh_left_num'])
							{
								$event = true;
								
								if ($sd['update_last'] == 'true')
								{
									$q3 = "UPDATE gs_object_services SET `engh_last` = engh_last + ".$sd['engh_interval']." WHERE `service_id`='".$sd['service_id']."'";
									$r3 = mysqli_query($ms, $q3);
								}
							}
						}
						
						// check if days are expired
						if (($sd['days'] == 'true') && ($sd['days_left'] == 'true'))
						{
							$days_diff = strtotime(gmdate("Y-m-d")) - (strtotime($sd['days_last']));
							$days_diff = floor($days_diff/3600/24);
							$days_diff = $sd['days_interval'] - $days_diff;
							
							if ($days_diff <= $sd['days_left_num'])
							{
								$event = true;
								
								if ($sd['update_last'] == 'true')
								{
									$days_last = date('Y-m-d', strtotime($sd['days_last']. ' + '.$sd['days_interval'].' days'));
									
									$q3 = "UPDATE gs_object_services SET `days_last` = '".$days_last."' WHERE `service_id`='".$sd['service_id']."'";
									$r3 = mysqli_query($ms, $q3);
								}
							}
						}
						
						if ($event == true)
						{
							if (($sd['notify_service_expire'] != 'true') || ($sd['update_last'] == 'true'))
							{
								if ($sd['update_last'] != 'true')
								{
									$q3 = "UPDATE gs_object_services SET `notify_service_expire` = 'true' WHERE `service_id`='".$sd['service_id']."'";
									$r3 = mysqli_query($ms, $q3);
								}
								
								// get object last location
								$q3 = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
								$r3 = mysqli_query($ms, $q3);
								$loc = mysqli_fetch_array($r3,MYSQL_ASSOC);
								
								// set dt_server and dt_tracker to show exact time
								$loc['dt_server'] = gmdate("Y-m-d H:i:s");
								$loc['dt_tracker'] = $loc['dt_server'];
								
								// get object details
								$q3 = "SELECT gs_objects.*, gs_user_objects.*
									FROM gs_objects
									INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
									WHERE gs_user_objects.imei='".$loc['imei']."'";
								$r3 = mysqli_query($ms, $q3);
								$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
								
								// add event desc to event data array
								$ed['event_desc'] = $sd['name'];
								
								event_notify($ed,$ud,$od,$loc);	
							}
						}
						else
						{
							$q3 = "UPDATE gs_object_services SET `notify_service_expire` = 'false' WHERE `service_id`='".$sd['service_id']."'";
							$r3 = mysqli_query($ms, $q3);
						}
					}
				}
			}			
		}
	}
	
	function serviceEventConnection()
	{
		global $ms, $gsValues;
		
		$q = "SELECT * FROM `gs_user_events` WHERE `type`='connyes' OR `type`='connno'";
		$r = mysqli_query($ms, $q);
		
		while($ed = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			if ($ed['active'] == 'true')
			{
				// get user data
				$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$ed['user_id']."'";
				$r2 = mysqli_query($ms, $q2);
				$ud = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				// get current date and time for week days and day time check
				$dt_server = gmdate("Y-m-d H:i:s");
				$dt_check = date("Y-m-d H:i:s", strtotime($dt_server.$ud["timezone"]));
				
				if (!check_event_week_days($dt_check, $ed['week_days']))
				{
					continue;
				}
				
				if (!check_event_day_time($dt_check, $ed['day_time']))
				{
					continue;
				}
				
				// prepare imei's
				$imei = explode(",", $ed['imei']);
				$imei = '"'.implode('","', $imei).'"';
				
				// get all imeis which sent data during last 24 hours
				$q2 = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) IN (".$imei.") AND dt_server > DATE_SUB(UTC_DATE(), INTERVAL 1 DAY)";
				$r2 = mysqli_query($ms, $q2);
				
				while($loc = mysqli_fetch_array($r2,MYSQL_ASSOC))
				{
					if ($ed['type'] == 'connyes')
					{
						if(strtotime($loc['dt_server']) >= strtotime(gmdate("Y-m-d H:i:s")." - ".$gsValues['CONNECTION_TIMEOUT']." minutes"))
						{
							
							if (get_event_status($ed['event_id'], $loc['imei']) == -1)
							{
								set_event_status($ed['event_id'], $loc['imei'], '1');
								
								// get object details
								$q3 = "SELECT gs_objects.*, gs_user_objects.*
									FROM gs_objects
									INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
									WHERE gs_user_objects.imei='".$loc['imei']."'";
								$r3 = mysqli_query($ms, $q3);
								$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
								
								// add event desc to event data array
								$ed['event_desc'] = $ed['name'];
								
								// set dt_tracker to dt_server to show exact connection time
								$loc['dt_tracker'] = $loc['dt_server'];
								
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
					
					if ($ed['type'] == 'connno')
					{
						if(strtotime($loc['dt_server']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes"))
						{
							if (get_event_status($ed['event_id'], $loc['imei']) == -1)
							{
								set_event_status($ed['event_id'], $loc['imei'], '1');
								
								// get object details
								$q3 = "SELECT gs_objects.*, gs_user_objects.*
									FROM gs_objects
									INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
									WHERE gs_user_objects.imei='".$loc['imei']."'";
								$r3 = mysqli_query($ms, $q3);
								$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
								
								// add event desc to event data array
								$ed['event_desc'] = $ed['name'];
								
								// set dt_tracker to dt_server to show exact disconnection time
								$loc['dt_tracker'] = $loc['dt_server'];
								
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
				}
			}
		}
	}
	
	function serviceEventGPS()
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_events` WHERE `type`='gpsyes' OR `type`='gpsno'";
		$r = mysqli_query($ms, $q);
		
		while($ed = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			if ($ed['active'] == 'true')
			{
				// get user data
				$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$ed['user_id']."'";
				$r2 = mysqli_query($ms, $q2);
				$ud = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				// get current date and time for week days and day time check
				$dt_server = gmdate("Y-m-d H:i:s");
				$dt_check = date("Y-m-d H:i:s", strtotime($dt_server.$ud["timezone"]));
				
				if (!check_event_week_days($dt_check, $ed['week_days']))
				{
					continue;
				}
				
				if (!check_event_day_time($dt_check, $ed['day_time']))
				{
					continue;
				}
				
				// prepare imei's
				$imei = explode(",", $ed['imei']);
				$imei = '"'.implode('","', $imei).'"';
				
				// get all imeis which sent data during last 24 hours
				$q2 = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) IN (".$imei.") AND dt_server > DATE_SUB(UTC_DATE(), INTERVAL 1 DAY)";
				$r2 = mysqli_query($ms, $q2);
				
				while($loc = mysqli_fetch_array($r2,MYSQL_ASSOC))
				{
					if ($ed['type'] == 'gpsyes')
					{
						if ($loc['loc_valid'] == '1')
						{	
							if (get_event_status($ed['event_id'], $loc['imei']) == -1)
							{
								set_event_status($ed['event_id'], $loc['imei'], '1');
								
								// get object details
								$q3 = "SELECT gs_objects.*, gs_user_objects.*
									FROM gs_objects
									INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
									WHERE gs_user_objects.imei='".$loc['imei']."'";
								$r3 = mysqli_query($ms, $q3);
								$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
								
								// add event desc to event data array
								$ed['event_desc'] = $ed['name'];
								
								// set dt_tracker to dt_server to show exact connection time
								$loc['dt_tracker'] = $loc['dt_server'];
								
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
					
					if ($ed['type'] == 'gpsno')
					{
						if (($loc['loc_valid'] == '0') && (strtotime($loc['dt_tracker']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
						{
							if (get_event_status($ed['event_id'], $loc['imei']) == -1)
							{
								set_event_status($ed['event_id'], $loc['imei'], '1');
								
								// get object details
								$q3 = "SELECT gs_objects.*, gs_user_objects.*
									FROM gs_objects
									INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
									WHERE gs_user_objects.imei='".$loc['imei']."'";
								$r3 = mysqli_query($ms, $q3);
								$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
								
								// add event desc to event data array
								$ed['event_desc'] = $ed['name'];
								
								// set dt_tracker to dt_server to show exact disconnection time
								$loc['dt_tracker'] = $loc['dt_server'];
								
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
				}
			}
		}
	}
	
	// service 1 minute
	function serviceEventStoppedMovingIdle()
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_events` WHERE `type`='stopped' OR `type`='moving' OR `type`='engidle'";
		$r = mysqli_query($ms, $q);
		
		while($ed = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			if ($ed['active'] == 'true')
			{
				// get user data
				$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$ed['user_id']."'";
				$r2 = mysqli_query($ms, $q2);
				$ud = mysqli_fetch_array($r2,MYSQL_ASSOC);
				
				// get current date and time for week days and day time check
				$dt_server = gmdate("Y-m-d H:i:s");
				$dt_check = date("Y-m-d H:i:s", strtotime($dt_server.$ud["timezone"]));
				
				if (!check_event_week_days($dt_check, $ed['week_days']))
				{
					continue;
				}
				
				if (!check_event_day_time($dt_check, $ed['day_time']))
				{
					continue;
				}
				
				// prepare imei's
				$imei = explode(",", $ed['imei']);
				$imei = '"'.implode('","', $imei).'"';
				
				// get all imeis which sent data during last 24 hours
				$q2 = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) IN (".$imei.") AND dt_server > DATE_SUB(UTC_DATE(), INTERVAL 1 DAY)";
				$r2 = mysqli_query($ms, $q2);
				
				while($loc = mysqli_fetch_array($r2,MYSQL_ASSOC))
				{
					$dt_last_stop = strtotime($loc['dt_last_stop']);
					$dt_last_idle = strtotime($loc['dt_last_idle']);
					$dt_last_move = strtotime($loc['dt_last_move']);
					
					if (($dt_last_stop > 0) || ($dt_last_move > 0))
					{
						if ($ed['type'] == 'stopped')
						{
							if (($dt_last_stop >= $dt_last_move) && (strtotime($loc['dt_last_stop']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
							{
								if (get_event_status($ed['event_id'], $loc['imei']) == -1)
								{
									set_event_status($ed['event_id'], $loc['imei'], '1');
									
									// get object details
									$q3 = "SELECT gs_objects.*, gs_user_objects.*
										FROM gs_objects
										INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
										WHERE gs_user_objects.imei='".$loc['imei']."'";
									$r3 = mysqli_query($ms, $q3);
									$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
									
									// add event desc to event data array
									$ed['event_desc'] = $ed['name'];
									
									// set dt_tracker to dt_server to show exact disconnection time
									$loc['dt_tracker'] = $loc['dt_server'];
									
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
						
						if ($ed['type'] == 'moving')
						{
							if (($dt_last_stop < $dt_last_move) && (strtotime($loc['dt_last_move']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
							{
								if (get_event_status($ed['event_id'], $loc['imei']) == -1)
								{
									set_event_status($ed['event_id'], $loc['imei'], '1');
									
									// get object details
									$q3 = "SELECT gs_objects.*, gs_user_objects.*
										FROM gs_objects
										INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
										WHERE gs_user_objects.imei='".$loc['imei']."'";
									$r3 = mysqli_query($ms, $q3);
									$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
									
									// add event desc to event data array
									$ed['event_desc'] = $ed['name'];
									
									// set dt_tracker to dt_server to show exact disconnection time
									$loc['dt_tracker'] = $loc['dt_server'];
									
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
						
						if ($ed['type'] == 'engidle')
						{
							if (($dt_last_stop <= $dt_last_idle) && ($dt_last_move <= $dt_last_idle) && (strtotime($loc['dt_last_idle']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
							{
								if (get_event_status($ed['event_id'], $loc['imei']) == -1)
								{
									set_event_status($ed['event_id'], $loc['imei'], '1');
									
									// get object details
									$q3 = "SELECT gs_objects.*, gs_user_objects.*
										FROM gs_objects
										INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
										WHERE gs_user_objects.imei='".$loc['imei']."'";
									$r3 = mysqli_query($ms, $q3);
									$od = mysqli_fetch_array($r3,MYSQL_ASSOC);
									
									// add event desc to event data array
									$ed['event_desc'] = $ed['name'];
									
									// set dt_tracker to dt_server to show exact disconnection time
									$loc['dt_tracker'] = $loc['dt_server'];
									
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
					}
				}
			}
		}
	}
?>