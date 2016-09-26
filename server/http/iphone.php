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
	
	//$file = 'iph.txt';
	//$imei = $_GET["imei"]."\n";
	//file_put_contents($file, $imei, FILE_APPEND | LOCK_EX);

	chdir('../');
	include ('s_insert.php');
	
	$loc = array();
	
	If (@$_GET["op"] == "gpsok")
	{
		$loc['imei'] = $_GET["imei"];
		$loc['protocol'] = 'iphone';
		$loc['ip'] = '';
		$loc['port'] = '';
		$loc['dt_server'] = gmdate("Y-m-d H:i:s");
		$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
		
		if (@$_GET["dt"] != "")
		{
			$loc['dt_tracker'] = $_GET["dt"];
		}
		
		$loc['lat'] = $_GET["lat"];
		$loc['lng'] = $_GET["lng"];
		$loc['altitude'] = $_GET["altitude"];
		$loc['angle'] = $_GET["angle"];
		$loc['speed'] = $_GET["speed"];
		$loc['loc_valid'] = '1';
		$loc['params'] = @$_GET["params"];
		$loc['event'] = @$_GET["event"];
		
		$loc['params'] = paramsToArray($loc['params']);
		
		insert_db_loc($loc);
	}
	
	If (@$_GET["op"] == "gpsno")
	{		
		$loc['imei'] = $_GET["imei"];
		$loc['protocol'] = 'iphone';
		$loc['ip'] = '';
		$loc['port'] = '';
		$loc['dt_server'] = gmdate("Y-m-d H:i:s");
		$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
		$loc['lat'] = '0';
		$loc['lng'] = '0';
		$loc['altitude'] = '0';
		$loc['angle'] = '0';
		$loc['speed'] = '0';
		$loc['loc_valid'] = '0';
		$loc['params'] = @$_GET["params"];
		$loc['event'] = @$_GET["event"];
		
		$loc['params'] = paramsToArray($loc['params']);
		
		insert_db_loc($loc);
	}
	
	mysqli_close($ms);
	die;
?>