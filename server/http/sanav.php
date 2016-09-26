<?
	header("Connection: close");
	header("Content-length: 0");
	flush();
	
	chdir('../');
	include ('s_insert.php');
	
	$loc = array();
	
	if (isset($_GET["imei"]))
	{
		$rmc = $_GET["rmc"];
		$gprmc = explode(",", $rmc);
		
		$gps_status = $gprmc[2]; //GPS STATUS (A - OK; V - NO)
		
		//dd.dddddd = (ddmm.mmmm \ 100) + ((ddmm.mmmm - ((ddmm.mmmm \ 100) * 100)) / 60)
		
		$loc['imei'] = $_GET["imei"];
		$loc['protocol'] = 'sanav';
		$loc['ip'] = '';
		$loc['port'] = '';
		$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
		$loc['dt_server'] = gmdate("Y-m-d H:i:s");
		
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
		$loc['params'] = '';
		$loc['event'] = '';
		
		$loc['params'] = paramsToArray($loc['params']);
		
		if ($gps_status == "A")
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