<?php
	session_start();
	
	// if previous user did not log off, cancel his seesion and start new one
	if (isset($_SESSION["user_id"]))
	{
		session_unset();
		session_destroy();
		session_start();
	}
	
	include ('init.php');
	include ('func/fn_common.php');
       
	// login via http call, example: login.php?u=admin&p=123456&rm=true&m=false
	if (true)
	{             
		$username = strtolower($_GET["u"]);              
		$password = $_GET["p"];                  
		$remember_me = @$_GET["rm"];                
		$mobile = @$_GET["m"];               
		
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `password`='".($password)."' LIMIT 1";
                echo($q);
		$r = mysqli_query($ms, $q);
		
		if ($row=mysqli_fetch_array($r))
		{
			if ($row['active'] == "true")
			{
				if ($remember_me == 'true')
				{
					setUserSessionHash($row['id']);
				}
				
				setUserSession($row['id']);
				setUserSessionSettings($row['id']);
				setUserSessionCPanel($row['id']);
				
				//write log
				writeLog('user_access', 'User login via http call: successful');
				
				if ($mobile == 'true')
				{
					header('Location: mobile/tracking.php');
					die;
				}
				else
				{
					header('Location: tracking2.php');
					die;
				}
			}
		}	
	}
	
	if(isset($_GET['au']))
	{
		$au = $_GET['au'];
		$mobile = @$_GET["m"];
		$user_id = getUserIdFromAU($au);
		
		if ($user_id == false)
		{
			die;
		}
		
		setUserSession($user_id);
		setUserSessionSettings($user_id);
		setUserSessionCPanel($user_id);
		
		//write log
		writeLog('user_access', 'User login via login.php: successful');
		
		if ($mobile == 'true')
		{
			header('Location: mobile/tracking.php');
			die;
		}
		else
		{
			header('Location: tracking.php');
			die;
		}
		die;
	}
	
?>