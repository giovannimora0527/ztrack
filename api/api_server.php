<?
        if (@$api_access != true) { die; }
        
        // split command and params
        $cmd = explode(',', $cmd);
        $command = @$cmd[0];
        $command = strtoupper($command);
        
        if ($command == 'ADD_USER')
        {
                loadLanguage('english');
                
                // command validation
                if (count($cmd) < 2) { die; }
                
                // command parameters
                $email = strtolower($cmd[1]);
                
                $privileges = array();
		$privileges['type'] = 'user';
                $privileges['history'] = (bool)$gsValues['HISTORY'];
                $privileges['reports'] = (bool)$gsValues['REPORTS'];
                $privileges['rilogbook'] = (bool)$gsValues['RILOGBOOK'];
                $privileges['object_control'] = (bool)$gsValues['OBJECT_CONTROL'];
                $privileges['image_gallery'] = (bool)$gsValues['IMAGE_GALLERY'];
                $privileges['chat'] = (bool)$gsValues['CHAT'];
		$privileges = json_encode($privileges);
                
                addUser('true', 'true', 'false', '', $privileges, '', $email, '', $gsValues['OBJ_HISTORY_CLEAR'], $gsValues['OBJ_EDIT'], $gsValues['OBJ_ADD'], $gsValues['OBJ_NUM'], $gsValues['OBJ_DT']);
        }
        
        if ($command == 'DEL_USER')
        {
                // command validation
                if (count($cmd) < 2) { die; }
                
                // command parameters
                $email = strtolower($cmd[1]);
                
                // get user id from email
                $user_id = getUserIdFromEmail($email);
                
                // delete user
                delUser($user_id);
        }
        
        if ($command == 'ADD_OBJECT')
        {
                // command validation
                if (count($cmd) < 4) { die; }
                
                // command parameters
                $imei = strtoupper($cmd[1]);
                $name = $cmd[2];
                $active_dt = $cmd[3];
                
                // add object
                addObjectSystem($name, $imei, 'true', $active_dt, '0');
                createObjectDataTable($imei);
        }
        
        if ($command == 'DEL_OBJECT')
        {
                // command validation
                if (count($cmd) < 2) { die; }
                
                // command parameters
                $imei = strtoupper($cmd[1]);
                
                // delete object
                delObjectSystem($imei);
        }
        
        if ($command == 'ADD_USER_OBJECT')
        {
                // command validation
                if (count($cmd) < 3) { die; }
                
                // command parameters
                $email = strtolower($cmd[1]);
                $imei = strtoupper($cmd[2]);
                
                // get user id from email
                $user_id = getUserIdFromEmail($email);
                
                // add object to user
                addObjectUser($user_id, $imei, 0, 0, 0);    
        }
        
        if ($command == 'DEL_USER_OBJECT')
        {
                // command validation
                if (count($cmd) < 3) { die; }
                
                // command parameters
                $email = strtolower($cmd[1]);
                $imei = strtoupper($cmd[2]);
                
                // get user id from email
                $user_id = getUserIdFromEmail($email);
                
                // delete object from user
                delObjectUser($user_id, $imei);
        }
        
        if ($command == 'OBJECT_SET_ACTIVITY')
        {
                // command validation
                if (count($cmd) < 4) { die; }
                
                // command parameters
                $imei = strtoupper($cmd[1]);
                $active = strtolower($cmd[2]);
                $active_dt = $cmd[3];
                
                // command exec               
                if ($active == 'true')
                {                        
                        $q = "UPDATE `gs_objects` SET `active`='true', `active_dt`='$active_dt' WHERE `imei`='".$imei."'";
                }
                else if ($active == 'false')
                {
                        $q = "UPDATE `gs_objects` SET `active`='false', `active_dt`='$active_dt' WHERE `imei`='".$imei."'";
                }
                $r = mysqli_query($ms, $q);
        }
        
        die;
?>