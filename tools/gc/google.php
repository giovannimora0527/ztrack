<?
	if(@$_GET['cmd'] == 'latlng')
	{
		$result = '';
		
		$search = $_GET["lat"].','.$_GET["lng"];
		$search = htmlentities(urlencode($search));
		
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$search.'&sensor=false&oe=utf-8';
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if(is_array($jsondata) && $jsondata['status']=="OK")
		{
			$result = $jsondata['results'][0]['formatted_address'];
		}
		
		echo json_encode($result);
	}
	
	if(@$_GET['cmd'] == 'address')
	{
		$result = array();
		
		$search = htmlentities(urlencode($_GET["search"]));
		
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$search.'&sensor=false&oe=utf-8';
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if(is_array($jsondata) && $jsondata['status']=="OK")
		{
			for ($i=0; $i<count($jsondata['results']); $i++)
			{
				$address = $jsondata['results'][$i]['formatted_address'];
				$lat = $jsondata['results'][$i]['geometry']['location']['lat'];
				$lng = $jsondata['results'][$i]['geometry']['location']['lng'];
				
				$result[] = array('address' => $address, 'lat' => $lat, 'lng' => $lng);
			}
		}
		
		echo json_encode($result);
	}
?>