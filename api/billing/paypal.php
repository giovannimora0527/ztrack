<?
	include ('../../init.php');
	
	$debug = false;
        $paypalmode = 'sandbox';
	$paypalmode = '';
	
	// debug
	if ($debug == true)
	{
		$file = gmdate("YmdHis").'.txt';
		$handle = fopen($file, 'w');
		fwrite($handle, file_get_contents('php://input'));
		fclose($handle);	
	}
        
        if($_POST)
        {
                if($paypalmode == 'sandbox')
                {
                    $paypalmode = '.sandbox';
                }
		
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval)
		{
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
			$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc'))
		{
			$get_magic_quotes_exists = true;
		}
		
		foreach ($myPost as $key => $value)
		{
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
			{
				$value = urlencode(stripslashes($value));
			}
			else
			{
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		
		$ch = curl_init('https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		
		if( !($res = curl_exec($ch)) )
		{
			curl_close($ch);
			exit;
		}
		curl_close($ch);
		
		// debug
		if ($debug == true)
		{
			$file = 'res_'.gmdate("YmdHis").'.txt';
			$handle = fopen($file, 'w');
			fwrite($handle, $res);
			fclose($handle);	
		}
                
                if (strcmp ($res, "VERIFIED") == 0)
                {
			// prepare data
                        $paymentstatus = $_POST['payment_status'];
                        $total = $_POST['mc_gross'];
			$custom = $_POST['custom'];
			// end prepare data
			
			// check if completed
                        if ($paymentstatus != "Completed")
                        {
				die;
			}
			
			// check if not negative price
			if ($total <= 0)
                        {
				die;
			}
			
			// check for filter params
			$custom = explode(',', $custom);
			if (count($custom) == 0)
			{
				die;
			}
			
			if ($gsValues['PAYMENT_PAYPAL_CUSTOM'] == '')
			{
				die;
			}
			
			if ($custom[0] == $gsValues['PAYMENT_PAYPAL_CUSTOM'])
			{				
				$api_url = $gsValues['URL_ROOT'].'/api/api.php';
				$api_key = $gsValues['HW_KEY'];
				
				if (($api_url != '') && ($api_key != ''))
				{
					$imei = $custom[1];
					
					$active_dt = gmdate("Y-m-d");
					$active_dt = date("Y-m-d", strtotime($active_dt.' + 12 months'));
					
					// call GPS server API url with hw key to activate object for 1 year
					$url = $api_url.'?api=server&ver=1.0&key='.$api_key.'&cmd=OBJECT_SET_ACTIVITY,'.$imei.',true,'.$active_dt;
					file_get_contents($url, false, null);	
				}
			}
		}
        }
?>