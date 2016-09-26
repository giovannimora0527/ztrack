<?
	$postdata = file_get_contents("php://input");
	ob_start();
	echo "OK\n";
	header("Connection: close");
	header("Content-length: " . (string)ob_get_length());
	ob_end_flush();
	ob_flush(); 
	flush();
	
	chdir('../');
	include ('s_insert.php');
	
	$loc = array();
	$loc = (array)json_decode($postdata);
	$loc["op"] = "loc";
	execLoc($loc);
	
	function execLoc($loc)
	{
		if ($loc["op"] == "loc")
		{
			$loc['protocol'] = '2lemetry';
			$loc['dt_server'] = gmdate("Y-m-d H:i:s");
			$loc['params'] = paramsToArray($loc['params']);
			insert_db_loc($loc);
		}
	}

	mysqli_close($ms);
	die;
?>