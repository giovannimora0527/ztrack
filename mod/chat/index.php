<?
	session_start();
	include ('../../init.php');
	include ('../../func/fn_common.php');
	
	if (isset($_GET['imei']))
	{
		$imei = $_GET['imei'];
		if (!checkObjectExistsSystem($imei))
		{
			die;
		}
	}
	else
	{
		die;
	}
?>

<!DOCTYPE html> 
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Chat</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scaleable=0">
		<meta name="HandheldFriendly" content="True" />
		
		<link type="text/css" href="style.css" rel="Stylesheet" />
		
		<script type="text/javascript" src="../../js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="../../js/moment.min.js"></script>
		<script type="text/javascript" src="chat.js?v=<? echo gmdate("Y-m-d H:i:s"); ?>"></script>
		
		<script>chatData['imei'] = <? echo $imei; ?>;</script>
	</head>
	
	<body onload="load()">
		<div class="chat-msgs-block">
			<div id="chat_msgs_dt"></div>
			<div id="chat_msgs">
				<div id="chat_msgs_text"></div>
				<div class="chat-msg-status" id="chat_msg_status"></div>
			</div>
		</div>
		
		<div class="chat-msg-block">
			<div class="text-input">
				<input id="chat_msg" class="inputbox" type="text" value="" placeholder="Type a message..." maxlength="500" onkeydown="if (event.keyCode == 13) chatSend();">
			</div>
			<div class="send-btn">
				<a class="send-message" href="javascript:chatSend();"><img src="img/send_icon.png" width="28" height="28"/></a>
			</div>
		</div>
	</body>
</html>