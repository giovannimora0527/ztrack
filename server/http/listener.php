<?
	//$_POST['imei'] - device 15 char ID
	//$_POST['dt_tracker'] - 0 UTC date and time in "YYYY-MM-DD HH-MM-SS" format
	//$_POST['lat'] - latitude with +/-
	//$_POST['lng'] - logitude with +/-
	//$_POST['altitude'] - in meters
	//$_POST['angle'] - in degree
	//$_POST['speed'] - in km/h
	//$_POST['loc_valid'] - 1 means valid location, 0 means not valid location
	//$_POST['params'] - stores array of params like acc, di, do, ai...
	//$_POST['event'] - possible events: sos, bracon, bracoff, mandown, shock, tow, haccel, hbrake, hcorn, pwrcut, gpscut, lowdc, lowbat, jamming

	ob_start();
	echo "OK";
	//header("Connection: close");
	header("Content-length: " . (string)ob_get_length());
	ob_end_flush();
	
	chdir('../');
	include ('s_insert.php');
	
	// check if data comes packaged
	if (isset($_POST["data"]))
	{
		$_POST["data"] = preg_replace( '/'.chr(31).'/', '&', $_POST["data"]);
		
		$loc_arr = array();
		$loc_arr = explode(chr(30), $_POST["data"]);
		
		foreach ($loc_arr as $key=>$loc_str)
		{
			parse_str($loc_str, $loc);
			execLoc($loc);
		}
	}
	else
	{
		execLoc($_POST);
	}
	
	function execLoc($loc)
	{
		if (@$loc["op"] == "loc")
		{
			$loc['protocol'] = @$loc['protocol'];
			$loc['ip'] = @$loc['ip'];			
			$loc['port'] = @$loc['port'];
			$loc['dt_server'] = gmdate("Y-m-d H:i:s");
			$loc['params'] = paramsToArray($loc['params']);
			
			insert_db_loc($loc);
		}
		
		if (@$loc["op"] == "noloc")
		{
			$loc['protocol'] = @$loc['protocol'];
			$loc['ip'] = @$loc['ip'];			
			$loc['port'] = @$loc['port'];
			$loc['dt_server'] = gmdate("Y-m-d H:i:s");
			$loc['params'] = paramsToArray($loc['params']);
			
			insert_db_noloc($loc);
		}
		
		if (@$loc["op"] == "rfid_swipe")
		{
			$swipe['imei'] = $loc["imei"];
			$swipe['dt_server'] = gmdate("Y-m-d H:i:s");
			$swipe['dt_swipe'] = $loc["dt_tracker"];
			$swipe['lat'] = $loc["lat"];
			$swipe['lng'] = $loc["lng"];
			$swipe['rfid'] = $loc["rfid"];
			
			insert_db_rfid_swipe($swipe);
		}
	}

	mysqli_close($ms);
	die;
?>