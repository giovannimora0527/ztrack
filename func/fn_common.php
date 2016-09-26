<?
	// #################################################
	// USER FUNCTIONS
	// #################################################
	
	function getUserIdFromAU($au)
	{
		global $ms, $gsValues;
		
		$result = false;
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' and `privileges` LIKE '%".$au."%'";
		$r = mysqli_query($ms, $q);
		
		if ($row = mysqli_fetch_array($r))
		{
			$privileges = json_decode($row['privileges'],true);
			
			if ($privileges['type'] == 'subuser')
			{
				if ($privileges['au_active'] == true)
				{
					if ($privileges['au'] == $au)
					{
						if ($row['active'] == "true")
						{
							$result = $row['id'];	
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	function getUserIdFromSessionHash()
	{
		global $ms, $gsValues;
		
		$result = false;
		
		if (isset($_COOKIE['gs_sess_hash']))
		{
			$sess_hash = $_COOKIE['gs_sess_hash'];
			
			$q = "SELECT * FROM `gs_users` WHERE `sess_hash`='".$sess_hash."'";
			$r = mysqli_query($ms, $q);
			
			if ($row = mysqli_fetch_array($r))
			{
				$sess_hash_check = md5($gsValues['PATH_ROOT'].$row['id'].$row['username'].$row['password'].$_SERVER['REMOTE_ADDR']);
				
				if ($sess_hash_check == $sess_hash)
				{
					$result = $row['id'];
				}
			}
		}
		
		return $result;
	}
	
	function setUserSessionHash($id)
	{
		global $ms, $gsValues;
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$row = mysqli_fetch_array($r);
		
		$sess_hash = md5($gsValues['PATH_ROOT'].$row['id'].$row['username'].$row['password'].$_SERVER['REMOTE_ADDR']);
		
		$q = "UPDATE gs_users SET `sess_hash`='".$sess_hash."' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$expire = time() + 2592000;
		setcookie("gs_sess_hash", $sess_hash, $expire, '/', null, null, true);
	}
	
	function deleteUserSessionHash($id)
	{
		global $ms;
		
		$q = "UPDATE gs_users SET `sess_hash`='' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$expire = time() + 2592000;
		setcookie("gs_sess_hash","",time()-$expire, '/');
	}
	
	function setUserSession($id)
	{
		global $ms, $gsValues;
		
		$_SESSION["user_id"] = $id;
		$_SESSION["session"] = md5($gsValues['PATH_ROOT']);
		$_SESSION["remote_addr"] = md5($_SERVER['REMOTE_ADDR']);
		
		$q2 = "UPDATE gs_users SET `ip`='".$_SERVER['REMOTE_ADDR']."', `dt_login`='".gmdate("Y-m-d H:i:s")."' WHERE `id`='".$id."'";
		$r2 = mysqli_query($ms, $q2);
	}
	
	function setUserSessionSettings($id)
	{
		global $ms, $gsValues;
		
		// set user settings
		$_SESSION = array_merge($_SESSION, getUserData($id));
	}
	
	function setUserSessionCPanel($id)
	{
		global $ms, $gsValues;
		
		if (!isset($_SESSION["cpanel_privileges"]))
		{
			if ($_SESSION['privileges'] == 'super_admin')
			{
				$_SESSION["cpanel_user_id"] = $id;
				$_SESSION["cpanel_privileges"] = 'super_admin';
				$_SESSION["cpanel_manager_id"] = 0;
			}
			else if ($_SESSION['privileges'] == 'admin')
			{
				$_SESSION["cpanel_user_id"] = $id;
				$_SESSION["cpanel_privileges"] = 'admin';
				$_SESSION["cpanel_manager_id"] = 0;
			}
			else if ($_SESSION['privileges'] == 'manager')
			{
				$_SESSION["cpanel_user_id"] = $id;
				$_SESSION["cpanel_privileges"] = 'manager';
				$_SESSION["cpanel_manager_id"] = $id;
			}
			else
			{
				$_SESSION["cpanel_privileges"] = false;
			}
		}
	}
	
	function checkUserSession()
	{
		$file = basename($_SERVER['SCRIPT_NAME']);
		
		if (($file == 'index.php') || (checkUserSession2() == false))
		{
			session_unset();
			session_destroy();
			session_start();
				
			$user_id = getUserIdFromSessionHash();
			
			if($user_id != false)
			{
				setUserSession($user_id);
				setUserSessionSettings($user_id);
				setUserSessionCPanel($user_id);
			}
		}
		
		if (checkUserSession2() == false)
		{
			if (($file == 'tracking.php') || ($file == 'cpanel.php'))
			{
				Header("Location: index.php");
			}
			
			if (($file != 'index.php') && ($file != 'fn_connect.php'))
			{
				die;
			}
		}
		else
		{
			if ($file == 'index.php')
			{
				Header("Location: tracking.php");
			}
		}
	}
	
	function checkUserSession2()
	{
		global $ms, $gsValues;
		
		$result = false;
		
		if (isset($_SESSION["user_id"]) && isset($_SESSION["session"]) && isset($_SESSION["remote_addr"]) && isset($_SESSION["cpanel_privileges"]))
		{
			if (checkUserActive($_SESSION["user_id"]) == true)
			{
				
				if ($_SESSION["cpanel_privileges"] == false)
				{
					if ($_SESSION["session"] == md5($gsValues['PATH_ROOT']))
					{
						$result = true;
					}	
				}
				else
				{
					if (($_SESSION["session"] == md5($gsValues['PATH_ROOT'])) && ($_SESSION["remote_addr"] == md5($_SERVER['REMOTE_ADDR'])))
					{
						$result = true;
					}	
				}
			}	
		}
		
		return $result;
	}
	
	function checkUserActive($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if ($row['active'] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function checkUserCPanelPrivileges()
	{
		global $ms, $gsValues;
		
		if (!isset($_SESSION["cpanel_privileges"]))
		{
			die;
		}
		
		if ($_SESSION["cpanel_privileges"] == false)
		{
			die;
		}
		
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($gsValues['ADMIN_IP'] != '')
			{
				$admin_ips = explode(",", $gsValues['ADMIN_IP']);	
				if (!in_array($_SERVER['REMOTE_ADDR'], $admin_ips))
				{
					die;
				}	
			}
		}
		
		if ($_SESSION["user_id"] != $_SESSION['cpanel_user_id'])
		{
			setUserSession($_SESSION['cpanel_user_id']);
		}
	}
	
	function getUserData($id)
	{
		global $ms, $la;
		
		$result = array();
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result["user_id"] = $id;
		$result["active"] = $row['active'];
		$result["manager_id"] = $row['manager_id'];
		$result["manager_obj_num"] = $row["manager_obj_num"];
		
		$privileges = json_decode($row['privileges'],true);
		$privileges = checkUserPrivilegesArray($privileges);
		
		if ($privileges["type"] == 'subuser')
		{
			$result["privileges"] = $privileges["type"];
			
			$privileges["imei"] = explode(",", $privileges["imei"]);
			$result["privileges_imei"] = '"'.implode('","', $privileges["imei"]).'"';
			
			$privileges["marker"] = explode(",", $privileges["marker"]);
			$result["privileges_marker"] = '"'.implode('","', $privileges["marker"]).'"';
			
			$privileges["route"] = explode(",", $privileges["route"]);
			$result["privileges_route"] = '"'.implode('","', $privileges["route"]).'"';
			
			$privileges["zone"] = explode(",", $privileges["zone"]);
			$result["privileges_zone"] = '"'.implode('","', $privileges["zone"]).'"';
			
			// check manager user privileges, in case some of them are not available, reset subuser privileges
			$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$row['manager_id']."'";
			$r2 = mysqli_query($ms, $q2);
			$row2 = mysqli_fetch_array($r2);
			$manager_privileges = json_decode($row2['privileges'],true);
			$manager_privileges = checkUserPrivilegesArray($manager_privileges);
			
			if ($manager_privileges["history"] == false) { $privileges["history"] = false; }
			if ($manager_privileges["reports"] == false) { $privileges["reports"] = false; }
			if ($manager_privileges["rilogbook"] == false) { $privileges["rilogbook"] = false; }
			if ($manager_privileges["object_control"] == false) { $privileges["object_control"] = false; }
			if ($manager_privileges["image_gallery"] == false) { $privileges["image_gallery"] = false; }
			if ($manager_privileges["chat"] == false) { $privileges["chat"] = false; }
			
			$result["privileges_history"] = $privileges["history"];
			$result["privileges_reports"] = $privileges["reports"];
			$result["privileges_rilogbook"] = $privileges["rilogbook"];
			$result["privileges_object_control"] = $privileges["object_control"];
			$result["privileges_image_gallery"] = $privileges["image_gallery"];
			$result["privileges_chat"] = $privileges["chat"];
		}
		else
		{
			$result["privileges"] = $privileges["type"];
			$result["privileges_imei"] = '';
			$result["privileges_marker"] = '';
			$result["privileges_route"] = '';
			$result["privileges_zone"] = '';
			
			$result["privileges_history"] = $privileges["history"];
			$result["privileges_reports"] = $privileges["reports"];
			$result["privileges_rilogbook"] = $privileges["rilogbook"];
			$result["privileges_object_control"] = $privileges["object_control"];
			$result["privileges_image_gallery"] = $privileges["image_gallery"];
			$result["privileges_chat"] = $privileges["chat"];
		}
		
		$result["username"] = $row['username'];
		$result["email"] = $row['email'];
		$result["info"] = $row['info'];
		$result["obj_history_clear"] = $row['obj_history_clear'];
		$result["obj_edit"] = $row['obj_edit'];
		$result["obj_add"] = $row['obj_add'];
		$result["obj_num"] = $row['obj_num'];
		$result["obj_dt"] = $row['obj_dt'];
		
		$result["currency"] = $row['currency'];
		$result["timezone"] = $row['timezone'];
		
		$result["dst"] = $row['dst'];
		$result["dst_start"] = $row['dst_start'];
		$result["dst_end"] = $row['dst_end'];
		
		$result["language"] = $row['language'];
		
		$result["chat_notify"] = $row['chat_notify'];
		
		$result["map_sp"] = $row['map_sp'];
		
		if($row['map_rc'] == '')
		{
			$result["map_rc"] = '#FF0000';
		}
		else
		{
			$result["map_rc"] = $row['map_rc'];
		}
		
		if($row['map_rhc'] == '')
		{
			$result["map_rhc"] = '#0800FF';
		}
		else
		{
			$result["map_rhc"] = $row['map_rhc'];
		}
		
		$result["od"] = $row['od'];
		$result["ohc"] = $row['ohc'];
		
		$result["sms_gateway_server"] = $row['sms_gateway_server'];
		$result["sms_gateway"] = $row['sms_gateway'];
		$result["sms_gateway_type"] = $row['sms_gateway_type'];
		$result["sms_gateway_url"] = $row['sms_gateway_url'];
		$result["sms_gateway_identifier"] = $row['sms_gateway_identifier'];
		
		$result["places_markers"] = $row['places_markers'];
		$result["places_routes"] = $row['places_routes'];
		$result["places_zones"] = $row['places_zones'];
		
		// units
		$result["units"] = $row['units'];
		$result = array_merge($result, getUnits($row['units']));
		
		return $result;
	}
	
	function convUserTimezone($dt)
	{
		if (strtotime($dt) > 0)
		{
			$dt = date("Y-m-d H:i:s", strtotime($dt.$_SESSION["timezone"]));
			
			// DST
			if ($_SESSION["dst"] == 'true')
			{
				$year = date('Y', strtotime($dt));
				
				$dst_start = strtotime($year.'-'.$_SESSION["dst_start"].':00');
				$dst_end =  strtotime($year.'-'.$_SESSION["dst_end"].':00');
				$dt_time = strtotime($dt);
				
				if (($dst_start <= $dt_time) && ($dst_end >= $dt_time))
				{
					$dt = date("Y-m-d H:i:s", strtotime($dt.'+ 1 hour'));
				}	
			}
		}
		
		return $dt;
	}
	
	function convUserUTCTimezone($dt)
	{
		if (strtotime($dt) > 0)
		{
			if (substr($_SESSION["timezone"],0,1) == "+")
			{
				$timezone_diff = str_replace("+", "-", $_SESSION["timezone"]);
			}
			else
			{
				$timezone_diff = str_replace("-", "+", $_SESSION["timezone"]);
			}
			
			$dt = date("Y-m-d H:i:s", strtotime($dt.$timezone_diff));
			
			// DST
			if ($_SESSION["dst"] == 'true')
			{
				$year = date('Y', strtotime($dt));
				
				$dst_start = strtotime($year.'-'.$_SESSION["dst_start"].':00');
				$dst_end =  strtotime($year.'-'.$_SESSION["dst_end"].':00');
				$dt_time = strtotime($dt);
				
				if (($dst_start <= $dt_time) && ($dst_end >= $dt_time))
				{
					$dt = date("Y-m-d H:i:s", strtotime($dt.'- 1 hour'));
				}	
			}
		}
		
		return $dt;
	}
	
	function checkUserToObjectPrivileges($imei)
	{
		global $ms;
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{
			$user_id = $_SESSION["manager_id"];
		}
		else
		{
			$user_id = $_SESSION["user_id"];
		}
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if ($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function addUser($send, $active, $account_expire, $account_expire_dt, $privileges, $manager_id, $email, $password, $obj_history_clear, $obj_edit, $obj_add, $obj_num, $obj_dt)
	{
		global $ms, $gsValues, $la;
		
		$status = false;
		
		$result = '';
		
		$email = strtolower($email);
		
		$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num == 0)
		{
			if ($password == '')
			{
				$password = substr(hash('sha1',gmdate('d F Y G i s u')),0,6);
			}
			
			$privileges_ = json_decode(stripslashes($privileges),true);
			
			if (isset($_SESSION['LANGUAGE']))
			{
				$language = $_SESSION['LANGUAGE'];
			}
			else
			{
				$language = $gsValues['LANGUAGE'];
			}
			
			if (($privileges_['type'] == 'subuser') && (@$privileges_['au_active'] == true))
			{
				$url_au = $gsValues['URL_ROOT']."/login.php?au=".$privileges_['au'];
				$url_au_mobile = $gsValues['URL_ROOT']."/login.php?au=".$privileges_['au'].'&m=true';
				
				$template = getDefaultTemplate('account_registration_au', $language);
				
				$subject = $template['subject'];
				$message = $template['message'];
				
				$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
				$subject = str_replace("%URL_AU%", $url_au, $subject);
				$subject = str_replace("%URL_AU_MOBILE%", $url_au_mobile, $subject);
				
				$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
				$message = str_replace("%URL_AU%", $url_au, $message);
				$message = str_replace("%URL_AU_MOBILE%", $url_au_mobile, $message);
			}
			else
			{
				$template = getDefaultTemplate('account_registration', $language);
				
				$subject = $template['subject'];
				$message = $template['message'];
				
				$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
				$subject = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $subject);
				$subject = str_replace("%EMAIL%", $email, $subject);
				$subject = str_replace("%USERNAME%", $email, $subject);
				$subject = str_replace("%PASSWORD%", $password, $subject);
				
				$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
				$message = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $message);
				$message = str_replace("%EMAIL%", $email, $message);
				$message = str_replace("%USERNAME%", $email, $message);
				$message = str_replace("%PASSWORD%", $password, $message);
			}
			
			if ($send == 'true')
			{
				if (sendEmail($email, $subject, $message))
				{
					$status = true;
				}
			}
			else
			{
				$status = true;
			}
			
			
			if ($status == true)
			{					
				$obj_dt = date("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$obj_dt.' days'));
				
				$dst = $gsValues['DST'];
				
				if ($dst == 'true')
				{
					$dst_start = $gsValues['DST_START'];
					$dst_end = $gsValues['DST_END'];	
				}
				else
				{
					$dst_start = '';
					$dst_end = '';	
				}
				
				$units = $gsValues['UNIT_OF_DISTANCE'].','.$gsValues['UNIT_OF_CAPACITY'].','.$gsValues['UNIT_OF_TEMPERATURE'];
				
				$q = "INSERT INTO gs_users (	`active`,
								`account_expire`,
								`account_expire_dt`,
								`privileges`,
								`manager_id`,
								`username`, 
								`password`, 
								`email`, 
								`dt_reg`,
								`obj_history_clear`,
								`obj_edit`,
								`obj_add`, 
								`obj_num`, 
								`obj_dt`,
								`currency`,
								`timezone`,
								`dst`,
								`dst_start`,
								`dst_end`,
								`language`,
								`units`,
								`map_sp`,
								`sms_gateway_server`)
								VALUES
								('".$active."',
								'".$account_expire."',
								'".$account_expire_dt."',
								'".$privileges."',
								'".$manager_id."',
								'".$email."',
								'".md5($password)."',
								'".$email."',
								'".gmdate("Y-m-d H:i:s")."',
								'".$obj_history_clear."',
								'".$obj_edit."',
								'".$obj_add."',
								'".$obj_num."',
								'".$obj_dt."',
								'".$gsValues['CURRENCY']."',
								'".$gsValues['TIMEZONE']."',
								'".$dst."',
								'".$dst_start."',
								'".$dst_end."',
								'".$gsValues['LANGUAGE']."',
								'".$units."',
								'last',
								'".$gsValues['SMS_GATEWAY_SERVER']."'
								)";
								
				$r = mysqli_query($ms, $q);
				$result = 'OK';
				
				//write log
				writeLog('user_access', 'User registration: successful. E-mail: '.$email);
			}
			else
			{
				$result = $la['CANT_SEND_EMAIL'].' '.$la['CONTACT_ADMINISTRATOR'];	
			}
		}
		else
		{
			$result = $la['THIS_EMAIL_ALREADY_EXISTS'];
		}
		
		return $result;
	}
	
	function delUser($id)
	{
		global $ms, $gsValues;
		
		$q = "DELETE FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// delete user sub users
		$q = "DELETE FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_zones` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_markers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_objects` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_object_groups` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);		
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$id."'";
  		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$img_file = $gsValues['PATH_ROOT'].'data/user/drivers/'.$row['driver_img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}			
		}
		
		$q = "DELETE FROM `gs_user_object_drivers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_object_passengers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_object_trailers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_cmd_exec` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_cmd` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_reports` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// delete user events
		$q = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		while ($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$event_id = $row['event_id'];
			
			$q2 = "DELETE FROM `gs_user_events_status` WHERE `event_id`='".$event_id."'";				
			$r2 = mysqli_query($ms, $q2);
		}
		
		$q = "DELETE FROM `gs_user_events` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
	}
	
	function getUserObjectIMEIs($id)
	{
		global $ms;
		
		$result = false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$result .= '"'.$row['imei'].'",';
		}
		$result = rtrim($result, ',');
		
		return $result;
	}
	
	function getUserNumberOfMarkers($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_markers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		return $count;
	}
	
	function getUserNumberOfZones($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		return $count;
	}
	
	function getUserNumberOfRoutes($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		return $count;
	}
	
	function checkUserPrivilegesArray($privileges)
	{
		if (!isset($privileges["history"])) { $privileges["history"] = true; }
		if (!isset($privileges["reports"])) { $privileges["reports"] = true; }
		if (!isset($privileges["rilogbook"])) { $privileges["rilogbook"] = true; }
		if (!isset($privileges["object_control"])) { $privileges["object_control"] = true; }
		if (!isset($privileges["image_gallery"])) { $privileges["image_gallery"] = true; }
		if (!isset($privileges["chat"])) { $privileges["chat"] = true; }
		
		return $privileges;
	}
	
	// #################################################
	//  END USER FUNCTIONS
	// #################################################
	
	// #################################################
	// OBJECT FUNCTIONS
	// #################################################
	
	function checkObjectLimitSystem()
	{
		global $ms, $gsValues;
		
		if ($gsValues['OBJECT_LIMIT'] == 0)
		{
			return false;
		}
		
		$q = "SELECT * FROM `gs_objects`";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num >= $gsValues['OBJECT_LIMIT'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function checkObjectLimitUser()
	{
		global $ms;
		
		// check privileges
		if ($_SESSION["privileges"] == 'subuser')
		{
			$user_id = $_SESSION["manager_id"];
		}
		else
		{
			$user_id = $_SESSION["user_id"];
		}
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if($num >= $_SESSION["obj_num"])
		{
			return true;
		}
		return false;
	}
	
	function checkObjectExistsSystem($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		if (!$r)
		{
			return false;
		}
		
		$num = mysqli_num_rows($r);
		if ($num >= 1)
		{
			return true;
		}
		return false;	
	}
	
	function checkObjectExistsUser($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		if (!$r)
		{
			return false;
		}
		
		$num = mysqli_num_rows($r);
		if ($num >= 1)
		{
			return true;
		}
		return false;
	}
	
	function adjustObjectTime($imei, $dt)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if($row)
		{
			if (strtotime($dt) > 0)
			{
				$dt = date("Y-m-d H:i:s", strtotime($dt.$row["time_adj"]));
			}
		}
		
		return $dt;
	}
	
	function createObjectDataTable($imei)
	{
		global $ms;
		
		$q = "CREATE TABLE IF NOT EXISTS gs_object_data_".$imei."(	dt_server datetime NOT NULL,
										dt_tracker datetime NOT NULL,
										lat double,
										lng double,
										altitude double,
										angle double,
										speed double,
										params varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
										KEY `dt_tracker` (`dt_tracker`)
										) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		$r = mysqli_query($ms, $q);
	}
	
	function addObjectSystem($name, $imei, $active, $active_date, $manager_id)
	{
		global $ms;
		
		$q = "INSERT INTO `gs_objects` (`imei`,
						`active`,
						`active_dt`,
						`manager_id`,
						`name`,
						`map_icon`,
						`icon`,
						`tail_color`,
						`tail_points`,
						`odometer_type`,
						`engine_hours_type`)
						VALUES
						('".$imei."',
						'".$active."',
						'".$active_date."',
						'".$manager_id."',
						'".$name."',
						'arrow',
						'img/markers/objects/31.png',
						'#00FF44',
						7,
						'gps',
						'off')";		
		$r = mysqli_query($ms, $q);
		
		// delete from unused objects
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Add object: successful. IMEI: '.$imei);
	}
	
	function addObjectSystemExtended($name, $imei, $model, $vin, $plate_number, $device, $sim_number, $active, $active_date, $manager_id)
	{
		global $ms;
		
		$q = "INSERT INTO `gs_objects` (`imei`,
						`active`,
						`active_dt`,
						`manager_id`,
						`name`,
						`map_icon`,
						`icon`,
						`tail_color`,
						`tail_points`,
						`device`,
						`sim_number`,
						`model`,
						`vin`,
						`plate_number`,
						`odometer_type`,
						`engine_hours_type`)
						VALUES
						('".$imei."',
						'".$active."',
						'".$active_date."',
						'".$manager_id."',
						'".$name."',
						'arrow',
						'img/markers/objects/31.png',
						'#00FF44',
						7,
						'".$device."',
						'".$sim_number."',
						'".$model."',
						'".$vin."',
						'".$plate_number."',
						'gps',
						'off')";		
		$r = mysqli_query($ms, $q);
		
		// delete from unused objects
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Add object: successful. IMEI: '.$imei);
	}
	
	function addObjectUser($user_id, $imei, $group_id, $driver_id, $trailer_id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
                $r = mysqli_query($ms, $q);
                $num = mysqli_num_rows($r);
                if ($num == 0)
                {
			$q = "INSERT INTO `gs_user_objects` 	(`user_id`,
								`imei`,
								`group_id`,
								`driver_id`,
								`trailer_id`)
								VALUES (
								'".$user_id."',
								'".$imei."',
								'".$group_id."',
								'".$driver_id."',
								'".$trailer_id."')";
			$r = mysqli_query($ms, $q);
                }
	}
	
	function duplicateObjectSystem($duplicate_imei, $imei, $active_date, $manager_id, $name)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$q = "INSERT INTO `gs_objects` (`imei`,
						`active`,
						`active_dt`,
						`manager_id`,
						`name`,
						`icon`,
						`map_arrows`,
						`map_icon`,
						`tail_color`,
						`tail_points`,
						`device`,
						`sim_number`,
						`model`,
						`vin`,
						`plate_number`,
						`odometer_type`,
						`engine_hours_type`,
						`odometer`,
						`engine_hours`,
						`fcr`,
						`time_adj`,
						`accuracy`)
						VALUES
						('".$imei."',
						'true',
						'".$active_date."',
						'".$manager_id."',
						'".$name."',
						'".$row['icon']."',
						'".$row['map_arrows']."',
						'".$row['map_icon']."',
						'".$row['tail_color']."',
						'".$row['tail_points']."',
						'".$row['device']."',
						'".$row['sim_number']."',
						'".$row['model']."',
						'".$row['vin']."',
						'".$row['plate_number']."',
						'".$row['odometer_type']."',
						'".$row['engine_hours_type']."',
						'".$row['odometer']."',
						'".$row['engine_hours']."',
						'".$row['fcr']."',
						'".$row['time_adj']."',
						'".$row['accuracy']."')";		
		$r = mysqli_query($ms, $q);
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		while($row = mysqli_fetch_array($r))
		{
			$q2 = "INSERT INTO `gs_object_sensors` (`imei`,
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
								VALUES
								('".$imei."',
								'".$row['name']."',
								'".$row['type']."',
								'".$row['param']."',
								'".$row['popup']."',
								'".$row['result_type']."',
								'".$row['text_1']."',
								'".$row['text_0']."',
								'".$row['units']."',
								'".$row['lv']."',
								'".$row['hv']."',
								'".$row['formula']."',
								'".$row['calibration']."')";
			$r2 = mysqli_query($ms, $q2);
		}
		
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		while($row = mysqli_fetch_array($r))
		{
			$q2 = "INSERT INTO `gs_object_services` (`imei`,
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
								`update_last`,
								`notify_service_expire`)
								VALUES
								('".$imei."',
								'".$row['name']."',
								'".$row['odo']."',
								'".$row['odo_interval']."',
								'".$row['odo_last']."',
								'".$row['engh']."',
								'".$row['engh_interval']."',
								'".$row['engh_last']."',
								'".$row['days']."',
								'".$row['days_interval']."',
								'".$row['days_last']."',
								'".$row['odo_left']."',
								'".$row['odo_left_num']."',
								'".$row['engh_left']."',
								'".$row['engh_left_num']."',
								'".$row['days_left']."',
								'".$row['days_left_num']."',
								'".$row['update_last']."',
								'".$row['notify_service_expire']."')";
			$r2 = mysqli_query($ms, $q2);
		}
	}
	
	function delObjectUser($user_id, $imei)
	{
		global $ms;
		
		$q = "DELETE FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
	}

	function delObjectSystem($imei)
	{
		global $ms, $gsValues;
		
		$q = "DELETE FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rfid_swipe_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rilogbook_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_sensors` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_services` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "SELECT * FROM `gs_object_img` WHERE `imei`='".$imei."'";
  		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$img_file = $gsValues['PATH_ROOT'].'data/img/'.$row['img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}			
		}
		
		$q = "DELETE FROM `gs_object_img` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_chat` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DROP TABLE gs_object_data_".$imei;
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Delete object: successful. IMEI: '.$imei);
	}
	
	function clearObjectHistory($imei)
	{
		global $ms;
		
		$q = "DELETE FROM gs_object_data_".$imei;
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rfid_swipe_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "UPDATE `gs_objects` SET  `dt_server`='0000-00-00 00:00:00',
						`dt_tracker`='0000-00-00 00:00:00',
						`lat`='0',
						`lng`='0',
						`altitude`='0',
						`angle`='0',
						`speed`='0',
						`loc_valid`='0',
						`params`='',
						`dt_last_stop`='0000-00-00 00:00:00',
						`dt_last_idle`='0000-00-00 00:00:00',
						`dt_last_move`='0000-00-00 00:00:00'
						WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Clear object history: successful. IMEI: '.$imei);
	}
	
	function checkObjectActive($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		if ($row['active'] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getObjectName($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return $row['name'];
	}
	
	function getObjectDriverFromSensor($user_id, $imei, $params)
	{
		global $ms;
		
		$driver = false;
		
		$driver_assign_id = false;
		
		$sensor = getSensorFromType($imei, 'da');
		
		if ($sensor != false)
		{
			$sensor_ = $sensor[0];
		
			$sensor_data = getSensorValue($params, $sensor_);
			$driver_assign_id = $sensor_data['value'];
		}
		else
		{
			return $driver;                                      
		
		}
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE UPPER(`driver_assign_id`)='".strtoupper($driver_assign_id)."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$driver = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return $driver;
	}
	
	function getObjectTrailerFromSensor($user_id, $imei, $params)
	{
		global $ms;
		
		$trailer = false;
		
		$trailer_assign_id = false;
		
		$sensor = getSensorFromType($imei, 'ta');
		
		if ($sensor != false)
		{
			$sensor_ = $sensor[0];
			
			$sensor_data = getSensorValue($params, $sensor_);
			$trailer_assign_id = $sensor_data['value'];
		}
		else
		{
			return $trailer;                                      
		
		}
		
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE UPPER(`trailer_assign_id`)='".strtoupper($trailer_assign_id)."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$trailer = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return $trailer;
	}
	
	function getObjectDriver($user_id, $imei, $params)
	{
		global $ms;
		
		$driver = false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		$driver_id = $row['driver_id'];
		
		if ($driver_id == '-1')
		{
			return $driver;
		}
		
		if ($driver_id == '0')
		{
			return getObjectDriverFromSensor($user_id, $imei, $params);
		}
	       
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id ."' AND `driver_id`='".$driver_id."'";
		$r = mysqli_query($ms, $q);
		$driver = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return $driver;
	}
	
	function getObjectTrailer($user_id, $imei, $params)
	{
		global $ms;
		
		$trailer = false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		$trailer_id = $row['trailer_id'];
		
		if ($trailer_id == '-1')
		{
			return $trailer;
		}
		
		if ($trailer_id == '0')
		{
			return getObjectTrailerFromSensor($user_id, $imei, $params);
		}
	       
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id ."' AND `trailer_id`='".$trailer_id."'";
		$r = mysqli_query($ms, $q);
		$trailer = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return $trailer;
	}
	
	function getObjectOdometer($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return floor($row['odometer']);
	}
	
	function getObjectEngineHours($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		return floor($row['engine_hours'] / 60 / 60);
	}
	
	function getObjectFCR($imei)
	{
		global $ms, $gsValues;
		
		// default fcr
		$default = array(	'source' => 'rates',
					'measurement' => 'l100km',
					'cost' => 0,
					'summer' => 0,
					'winter' => 0,
					'winter_start' => '12-01',
					'winter_end' => '03-01');
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		// set default fcr if not set in DB
		if (($row['fcr'] == '') || (json_decode($row['fcr'],true) == null))
		{
			$fcr = $default;
		}
		else
		{
			$fcr = json_decode($row['fcr'],true);
			
			if (!isset($fcr["source"])) { $fcr["source"] = $default["source"]; }
			if (!isset($fcr["measurement"])) { $fcr["measurement"] = $default["measurement"]; }
			if (!isset($fcr["cost"])) { $fcr["cost"] = $default["cost"]; }
			if (!isset($fcr["summer"])) { $fcr["summer"] = $default["summer"]; }
			if (!isset($fcr["winter"])) { $fcr["winter"] = $default["winter"]; }
			if (!isset($fcr["winter_start"])) { $fcr["winter_start"] = $default["winter_start"]; }
			if (!isset($fcr["winter_end"])) { $fcr["winter_end"] = $default["winter_end"]; }
		}
		
		return $fcr;
	}
	
	function getObjectAccuracy($imei)
	{
		global $ms, $gsValues;
		
		// default accuracy
		$default = array(	'stops' => 'gps',
					'min_moving_speed' => 6,
					'min_idle_speed' => 3,
					'min_diff_points' => 0.0005,
					'use_gpslev' => false,
					'min_gpslev' => 5,
					'use_hdop' => false,
					'max_hdop' => 3,
					'min_ff' => 10,
					'min_ft' => 10);
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		// set default accuracy if not set in DB
		if (($row['accuracy'] == '') || (json_decode($row['accuracy'],true) == null))
		{
			$accuracy = $default;			
		}
		else
		{
			$accuracy = json_decode($row['accuracy'],true);
			
			if (!isset($accuracy["stops"])) { $accuracy["stops"] = $default["stops"]; }
			if (!isset($accuracy["min_moving_speed"])) { $accuracy["min_moving_speed"] = $default["min_moving_speed"]; }
			if (!isset($accuracy["min_idle_speed"])) { $accuracy["min_idle_speed"] = $default["min_idle_speed"]; }
			if (!isset($accuracy["min_diff_points"])) { $accuracy["min_diff_points"] = $default["min_diff_points"]; }
			if (!isset($accuracy["use_gpslev"])) { $accuracy["use_gpslev"] = $default["use_gpslev"]; }
			if (!isset($accuracy["min_gpslev"])) { $accuracy["min_gpslev"] = $default["min_gpslev"]; }
			if (!isset($accuracy["use_hdop"])) { $accuracy["use_hdop"] = $default["use_hdop"]; }
			if (!isset($accuracy["max_hdop"])) { $accuracy["max_hdop"] = $default["max_hdop"]; }
			if (!isset($accuracy["min_ff"])) { $accuracy["min_ff"] = $default["min_ff"]; }
			if (!isset($accuracy["min_ft"])) { $accuracy["stops"] = $default["stops"]; }
		}
		
		return $accuracy;
	}
	
	function getObjectSensors($imei)
	{
		global $ms;
		
		// get object sensor list
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$sensors = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$sensor_id = $row['sensor_id'];
			
			$calibration = json_decode($row['calibration'], true);
			if ($calibration == null)
			{
				$calibration = array();	
			}
			
			$sensors[$sensor_id] = array(	'name' => $row['name'],
							'type' => $row['type'],
							'param' => $row['param'],
							'popup' => $row['popup'],
							'result_type' => $row['result_type'],
							'text_1' => $row['text_1'],
							'text_0' => $row['text_0'],
							'units' => $row['units'],
							'lv' => $row['lv'],
							'hv' => $row['hv'],
							'formula' => $row['formula'],
							'calibration' => $calibration
							);
		}
		
		return $sensors;
	}
	
	function getObjectService($imei)
	{
		global $ms;
		
		// get object service list
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$imei."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$service = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$row['odo_interval'] = convDistanceUnits($row['odo_interval'], 'km', $_SESSION["unit_distance"]);
			$row['odo_last'] = convDistanceUnits($row['odo_last'], 'km', $_SESSION["unit_distance"]);
			$row['odo_left_num'] = convDistanceUnits($row['odo_left_num'], 'km', $_SESSION["unit_distance"]);
			
			$row['odo_interval'] = round($row['odo_interval']);
			$row['odo_last'] = round($row['odo_last']);
			$row['odo_left_num'] = round($row['odo_left_num']);
			
			$service_id = $row['service_id'];
			$service[$service_id] = array(	'name' => $row['name'],
							'odo' => $row['odo'],
							'odo_interval' => $row['odo_interval'],
							'odo_last' => $row['odo_last'],
							'engh' => $row['engh'],
							'engh_interval' => $row['engh_interval'],
							'engh_last' => $row['engh_last'],
							'days' => $row['days'],
							'days_interval' => $row['days_interval'],
							'days_last' => $row['days_last'],
							'odo_left' => $row['odo_left'],
							'odo_left_num' => $row['odo_left_num'],
							'engh_left' => $row['engh_left'],
							'engh_left_num' => $row['engh_left_num'],
							'days_left' => $row['days_left'],
							'days_left_num' => $row['days_left_num'],
							'update_last' => $row['update_last']
							);
		}
		
		return $service;
	}
	
	function getObjectActivePeriodAvgDate($id)
        {
		global $ms;
		
		$date_from_today = '';
                $total_days = 0;
                $count = 0;
                
                $q = "SELECT * FROM `gs_objects` WHERE `imei` IN (".getUserObjectIMEIs($id).")";
		$r = mysqli_query($ms, $q);
                
		if (!$r)
		{
			return $date_from_today;
		}

		while($row=mysqli_fetch_array($r))
		{			
			$today = strtotime(gmdate('Y-m-d'));
			$active_dt = strtotime($row['active_dt']);
			
			$diff_days = round(($active_dt - $today) / 86400);
			
			if ($diff_days > 0)
			{
				$total_days += $diff_days;
				$count++;
			}
		}	
                
		if ($count == 0)
		{
			return $date_from_today;
		}
		
		$total_days = round($total_days/$count);
		      
		$date_from_today = date('Y-m-d', strtotime(gmdate('Y-m-d'). ' + '.$total_days.' days'));
                
		return $date_from_today;
        }
	
	function sendObjectSMSCommand($user_id, $imei, $name, $cmd)
	{
		global $ms, $gsValues;
		
		$result = false;
		
		// validate
		$imei = strtoupper($imei);
		
		if ($imei == '') return $result;
		
		if ($cmd == '') return $result;
		
		// variables
		$cmd = str_replace("%IMEI%", $imei, $cmd);
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$ud = mysqli_fetch_array($r);
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$od = mysqli_fetch_array($r,MYSQL_ASSOC);
		
		$number = $od['sim_number'];
		
		if ($ud['sms_gateway'] == 'true')
		{
			if ($ud['sms_gateway_type'] == 'http')
			{
				$result = sendSMSHTTP($ud['sms_gateway_url'], '', $number, $cmd);
			}
			else if ($ud['sms_gateway_type'] == 'app')
			{
				$result = sendSMSAPP($ud['sms_gateway_identifier'], '',  $number, $cmd);
			}
		}
		else
		{
			if (($ud['sms_gateway_server'] == 'true') && ($gsValues['SMS_GATEWAY'] == 'true'))
			{
				if ($gsValues['SMS_GATEWAY_TYPE'] == 'http')
				{
					$result = sendSMSHTTP($gsValues['SMS_GATEWAY_URL'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $cmd);
				}
				else if ($gsValues['SMS_GATEWAY_TYPE'] == 'app')
				{
					$result = sendSMSAPP($gsValues['SMS_GATEWAY_IDENTIFIER'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $cmd);
				}
			}
		}
		
		if ($result == true)
		{
			$q = "INSERT INTO `gs_object_cmd_exec`(`user_id`,
								`dt_cmd`,
								`imei`,
								`name`,
								`gateway`,
								`type`,
								`cmd`,
								`status`)
								VALUES
								('".$user_id."',
								'".gmdate("Y-m-d H:i:s")."',
								'".$imei."',
								'".$name."',
								'sms',
								'ascii',
								'".$cmd."',							 
								'1')";
			$r = mysqli_query($ms, $q);	
		}
		
		return $result;
	}
	
	function sendObjectGPRSCommand($user_id, $imei, $name, $type, $cmd)
	{
		global $ms;
		
		$result = false;
		
		// validate
		$imei = strtoupper($imei);
				
		if ($imei == '') return $result;
		
		if ($cmd == '') return $result;
		
		if ($type == 'ascii')
		{
			// variables
			$cmd = str_replace("%IMEI%", $imei, $cmd);
		}
		else if ($type == 'hex')
		{
			$cmd = strtoupper($cmd);
			
			if (!ctype_xdigit($cmd)) return $result;
		}
		else
		{
			return $result;
		}
		
		$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei`='".$imei."' AND `type`='".$type."' AND `cmd`='".$cmd."' AND `status`='0'";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		if ($num == 0)
		{
			$q = "INSERT INTO `gs_object_cmd_exec`(`user_id`,
								`dt_cmd`,
								`imei`,
								`name`,
								`gateway`,
								`type`,
								`cmd`,
								`status`)
								VALUES
								('".$user_id."',
								'".gmdate("Y-m-d H:i:s")."',
								'".$imei."',
								'".$name."',
								'gprs',
								'".$type."',
								'".$cmd."',							 
								'0')";
			$r = mysqli_query($ms, $q);
			
			$result = true;
		}
		
		return $result;
	}
	
	// #################################################
	// END OBJECT FUNCTIONS
	// #################################################
	
	// #################################################
	// SENSOR FUNCTIONS
	// #################################################
	
	function mergeParams($old, $new)
	{
		if (is_array($old) && is_array($new))
		{
			$new = array_merge($old, $new);	
		}
		
		return $new;
	}
	
	function getParamsArray($params)
	{
		$arr_params = array();
		
		if ($params != '')
		{
			$params = json_decode($params,true);
			
			if (is_array($params))
			{
				foreach ($params as $key => $value)
				{
					array_push($arr_params, $key);
				}
			}
		}
		
		return $arr_params;
	}
	
	function getParamValue($params, $param)
	{
		$result = 0;
		
		if (isset($params[$param]))
		{
			$result = $params[$param];
		}
		
		return $result;
	}
	
	function paramsToArray($params)
	{
		// keep compatibility with old software versions which used '|' and with software versions using JSON
		
		$arr_params = array();
		if (substr($params, -1) == '|')
		{
			$params = explode("|", $params);
			
			for ($i = 0; $i < count($params)-1; ++$i)
			{
				$param = explode("=", $params[$i]);
				$arr_params[$param[0]] = $param[1];
			}
		}
		else
		{
			$arr_params = json_decode($params,true);
		}
		
		if (!is_array($arr_params))
		{
			$arr_params = array();
		}
		
		return $arr_params;
	}
	
	function getSensorValue($params, $sensor)
	{
		$result = array();
		$result['value'] = 0;
		$result['value_full'] = '';
		
		$param_value = getParamValue($params, $sensor['param']);
		
		// formula
		if (($sensor['result_type'] == 'abs') || ($sensor['result_type'] == 'rel') || ($sensor['result_type'] == 'value'))
		{
			if ($sensor['formula'] != '')
			{
				$formula = strtolower($sensor['formula']);
				if (!is_numeric($param_value))
				{
					$param_value = 0;
				}
				$formula = str_replace('x',$param_value,$formula);
				$param_value = calcString($formula);
			}
		}
		
		if (($sensor['result_type'] == 'abs') || ($sensor['result_type'] == 'rel'))
		{
			$param_value = sprintf("%01.3f", $param_value);
			
			$result['value'] = $param_value;
			$result['value_full'] = $param_value;
		}
		else if ($sensor['result_type'] == 'logic')
		{
			if($param_value == 1)
			{
				$result['value'] = $param_value;
				$result['value_full'] = $sensor['text_1'];
			}
			else
			{
				$result['value'] = $param_value;
				$result['value_full'] = $sensor['text_0'];
			}
		}
		else if ($sensor['result_type'] == 'value')
		{
			// calibration
			$out_of_cal = true;
			
			$calibration = json_decode($sensor['calibration'], true);
			if ($calibration == null)
			{
				$calibration = array();	
			}
			
			if (count($calibration) >= 2)
			{
				// put all X values to separate array
				$x_arr = array();
				
				for ($i=0; $i<count($calibration); $i++)
				{
					$x_arr[] = $calibration[$i]['x'];
				}
			    
				sort($x_arr);
				
				for ($i=0; $i<count($calibration)-1; $i++)
				{
					$x_low = $x_arr[$i];
					$x_high = $x_arr[$i+1];
					
					if (($param_value >= $x_low) && ($param_value <= $x_high))
					{
						// get Y low and high
						$y_low = 0;
						$y_high = 0;
						
						for($j=0; $j<count($calibration); $j++)
						{
							if ($calibration[$j]['x'] == $x_low)
							{
								$y_low = $calibration[$j]['y'];
							}
							
							if ($calibration[$j]['x'] == $x_high)
							{
								$y_high = $calibration[$j]['y'];
							}
						}
						
						// get coeficient
						$a = $param_value - $x_low;
						$b = $x_high - $x_low;
						
						$coef = ($a/$b);
						
						$c = $y_high - $y_low;
						$coef = $c * $coef;
						
						$param_value = $y_low + $coef;
						
						$out_of_cal = false;
						
						break;
					}
				}
			    
				if ($out_of_cal)
				{
					// check if lower than cal
					$x_low = $x_arr[0];
					
					if ($param_value < $x_low)
					{
						for($j=0; $j<count($calibration); $j++)
						{		    
							if ($calibration[$j]['x'] == $x_low)
							{
							    $param_value = $calibration[$j]['y'];
							}
						}
					}
					
					// check if higher than cal
					$x_high = end($x_arr);
					
					if ($param_value > $x_high)
					{		    
						for($j=0; $j<count($calibration); $j++)
						{		    
							if ($calibration[$j]['x'] == $x_high)
							{
							    $param_value = $calibration[$j]['y'];
							}
						}
					}
				}
			}
			
			$param_value = sprintf("%01.2f", $param_value);
			
			$result['value'] = $param_value;
			$result['value_full'] = $param_value.' '.$sensor['units'];
		}
		else if ($sensor['result_type'] == 'string')
		{
			$result['value'] = $param_value;
			$result['value_full'] = $param_value;
		}
		else if ($sensor['result_type'] == 'percentage')
		{
			if (($param_value > $sensor['lv']) && ($param_value < $sensor['hv']))
			{
				$a = $param_value - $sensor['lv'];
				$b = $sensor['hv'] - $sensor['lv'];
				
				$result['value'] = floor(($a/$b) * 100);
			}
			else if ($param_value <= $sensor['lv'])
			{
				$result['value'] = 0;
			}
			else if ($param_value >= $sensor['hv'])
			{
				$result['value'] = 100;
			}
			
			$result['value_full'] = $result['value'].' %';
		}
		
		return $result;
	}
	
	function getSensors($imei)
	{
		global $ms;
		
		$result = array();
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		while($sensor=mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$result[] = $sensor;
		}
		
		if (count($result) > 0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	function getSensorFromType($imei, $type)
	{
		global $ms;
		
		$result = array();
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."' AND `type`='".$type."'";
		$r = mysqli_query($ms, $q);
		
		while($sensor=mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$result[] = $sensor;
		}
		
		if (count($result) > 0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	// #################################################
	// END SENSOR FUNCTIONS
	// #################################################
	
	// #################################################
	// MATH FUNCTIONS
	// #################################################
	
	function calcString($str)
	{
		$result = 0;
		try
		{
			$str = trim($str);
			$str = preg_replace('/[^0-9\(\)+-\/\*.]/', '', $str);
			$str = $str.';';
			
			return $result + eval('return '.$str);	
		}
		catch (Exception $e)
		{
			return $result;
		}
	}
	
	function getUnits($units)
	{
		$result = array();
		
		$units = explode(",", $units);
		
		$result["unit_distance"] = @$units[0];
		if ($result["unit_distance"] == '')
		{
			$result["unit_distance"] = 'km';
		}
		
		$result["unit_capacity"] = @$units[1];
		if ($result["unit_capacity"] == '')
		{
			$result["unit_capacity"] = 'l';
		}
		
		$result["unit_temperature"] = @$units[2];
		if ($result["unit_temperature"] == '')
		{
			$result["unit_temperature"] = 'c';
		}
		
		return $result;
	}
	
	function convSpeedUnits($val, $from, $to)
	{
		return floor(convDistanceUnits($val, $from, $to));
	}
	
	function convDistanceUnits($val, $from, $to)
	{
		if ($from == 'km')
		{
			if ($to == 'mi')
			{
				$val = $val * 0.621371;
			}
			else if ($to == 'nm')
			{
				$val = $val * 0.539957;
			}
		}
		else if ($from == 'mi')
		{
			if ($to == 'km')
			{
				$val = $val * 1.60934;
			}
			else if ($to == 'nm')
			{
				$val = $val * 0.868976;
			}
		}
		else if ($from == 'nm')
		{
			if ($to == 'km')
			{
				$val = $val * 1.852;
			}
			else if ($to == 'nm')
			{
				$val = $val * 1.15078;
			}
		}
		
		return $val;	
	}
	
	function convAltitudeUnits($val, $from, $to)
	{
		if ($from == 'km')
		{
			if (($to == 'mi') || ($to == 'nm')) // to feet
			{
				$val = floor($val * 3.28084);
			}
		}
		
		return $val;
	}
	
	//function convTempUnits($val, $from, $to)
	//{
	//	
	//}
	
	function getTimeDetails($sec)
	{
		global $la;
		
		$seconds = 0;
 		$hours   = 0;
 		$minutes = 0;
		
		if($sec % 86400 <= 0){$days = $sec / 86400;}
		if($sec % 86400 > 0)
		{
			$rest = ($sec % 86400);
			$days = ($sec - $rest) / 86400;
     		if($rest % 3600 > 0)
			{
				$rest1 = ($rest % 3600);
				$hours = ($rest - $rest1) / 3600;
        		if($rest1 % 60 > 0)
				{
					$rest2 = ($rest1 % 60);
           		$minutes = ($rest1 - $rest2) / 60;
           		$seconds = $rest2;
        		}
        		else{$minutes = $rest1 / 60;}
     		}
     		else{$hours = $rest / 3600;}
		}
		
		if($days > 0){$days = $days.' '.$la['UNIT_D'].' ';}
		else{$days = false;}
		if($hours > 0){$hours = $hours.' '.$la['UNIT_H'].' ';}
		else{$hours = false;}
		if($minutes > 0){$minutes = $minutes.' '.$la['UNIT_MIN'].' ';}
		else{$minutes = false;}
		$seconds = $seconds.' '.$la['UNIT_S'];
		
		return $days.''.$hours.''.$minutes.''.$seconds;
	}
	
	function getTimeDifferenceDetails($start_date, $end_date)
	{
		$diff = strtotime($end_date)-strtotime($start_date);
		return getTimeDetails($diff);
	}

	function getLengthBetweenCoordinates($lat1, $lon1, $lat2, $lon2)
	{
		$theta = $lon1 - $lon2; 
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
		$dist = acos($dist); 
		$dist = rad2deg($dist); 
		$km = $dist * 60 * 1.1515 * 1.609344;
		
		return sprintf("%01.6f", $km);
	}
	
	function getAngle($lat1, $lng1, $lat2, $lng2)
	{
		$angle = (rad2deg(atan2(sin(deg2rad($lng2) - deg2rad($lng1)) * cos(deg2rad($lat2)), cos(deg2rad($lat1)) * sin(deg2rad($lat2)) - sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lng2) - deg2rad($lng1)))) + 360) % 360;
		
		return floor($angle);
	}
	
	function isPointInPolygon($vertices, $lat, $lng)
	{
		$polyX = array();
		$polyY = array();
		
		$ver_arr = explode(',', $vertices);
		
		// check for all X and Y
                if(!is_int(count($ver_arr)/2))
                {
                        array_pop($ver_arr);
                }
		
		$polySides = 0;
		$i = 0;
		
		while ($i < count($ver_arr))
		{
			$polyX[] = $ver_arr[$i+1];
			$polyY[] = $ver_arr[$i];
			
			$i+=2;
			$polySides++;
		}
		
		$j = $polySides-1 ;
		$oddNodes = 0;
		
		for ($i=0; $i<$polySides; $i++)
		{
			if ($polyY[$i]<$lat && $polyY[$j]>=$lat || $polyY[$j]<$lat && $polyY[$i]>=$lat)
			{
				if ($polyX[$i]+($lat-$polyY[$i])/($polyY[$j]-$polyY[$i])*($polyX[$j]-$polyX[$i])<$lng)
				{
					$oddNodes=!$oddNodes;
				}
			}
			$j=$i;
		}
		
		return $oddNodes;
	}
	
	function isPointOnLine($points, $lat, $lng)
        {
                $lineX = array();
		$lineY = array();
		
		$points_arr = explode(',', $points);
		
		// check for all X and Y
                if(!is_int(count($points_arr)/2))
                {
                        array_pop($points_arr);
                }
		
		$points_num = 0;
		$i = 0;
		
		while ($i < count($points_arr))
		{
			$lineX[] = $points_arr[$i];
			$lineY[] = $points_arr[$i+1];
			
			$i+=2;
			$points_num++;
		}
                
                for ($i=0; $i<$points_num-1; $i++)
		{
			// line segment
			$a['lat'] = $lineX[$i];
			$a['lng'] = $lineY[$i];
			$b['lat'] = $lineX[$i+1];
			$b['lng'] = $lineY[$i+1];
			
			// point
			$c['lat'] = $lat;
			$c['lng'] = $lng;
			
			$dist = getGeoDistancePointToSegment($a, $b, $c);
			$dist = sprintf('%0.6f', $dist);
                        
			if (!isset($distance))
			{
				$distance = $dist;
			}
			else
			{
				if ($distance > $dist)
				{
					$distance = $dist;
				}	
			}
		}
		
                return $distance;    
        }
	
	function getHeightFromBaseTriangle($ab, $ac, $bc)
	{
		// find $s (semiperimeter) for Heron's formula
		$s = ($ab + $ac + $bc) / 2;
		
		// Heron's formula - area of a triangle
		$area = sqrt($s * ($s - $ab) * ($s - $ac) * ($s - $bc));
		
		// find the height of a triangle - ie - distance from point to line segment
		$height = $area / (.5 * $ab);
		
		return $area;
	}
	
	function getAnglesFromSides($ab, $bc, $ac)
	{
		$a = $bc;
		$b = $ac;
		$c = $ab;
		
		$a_div = 2 * $b * $c;
		if ($a_div == 0)
		{
			$a_div = 1;
		}
		
		$b_div = 2 * $c * $a;
		if ($b_div == 0)
		{
			$b_div = 1;
		}
		
		$c_div = 2 * $a * $b;
		if ($c_div == 0)
		{
			$c_div = 1;
		}
		
		$angle['a'] = rad2deg(acos((pow($b,2) + pow($c,2) - pow($a,2)) / $a_div));
		$angle['b'] = rad2deg(acos((pow($c,2) + pow($a,2) - pow($b,2)) / $b_div));
		$angle['c'] = rad2deg(acos((pow($a,2) + pow($b,2) - pow($c,2)) / $c_div));
		
		return $angle;		
	}
	
	function getGeoDistancePointToSegment($a, $b, $c)
	{
		$ab = getLengthBetweenCoordinates($a['lat'], $a['lng'], $b['lat'], $b['lng']); // base or line segment
		$ac = getLengthBetweenCoordinates($a['lat'], $a['lng'], $c['lat'], $c['lng']);
		$bc = getLengthBetweenCoordinates($b['lat'], $b['lng'], $c['lat'], $c['lng']);
		
		$angle = getAnglesFromSides($ab, $bc, $ac);
		
		//if($ab + $ac == $bc) // then points are collinear - point is on the line segment
		//{
		//	return 0;
		//}
		//else
		if($angle['a'] <= 90 && $angle['b'] <= 90) // A or B are not obtuse - return height as distance
		{
			return getHeightFromBaseTriangle($ab, $ac, $bc);
		}
		else // A or B are obtuse - return smallest side as distance
		{
			return ($ac > $bc) ? $bc : $ac;
		}
	}
	
	// #################################################
	// END MATH FUNCTIONS
	// #################################################
	
	// #################################################
	// STRING/ARRAY FUNCTIONS
	// #################################################
	
	function stringToBool($str) {
		return filter_var($str, FILTER_VALIDATE_BOOLEAN);
	}

	function searchString($str, $findme)
	{
		return preg_match('/'.$findme.'/',$str);
	}
	
	function truncateString($text, $chars)
	{
		if (strlen($text) > $chars)
		{
			$text = substr($text, 0, $chars).'...';
		}
		return $text;
	}
	
	function generatorTag()
	{
		global $gsValues;
		echo '<meta name="generator" content="'.$gsValues['GENERATOR'].'" />';
	}
	
	// #################################################
	// END STRING/ARRAY FUNCTIONS
	// #################################################
	
	// #################################################
	// TEMPLATE FUNCTIONS
	// #################################################
	
	function getDefaultTemplate($name, $language)
	{
		global $ms;
		
		$result = false;
		
		$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='".$language."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if (!$row)
		{
			$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='english'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
		}
		
		if ($row)
		{
			$result = array('subject' => $row['subject'], 'message' => $row['message']);	
		}
		
		return $result;
	}
	
	// #################################################
	// END TEMPLATE FUNCTIONS
	// #################################################
	
	// #################################################
	// GEOCODER FUNCTIONS
	// #################################################

	function getGeocoderCache($lat, $lng)
	{
		global $ms;
		
		$result = '';
		
		// set lat and lng search ranges
		$lat_a = $lat - 0.000050;
		$lat_b = $lat + 0.000050;
		
		$lng_a = $lng - 0.000050;
		$lng_b = $lng + 0.000050;
		
		$q = "SELECT * FROM gs_geocoder_cache WHERE (lat BETWEEN ".$lat_a." AND ".$lat_b.") AND (lng BETWEEN ".$lng_a." AND ".$lng_b.")";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			$id = $row['id'];
			$result = $row['address'];			
			$count = $row['count'] + 1;
			
			$q = 'UPDATE gs_geocoder_cache SET `count`="'.$count.'" WHERE id="'.$id.'"';
			$r = mysqli_query($ms, $q);
		}
		
		return $result;
	}
	
	function insertGeocoderCache($lat, $lng, $address)
	{
		global $ms;
		
		if (($lat == '') || ($lng == '') || ($address == ''))
		{
			return;
		}
		
		$q = "INSERT INTO `gs_geocoder_cache`(	`lat`,
							`lng`,
							`address`)
							VALUES
							('".$lat."',
							'".$lng."',
							'".$address."')";
		$r = mysqli_query($ms, $q);
	}
	
	// #################################################
	// END GEOCODER FUNCTIONS
	// #################################################
	
	// #################################################
	// LANGUAGE FUNCTIONS
	// #################################################
	
	function loadLanguage($lng, $units = '')
	{
		global $ms, $la, $gsValues;
		
		// always load main english language to prevet error if something is not translated in another language
		include ($gsValues['PATH_ROOT'].'lng/english/lng_main.php');
		
		// load another language
		if ($lng != 'english')
		{
			$lng = $gsValues['PATH_ROOT'].'lng/'.$lng.'/lng_main.php';
			
			if (file_exists($lng))
			{
				include($lng);
			}
		}
		
		// set unit strings
		$units = getUnits($units);
		
		if ($units["unit_distance"] == 'km')
		{
			$la["UNIT_SPEED"] = $la['UNIT_KPH'];
			$la["UNIT_DISTANCE"] = $la['UNIT_KM'];
			$la["UNIT_HEIGHT"] = $la['UNIT_M'];
		}
		else if ($units["unit_distance"] == 'mi')
		{
			$la["UNIT_SPEED"] = $la['UNIT_MPH'];
			$la["UNIT_DISTANCE"] = $la['UNIT_MI'];
			$la["UNIT_HEIGHT"] = $la['UNIT_FT'];
		}
		else if ($units["unit_distance"] == 'nm')
		{
			$la["UNIT_SPEED"] = $la['UNIT_KN'];
			$la["UNIT_DISTANCE"] = $la['UNIT_NM'];
			$la["UNIT_HEIGHT"] = $la['UNIT_FT'];
		}
		
		if ($units["unit_capacity"] == 'l')
		{
			$la["UNIT_CAPACITY"] = $la['UNIT_LITERS'];
		}
		else
		{
			$la["UNIT_CAPACITY"] = $la['UNIT_GALLONS'];
		}
		
		if ($units["unit_temperature"] == 'c')
		{
			$la["UNIT_TEMPERATURE"] = 'C';
		}
		else
		{
			$la["UNIT_TEMPERATURE"] = 'F';
		}
	}
	
	function getLanguageListFiles()
	{
		global $gsValues;
		
		$result = '';
		
		$path = $gsValues['PATH_ROOT'].'lng';
		$dh = opendir($path);
	    
		$languages = array();
		    
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				$folder_path = $path.'/'.$file;
				
				if (is_dir($folder_path))
				{
					if (file_exists($folder_path.'/lng_main.php'))
					{
						$lng = strtolower($file);
						
						if ($lng != 'english')
						{
							$languages[] = $lng;	
						}
					}
				}
			}
		}
		
		closedir($dh);
		
		sort($languages);
		
		foreach ($languages as $value)
		{  		   
		    $result .= '<option value="'.$value.'">'.ucfirst($value).'</option>';
		}
		
		return $result;
	}
	
	function getLanguageList()
	{
		global $ms, $gsValues;
		
		$result = '';
		
		$languages = explode(",", $gsValues['LANGUAGES']);
		
		array_unshift($languages , '');
		
		foreach ($languages as $value)
		{
			if ($value != '')
			{				
				if($value == 'spanish'){
                  $result .= '<option value="'.$value.'">Español'.'</option>';
				}
				else{
					$result .= '<option value="'.$value.'">'.ucfirst($value).'</option>';	
				}
			}
		}
		
		return $result;
	}
	
	// #################################################
	// END LANGUAGE FUNCTIONS
	// #################################################
	
	// #################################################
	// END LOG FUNCTIONS
	// #################################################
	
	function writeLog($log, $log_data)
	{
		global $ms, $gsValues;
		
		$file = gmdate("Y_m").'_'.$log.'.php';
		$path = $gsValues['PATH_ROOT'].'logs/'.$file;
		
		if (!file_exists($path))
		{
			$str = "<?\r\n";
			$str .= "session_start();\r\n";
			$str .= "include ('../init.php');\r\n";
			$str .= "include ('../func/fn_common.php');\r\n";
			$str .= "checkUserSession();\r\n";
			$str .= "checkUserCPanelPrivileges();\r\n";
			$str .= "header('Content-Type:text/plain');\r\n";
			$str .= "?>\r\n";
			
			file_put_contents($path, $str, FILE_APPEND);
		}
		
		$str = '['.gmdate("Y-m-d H:i:s").'] '.$_SERVER['REMOTE_ADDR'].' ';
		
		if (isset($_SESSION["user_id"]) && isset($_SESSION["username"]))
		{
			$str .= '['.$_SESSION["user_id"].']'.$_SESSION["username"].' ';	
		}
		
		$str .= '- '.$log_data."\r\n";
		
		file_put_contents($path, $str, FILE_APPEND);
	}
	
	// #################################################
	// END LOG FUNCTIONS
	// #################################################
?>