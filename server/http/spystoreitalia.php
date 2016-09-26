<?
	ob_start();
	echo "OK";
	header("Connection: close");
	header("Content-length: " . (string)ob_get_length());
	ob_end_flush();
	
	if (!isset($_GET["imei"]))
	{
		die;
	}
	
	chdir('../');
	include ('s_insert.php');
	
	$loc = array();
	
	//?BATTERIA=83&IMEI=861001001311118&GPRMC=32,010203.000,A,3704.303730,N,1517.237951,E,0.00,0.00,010203,,E,A
	
	if (isset($_GET["IMEI"]))
	{
		$gprmc = $_GET["GPRMC"];
		$gprmc = explode(",", $gprmc);
		
		$gps_status = $gprmc[2]; //GPS STATUS (A - OK; V - NO)
		
		//dd.dddddd = (ddmm.mmmm \ 100) + ((ddmm.mmmm - ((ddmm.mmmm \ 100) * 100)) / 60)
		
		$loc['imei'] = $_GET["IMEI"];
		$loc['protocol'] = 'spystoreitalia';
		$loc['ip'] = '';
		$loc['port'] = '';
		$loc['dt_server'] = gmdate("Y-m-d H:i:s");
		
		$year = substr($gprmc[9], 4, 2);
		$month = substr($gprmc[9], 2, 2);
		$day = substr($gprmc[9], 0, 2);
		
		$hour = substr($gprmc[1], 0, 2);
		$min = substr($gprmc[1], 2, 2);
		$sec = substr($gprmc[1], 4, 2);
		
		$loc['dt_tracker'] = "20".$year."-".$month."-".$day." ".$hour.":".$min.":".$sec;
		
		$loc['lat'] = $gprmc[3];
		$loc['lat'] = floor(($loc['lat'] / 100)) + (($loc['lat'] - (floor(($loc['lat'] / 100)) * 100)) / 60);
		$loc['lat'] = sprintf("%01.6f", $loc['lat']);
		if ($gprmc[4] == "S") $loc['lat'] = $loc['lat'] * (-1);
		
		$loc['lng'] = $gprmc[5];
		$loc['lng'] = floor(($loc['lng'] / 100)) + (($loc['lng'] - (floor(($loc['lng'] / 100)) * 100)) / 60);
		$loc['lng']= sprintf("%01.6f", $loc['lng']);
		if ($gprmc[6] == "W") $loc['lng'] = $loc['lng'] * (-1);
		
		$loc['altitude'] = '0';
		$loc['angle'] = floor($gprmc[8]);
		$loc['speed'] = floor($gprmc[7] * 1.852);
		
		$batp = 'batp='.$_GET["BATTERIA"].'|';
		
		$loc['params'] = $batp;
		$loc['event'] = '';
		
		$loc['params'] = paramsToArray($loc['params']);
		
		if ($gps_status == 'A')
		{	
			$loc['loc_valid'] = '1';
		}
		else
		{
			$loc['loc_valid'] = '0';
		}
		
		insert_db_loc($loc);
	}
	
	mysqli_close($ms);
	die;
?>