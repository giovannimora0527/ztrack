<?
        function sendSMSHTTP($gateway, $filter, $number, $message)
        {
                global $ms;
                
                if (($gateway != '') && ($number != '') && ($message != ''))
                {
                        // multiple phone numbers
                        $numbers = explode(",", $number);
                        
                        // fitler array
                        if ($filter != '')
                        {
                                $filters = explode(",", $filter);
                        }
                        
                        for ($i = 0; $i < count($numbers); ++$i)
                        {
                                if ($i > 4)
                                {
                                        break;
                                }
                                
                                $number = trim($numbers[$i]);
                                
                                //IMPORTANT
                                $number_encoded = urlencode($number);
                                $message_encoded = urlencode($message);
                                //IMPORTANT
                                
                                $url = str_replace("%NUMBER%", $number_encoded, $gateway);
                                $url = str_replace("%MESSAGE%", $message_encoded, $url);
                                
                                sleep(1);
                                
                                if (isset($filters))
                                {
                                        foreach($filters as $value)
                                        {
                                                if(strpos($number, $value) !== false)
                                                {
                                                        $result = @file_get_contents($url);
                                                }
                                        }        
                                }
                                else
                                {
                                        $result = @file_get_contents($url);     
                                }                           
                        }
                        
                        return true;
                }
                else
                {
                        return false;
                }
        }
        
        function sendSMSAPP($identifier, $filter, $number, $message)
        {
                global $ms;
                
                if (($identifier != '') && ($number != '') && ($message != ''))
                {
                        $message = substr($message, 0, 160);
                        
                        // multiple phone numbers
                        $numbers = explode(",", $number);
                        
                        // fitler array
                        if ($filter != '')
                        {
                                $filters = explode(",", $filter);
                        }
                        
                        for ($i = 0; $i < count($numbers); ++$i)
                        {
                                if ($i > 4)
                                {
                                        break;
                                }
                                
                                $number = trim($numbers[$i]);
                                
                                $dt_server = gmdate("Y-m-d H:i:s");
                                
                                if (isset($filters))
                                {
                                        foreach($filters as $value)
                                        {
                                                if(strpos($number, $value) !== false)
                                                {
                                                        $q = "INSERT INTO `gs_sms_gateway_app`(`dt_server`,`identifier`,`number`,`message`) VALUES ('".$dt_server."','".$identifier."','".$number."','".$message."')";
                                                        $r = mysqli_query($ms, $q);  
                                                }
                                        }        
                                }
                                else
                                {
                                        $q = "INSERT INTO `gs_sms_gateway_app`(`dt_server`,`identifier`,`number`,`message`) VALUES ('".$dt_server."','".$identifier."','".$number."','".$message."')";
                                        $r = mysqli_query($ms, $q);        
                                }
                        }
                        
                        return true;
                }
                else
                {
                        return false;
                }
        }
        
        function getSMSAPPTotalInQueue($identifier)
        {
                global $ms;
                
                $q = "SELECT * FROM `gs_sms_gateway_app` WHERE `identifier`='".$identifier."'";
		$r = mysqli_query($ms, $q);
                
                $count = mysqli_num_rows($r);
                
                return $count;
        }
        
        function clearSMSAPPQueue($identifier)
        {
                global $ms;
                
                $q = "DELETE FROM `gs_sms_gateway_app` WHERE `identifier`='".$identifier."'";
		$r = mysqli_query($ms, $q);
        }
?>