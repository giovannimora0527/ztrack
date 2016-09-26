<?
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

	$username = "demo";
	$password = "demo123";
	
	$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `password`='".md5($password)."' LIMIT 1";
	$r = mysqli_query($ms, $q);
	
	if ($row=mysqli_fetch_array($r))
	{
		if ($row['active'] == "true")
		{
			setUserSession($row['id']);
			setUserSessionSettings($row['id']);
			setUserSessionCPanel($row['id']);
			
			//write log
			writeLog('user_access', 'User login via demo.php: successful');
			
			header('Location: tracking.php');
			die;
		}
	}
?>