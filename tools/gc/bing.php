<?
	$gsValues = array();
	include ('../../config.custom.php');
	
	if(@$_GET['cmd'] == 'latlng')
	{
		$result = '';
		
		$search = $_GET["lat"].','.$_GET["lng"];
		$search = htmlentities(urlencode($search));
		
		$url = 'http://dev.virtualearth.net/REST/v1/Locations/'.$search.'?o=json&key='.$gsValues['MAP_BING_KEY'];
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if(is_array($jsondata) && $jsondata['statusCode'] == 200)
		{
			if (count($jsondata['resourceSets']['0']['resources']) > 0)
			{
				$result = $jsondata['resourceSets']['0']['resources']['0']['name'];
			}
		}
		
		echo json_encode($result);
	}
	
	if(@$_GET['cmd'] == 'address')
	{
		$result = array();
		
		$search = htmlentities(urlencode($_GET["search"]));
		
		$url = 'http://dev.virtualearth.net/REST/v1/Locations?query='.$search.'&key='.$gsValues['MAP_BING_KEY'];
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if(is_array($jsondata) && $jsondata['statusCode'] == 200)
		{
			if (count($jsondata['resourceSets']['0']['resources']) > 0)
			{
				$address = $jsondata['resourceSets']['0']['resources']['0']['name'];
				$lat = $jsondata['resourceSets']['0']['resources']['0']['point']['coordinates'][0];
				$lng = $jsondata['resourceSets']['0']['resources']['0']['point']['coordinates'][1];
				
				$result[] = array('address' => $address, 'lat' => $lat, 'lng' => $lng);
			}
		}
		
		echo json_encode($result);
	}
?>