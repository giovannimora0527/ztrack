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

	chdir('../server');
	include ('s_insert.php');
	
	$loc = array();
	
	$loc['imei'] = $_GET["imei"];
	$loc['protocol'] = 'api_loc';
	$loc['ip'] = '';
	$loc['port'] = '';
	$loc['dt_server'] = gmdate("Y-m-d H:i:s");
	$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
	
	if (@$_GET["dt"] != "")
	{
		$loc['dt_tracker'] = $_GET["dt"];
	}	
	
	$loc['lat'] = @$_GET["lat"];
	$loc['lng'] = @$_GET["lng"];
	$loc['altitude'] = @$_GET["altitude"];
	$loc['angle'] = @$_GET["angle"];
	$loc['speed'] = @$_GET["speed"];
	$loc['loc_valid'] = @$_GET["loc_valid"];
	$loc['params'] = @$_GET["params"];
	$loc['event'] = @$_GET["event"];
	
	$loc['params'] = paramsToArray(@$loc['params']);
	
	insert_db_loc($loc);
	
	mysqli_close($ms);
	die;
?>