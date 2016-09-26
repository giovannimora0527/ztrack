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
	
	$gps_status = $_GET['status']; //GPS STATUS (A - OK; V - NO)
	
	$loc['imei'] = $_GET["imei"];
	$loc['protocol'] = 'aspicore';
	$loc['ip'] = '';
	$loc['port'] = '';
	$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
	$loc['dt_server'] = gmdate("Y-m-d H:i:s");
	$loc['lat'] = $_GET["lat"];
	$loc['lng'] = $_GET["lon"];
	$loc['altitude'] = '0';
	$loc['angle'] = '0';
	$loc['speed'] = floor($_GET["speed"] * 3.6);
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
	
	mysqli_close($ms);
	die;
?>