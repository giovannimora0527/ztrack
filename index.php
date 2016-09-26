<?	
	session_start();
	include ('init.php');
	include ('func/fn_common.php');
	checkUserSession();
	
	loadLanguage($gsValues['LANGUAGE']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<? generatorTag(); ?>
	<title><? echo $gsValues['NAME'].' '.$gsValues['VERSION']; ?></title>
	
	<link type="text/css" href="theme/jquery-ui.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<script type="text/javascript" src="js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.show-pass.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
</head>

<body id="login" onload="connectLoad()">
	<div class="line"></div>
	<div class="login-block">
		<img width="250px" height="63px" id="logo" src="<? echo $gsValues['URL_LOGO']; ?>" />
		
		<?
			if ($gsValues['MULTI_SERVER_LOGIN'] == true)
			{
				if ($gsValues['ALLOW_REGISTRATION'] == "false")
				{
					echo '<div id="reg_closed">'.$la['NEW_USER_REGISTRATION_ON_THIS_SERVER_IS_CLOSED'].'</div>';
				}
				
				echo '<select id="server" class="selectbox" onChange="connectServer();">';
				
				foreach ($gsValues['MULTI_SERVER_LIST'] as $key => $value)
				{
					if ($gsValues['URL_ROOT'] == $key)
					{
						echo '<option selected value="'.$key.'">'.$value.'</option>';
					}
					else
					{
						echo '<option value="'.$key.'">'.$value.'</option>';
					}
				}
				echo '</select>';
			}
		?>
		
		<div id="tabs_connect">
			<ul>
				<li><a href="#tabs_connect_login"><? echo $la['LOGIN']; ?></a></li>
				<li><a href="#tabs_connect_recover"><? echo $la['LOST_LOGIN_RECOVERY']; ?></a></li>
				<? if ($gsValues['ALLOW_REGISTRATION'] == "true"){?>
				<li><a href="#tabs_connect_reg" id="tabs_connect_reg_tab"><? echo $la['REGISTRATION']; ?></a></li>
				<? }?>
			</ul>
			
			<div id="tabs_connect_login" class="tab-content">
				<form action="#" target="" autocomplete="on">
					<div class="title-block">
						<? echo $la['ENTER_USERNAME_AND_PASSWORD_TO_LOGIN']; ?>
					</div>
					<div class="row">
						<div class="icon icon-user"></div><input placeholder="<? echo $la['USERNAME']; ?>" class="inputbox" id="username" maxlength="50">
					</div>
					<div class="row" style="position: relative;">
						<div class="reveal" title="<? echo $la['SHOW_HIDE_PASSWORD']; ?>"></div>
						
						<div class="icon icon-password"></div><input placeholder="<? echo $la['PASSWORD']; ?>" class="inputbox" type="password" id="password" maxlength="20">
						
						<div class="remember-block">
							<div class="float-right"><? echo $la['REMEMBER_ME']; ?></div>
							<input class="checkbox float-right" type="checkbox" id="remember_me">
						</div>
					</div>
					<center><input type="submit" class="button" value="<? echo $la['LOGIN']; ?>" onClick="connectLogin(); return false;"/></center>
				</form>
			</div>
			
			<div id="tabs_connect_recover" class="tab-content">
				<div class="title-block">
					<? echo $la['NEW_LOGIN_DATA_WILL_BE_SENT_TO_EMAIL']; ?>
				</div>
				<div class="row">
					<div class="icon icon-email"></div><input placeholder="<? echo $la['EMAIL']; ?>" class="inputbox" id="crem_email" maxlength="50" />
				</div>
				<div class="row" style="position: relative;">
					<div class="icon icon-code"></div><input placeholder="<? echo $la['ENTER_CODE']; ?>" class="inputbox" type="text" id="crem_seccode" />
					<img class="security-code" src="tools/seccode.php" align="absmiddle">
				</div>
				<center><input type="button" class="button" value="<? echo $la['RECOVER']; ?>" onClick="connectRecover();"/></center>
			</div>
			
			<? if ($gsValues['ALLOW_REGISTRATION'] == "true"){?>
			<div id="tabs_connect_reg" class="tab-content">
				<div class="title-block">
					<? echo $la['LOGIN_DATA_WILL_BE_SENT_TO_EMAIL']; ?>
				</div>
				<div class="row">
					<div class="icon icon-email"></div><input placeholder="<? echo $la['EMAIL']; ?>" class="inputbox" id="creg_email" maxlength="50" />
				</div>
				<div class="row" style="position: relative;">
					<div class="icon icon-code"></div><input placeholder="<? echo $la['ENTER_CODE']; ?>" class="inputbox" type="text" id="creg_seccode" />
					<img class="security-code" src="tools/seccode.php" align="absmiddle">
				</div>
				<center><input type="button" class="button" value="<? echo $la['REGISTER']; ?>" onClick="connectRegister();"/></center>
			</div>
			<? }?>
		</div>
		
		<input type="button" class="button mobile-v float-left" value="<? echo $la['MOBILE_VERSION']; ?>" onclick="window.open('mobile/index.php', '_self');"/>
		 <select id="system_language" class="selectbox float-right" onChange="switchLanguageLogin();"><? echo getLanguageList(); ?></select> 
	</div>
</body>
</html>