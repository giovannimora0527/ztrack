<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/email.php');
	loadLanguage($gsValues['LANGUAGE']);
	
	if(@$_POST['cmd'] == 'session_check')
	{
		checkUserSession();
	
		if (checkUserSession2() == true)
		{
			echo 'true';
		}
		else
		{
			echo 'false';
		}
		die;
	}

	if(@$_POST['cmd'] == 'login')
	{
		$username = strtolower($_POST["username"]);
		$password = $_POST["password"];
		$remember_me = $_POST["remember_me"];
		$mobile = $_POST["mobile"];
		
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `password`='".md5($password)."' LIMIT 1";
		$r = mysqli_query($ms, $q);

		if ($row = mysqli_fetch_array($r))
		{
			if ($row['active'] == 'true')
			{				
				if ($gsValues['LOGIN_VIA_HTTP'] == true)
				{
					$result['cmd'] = 'login_http';
					$result['url'] = $gsValues['URL_ROOT']."/login.php?u=".$username."&p=".$password."&rm=".$remember_me."&m=".$mobile;
				}
				else
				{
					if ($remember_me == 'true')
					{
						setUserSessionHash($row['id']);
					}
					
					setUserSession($row['id']);
					setUserSessionSettings($row['id']);
					setUserSessionCPanel($row['id']);
					
					if (($gsValues['PAGE_AFTER_LOGIN'] == 'cpanel') && ($_SESSION["cpanel_privileges"] != false))
					{
						$result['cmd'] = 'login_cpanel';	
					}
					else
					{
						$result['cmd'] = 'login_tracking';	
					}
					
					//write log
					writeLog('user_access', 'User login: successful');
				}
			}
			else
			{
				$result['cmd'] = 'msg';
				$result['msg'] = $la['YOUR_ACCOUNT_IS_LOCKED'];
				
				//write log
				writeLog('user_access', 'User login: locked account. Username: "'.$username.'"');
			}
		}
		else
		{
			$result['cmd'] = 'msg';
			$result['msg'] = $la['USERNAME_OR_PASSWORD_INCORRECT'];
			
			//write log
			writeLog('user_access', 'User login: unsuccessful. Username: "'.$username.'"');
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
		die;	
	}
	
	if ((@$_POST['cmd'] == 'register') && ($gsValues['ALLOW_REGISTRATION'] == "true"))
	{
		$email = $_POST['email'];
		$seccode = $_POST['seccode'];
		
		if ($email != '')
		{
			if ($seccode == @$_SESSION["seccode"])
			{
				$account_expire = $gsValues['ACCOUNT_EXPIRE'];
				$account_expire_dt = '';
				
				if ($account_expire == 'true')
				{
					$account_expire_dt = date("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$gsValues['ACCOUNT_EXPIRE_PERIOD'].' days'));
				}
				
				$privileges = array();
				$privileges['type'] = 'user';
				$privileges['history'] = stringToBool($gsValues['HISTORY']);
				$privileges['reports'] = stringToBool($gsValues['REPORTS']);
				$privileges['rilogbook'] = stringToBool($gsValues['RILOGBOOK']);
				$privileges['object_control'] = stringToBool($gsValues['OBJECT_CONTROL']);
				$privileges['image_gallery'] = stringToBool($gsValues['IMAGE_GALLERY']);
				$privileges['chat'] = stringToBool($gsValues['CHAT']);
				$privileges = json_encode($privileges);
				
				$result = addUser('true', 'true', $account_expire, $account_expire_dt, $privileges, '', $email, '', $gsValues['OBJ_HISTORY_CLEAR'], $gsValues['OBJ_EDIT'], $gsValues['OBJ_ADD'], $gsValues['OBJ_NUM'], $gsValues['OBJ_DT']);
				
				if ($result == 'OK')
				{
					$result = $la['REGISTRATION_SUCCESSFUL'].' '.$la['PLEASE_CHECK_YOUR_EMAIL'];
				}
				
				echo $result;
			}
			else
			{
				echo $la['SECURITY_CODE_IS_INCORRECT'];
			}
		}
	}
	
	if (@$_POST['cmd'] == 'recover')
	{
		$email = $_POST['email'];
		$seccode = $_POST['seccode'];
		
		if ($email != "")
		{
			if ($seccode == $_SESSION["seccode"])
			{
				$email = strtolower($email);
				
				$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `privileges` NOT LIKE ('%subuser%') LIMIT 1";
				$r = mysqli_query($ms, $q);
				$num = mysqli_num_rows($r);
				
				if ($num > 0)
				{
					$row = mysqli_fetch_array($r,MYSQL_ASSOC);
					
					$new_password = substr(hash('sha1',gmdate('d F Y G i s u')),0,6);
					
					$template = getDefaultTemplate('account_recover', $gsValues['LANGUAGE']);
					
					$subject = $template['subject'];
					$message = $template['message'];
					
					$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
					$subject = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $subject);
					$subject = str_replace("%EMAIL%", $email, $subject);
					$subject = str_replace("%USERNAME%", $row['username'], $subject);
					$subject = str_replace("%PASSWORD%", $new_password, $subject);
					
					$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
					$message = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $message);
					$message = str_replace("%EMAIL%", $email, $message);
					$message = str_replace("%USERNAME%", $row['username'], $message);
					$message = str_replace("%PASSWORD%", $new_password, $message);
					
					if (sendEmail($email, $subject, $message))
					{
						$q = "UPDATE gs_users SET password='".md5($new_password)."' WHERE email='".$email."'";
						$r = mysqli_query($ms, $q);
						
						echo $la['USERNAME_PASSWORD_SENT'].' '.$la['PLEASE_CHECK_YOUR_EMAIL'];
						
						//write log
						writeLog('user_access', 'User recover: successful. E-mail: '.$email);
					}
					else
					{
						echo $la['CANT_SEND_EMAIL'].' '.$la['CONTACT_ADMINISTRATOR'];
					}
				}
				else
				{
					echo $la['THIS_EMAIL_IS_NOT_REGISTERED'];
					
					//write log
					writeLog('user_access', 'User recover: no such e-mail. E-mail: '.$email);
				}
			}
			else
			{
				echo $la['SECURITY_CODE_IS_INCORRECT'];
			}
		}
	}

	if (@$_POST['cmd'] == 'logout')
	{
		//write log
		writeLog('user_access', 'User logout');
		
		if (isset($_SESSION["user_id"]))
		{				
			deleteUserSessionHash($_SESSION["user_id"]);
		}
		
		session_unset();
		session_destroy();
		
		echo $gsValues['URL_LOGIN'];
	}
?>