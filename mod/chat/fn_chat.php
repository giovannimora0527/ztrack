<? 
	session_start();
	include ('../../init.php');
        
        if(@$_POST['cmd'] == 'send_msg')
	{
                $dt_server = gmdate("Y-m-d H:i:s");
                $imei = $_POST['imei'];
                $side = 'C';
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
	
	if(@$_POST['cmd'] == 'load_chat_data')
	{
		$result = array();
		$result['msg_count'] = array();
		$result['last_msg_status'] = false;
		
		$imei = $_POST['imei'];
		$last_msg_id = $_POST['last_msg_id'];
		
		// set messages to delivered
		$q = "UPDATE `gs_object_chat` SET `status`=1 WHERE `imei`='".$imei."' AND `side`='S' AND `status`=0";
		$r = mysqli_query($ms, $q);
		
		// get unread messages number
		$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' AND `side`='S' AND `status`!=2";
		$r = mysqli_query($ms, $q);
		$result['msg_count'][$imei] = mysqli_num_rows($r);
		
		// get last sent message status
		if (($imei != 'false') && ($last_msg_id != 'false'))
		{
			$q = "SELECT * FROM `gs_object_chat` WHERE `imei`='".$imei."' AND `msg_id`='".$last_msg_id."' AND `side`='C'";
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
		$q = "UPDATE `gs_object_chat` SET `status`=2 WHERE `imei`='".$imei."' AND `side`='S'";
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
			
                        $dt = $row['dt_server'];
			
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