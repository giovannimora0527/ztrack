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
	
	$loc['imei'] = $_GET["username"];
	$loc['protocol'] = 'http_insert';
	$loc['ip'] = '';
	$loc['port'] = '';
	$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
	$loc['dt_server'] = gmdate("Y-m-d H:i:s");
	$loc['lat'] = $_GET["latitude"];
	$loc['lng'] = $_GET["longitude"];
	$loc['altitude'] = '0';
	$loc['angle'] = '0';
	$loc['speed'] = $_GET["speed"];
	$loc['loc_valid'] = '1';
	$loc['params'] = '';
	$loc['event'] = '';
	
	$loc['params'] = paramsToArray($loc['params']);
	
	insert_db_loc($loc);
	
	mysqli_close($ms);
	die;
?>