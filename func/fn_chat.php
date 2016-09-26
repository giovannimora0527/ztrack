<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	// check privileges
	if ($_SESSION["privileges"] == 'subuser')
	{
		$user_id = $_SESSION["manager_id"];
	}
	else
	{
		$user_id = $_SESSION["user_id"];
	}
        
        if(@$_POST['cmd'] == 'send_msg')
	{
                $dt_server = gmdate("Y-m-d H:i:s");
                $imei = $_POST['imei'];
                $side = 'S';
                $msg = $_POST['msg'];
		
		$q = 'UPDATE gs_objects SET `dt_chat`="'.$dt_server.'" WHERE imei="'.$imei.'"';
		$r = mysqli_query($ms, $q);
               
                $q = 'INSERT INTO gs_object_chat (dt_server,
                                                        imei,
                                                        side,
                                                        msg
                                                        ) VALUES (
                                                        "'.$dt_server.'",
                                                        "'.$imei.'",
                                                        "'.$side.'",
                                                        "'.$msg.'")';
                                    
                $r = mysqli_query($ms, $q);
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['cmd'] == 'delete_all_msgs')
	{
		$imei = $_POST['imei'];
		
		$q = "DELETE FROM `gs_object_chat` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = 'UPDATE gs_objects SET `dt_chat`="" WHERE imei="'.$imei.'"';
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_chat_data')
	{
		$result = array();
		$result['msg_count'] = array();
		$result['msg_dt'] = array();
		$result['last_msg_status'] = false;
		
		$imei = $_POST['imei'];
		$last_msg_id = $_POST['last_msg_id'];
		
		// set messages to delivered
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "UPDATE `gs_object_chat` SET `status`=1 WHERE `imei` IN (".$_SESSION["privileges_imei"].") AND `side`='C' AND `status`=0";
		}
		else
		{
			$q = "UPDATE `gs_object_chat` SET `status`=1 WHERE `imei` IN (".getUserObjectIMEIs($user_id).") AND `side`='C' AND `status`=0";
		}
		
		$r = mysqli_query($ms, $q);
		
		// get unread messages number
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei` IN (".$_SESSION["privileges_imei"].") AND `side`='C' AND `status`!=2";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") AND `side`='C' AND `status`!=2";
		}
		
		$r = mysqli_query($ms, $q);
		
		if ($r)
		{
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				if (!isset($result['msg_count'][$row['imei']]))
				{
					$result['msg_count'][$row['imei']] = 0;
				}
				
				$result['msg_count'][$row['imei']] += 1;
			}	
		}
		
		// get last messages datetime
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_objects` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_objects` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		$r = mysqli_query($ms, $q);
		
		if ($r)
		{
			while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				if (strtotime($row['dt_chat']) > 0)
				{
					$dt = convUserTimezone($row['dt_chat']);
					$result['msg_dt'][$row['imei']] = $dt;
				}
				else
				{
					$result['msg_dt'][$row['imei']] = '';
				}
			}	
		}
		
		// get last sent message status
		if (($imei != 'false') && ($last_msg_id != 'false'))
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' AND `msg_id`='".$last_msg_id."' AND `side`='S'";
			$r = mysqli_query($ms, $q);
			if($row = mysqli_fetch_array($r,MYSQL_ASSOC))
			{
				$result['last_msg_status'] = $row['status'];
			}
		}
		
		echo json_encode($result);
		die;
	}
        
        if(@$_POST['cmd'] == 'load_msgs')
	{
		$result = array();
		
                $imei = $_POST['imei'];
		$type = $_POST['type'];
		$msg_limit = $_POST['msg_limit'];
		$first_msg_id = $_POST['first_msg_id'];
		$last_msg_id = $_POST['last_msg_id'];
		
		// set messages to seen
		$q = "UPDATE `gs_object_chat` SET `status`=2 WHERE `imei`='".$imei."' AND `side`='C'";
		$r = mysqli_query($ms, $q);
		
		// get messages
		if ($type == 'select')
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' ORDER BY `msg_id` desc LIMIT ".$msg_limit;
		}
		else if ($type == 'old')
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' AND `msg_id`<'".$first_msg_id."' ORDER BY `msg_id` desc LIMIT ".$msg_limit;
		}
		else if ($type == 'new')
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' AND `msg_id`>'".$last_msg_id."' ORDER BY `msg_id` desc";
		}
		
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r,MYSQL_ASSOC))
		{
			$msg_id = $row['msg_id'];
			
			$dt = convUserTimezone($row['dt_server']);
			
                        $result[$msg_id] = array(	'dt' => $dt,
							's' => $row['side'],
							'm' => $row['msg'],
							'st' => $row['status']
							);
		}
		
		echo json_encode($result);
		die;
        }
?>