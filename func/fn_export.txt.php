<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();

	$imei = '111111111111111';
	
	$dtf = '2015-01-01';
	$dtt = '2020-01-01';

	if (substr($_SESSION["timezone"],0,1) == "+")
	{
		$timezone_diff = str_replace("+", "-", $_SESSION["timezone"]);
	}
	else
	{
		$timezone_diff = str_replace("-", "+", $_SESSION["timezone"]);
	}
	$dtf = date("Y-m-d H:i:s", strtotime($dtf.$timezone_diff));
	$dtt = date("Y-m-d H:i:s", strtotime($dtt.$timezone_diff));
	
	$q = "SELECT DISTINCT 	dt_tracker,
				lat,
				lng,
				altitude,
				angle,
				speed,
				params
				FROM `gs_object_data_".$imei."` WHERE dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
				
	$r = mysqli_query($ms, $q);

	while($route_data=mysqli_fetch_array($r,MYSQL_ASSOC))
	{
		$dt_tracker = date("Y-m-d H:i:s", strtotime($route_data['dt_tracker'].$_SESSION["timezone"]));
		$lat = $route_data['lat'];
		$lng = $route_data['lng'];
		$altitude = $route_data['altitude'];
		$angle = $route_data['angle'];
		$speed = $route_data['speed'];
		$route_data['params'] = paramsToArray($route_data['params']);		
		$params = paramsToStr($route_data['params']);
		
		if (($lat != 0) || ($lng != 0))
		{
			echo $dt_tracker.','.$lat.','.$lng.','.$altitude.','.$angle.','.$speed.','.$params.',#<br/>';
		}
	}
	
	function paramsToStr($params)
	{
		$arr_params = array();
		
		foreach ($params as $key => $value)
		{
			array_push($arr_params, $key.'='.$value);
		}
		
		$result = implode('|', $arr_params);
		
		if ($result != '')
		{
			$result.="|";
		}
		
		return $result;
	}
?>