<?
	$gsValues = array();
	include ('../../config.custom.php');
	
	if(@$_GET['cmd'] == 'latlng')
	{
		$result = '';
		
                $lat = $_GET["lat"];
		$lng = $_GET["lng"];
		
                $url = 'http://nominatim.openstreetmap.org/reverse?email='.$gsValues['EMAIL'].'&format=json&lat='.$lat.'&lon='.$lng.'&zoom=18&addressdetails=1';
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		$result = $jsondata['display_name'];
		
//		if (isset($jsondata['address']['house_number']))
//                {
//                    $result .= $jsondata['address']['house_number'].', ';
//                }
//                
//                if (isset($jsondata['address']['road']))
//                {
//                    $result .= $jsondata['address']['road'].', ';
//                }
//                
//                if (isset($jsondata['address']['city']))
//                {
//                    $result .= $jsondata['address']['city'].', ';
//                }
//                
//                if (isset($jsondata['address']['"state_district']))
//                {
//                    $result .= $jsondata['address']['"state_district'].', ';
//                }
//                
//                if (isset($jsondata['address']['postcode']))
//                {
//                    $result .= $jsondata['address']['postcode'].', ';
//                }
//                
//                if (isset($jsondata['address']['country_code']))
//                {
//                    $result .= strtoupper($jsondata['address']['country_code']).', ';
//                }
//                
//                $result = substr($result, 0, -2);
                
		echo json_encode($result);
	}
	
	if(@$_GET['cmd'] == 'address')
	{
		$result = array();
		
		$search = htmlentities(urlencode($_GET["search"]));
		
                $url = 'http://nominatim.openstreetmap.org/search?q='.$search.'&format=json&polygon=0&addressdetails=1';
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
                
		if(is_array($jsondata))
		{
			for ($i=0; $i<count($jsondata); $i++)
			{
				$address = '';
                                
                                if (isset($jsondata[$i]['address']['house_number']))
                                {
                                    $address .= $jsondata[$i]['address']['house_number'].', ';
                                }
                                
                                if (isset($jsondata[$i]['address']['road']))
                                {
                                    $address .= $jsondata[$i]['address']['road'].', ';
                                }
                                
                                if (isset($jsondata[$i]['address']['city']))
                                {
                                    $address .= $jsondata[$i]['address']['city'].', ';
                                }
                                
                                if (isset($jsondata[$i]['address']['"state_district']))
                                {
                                    $address .= $jsondata[$i]['address']['"state_district'].', ';
                                }
                                
                                if (isset($jsondata[$i]['address']['postcode']))
                                {
                                    $address .= $jsondata[$i]['address']['postcode'].', ';
                                }
                                
                                if (isset($jsondata[$i]['address']['country_code']))
                                {
                                    $address .= strtoupper($jsondata[$i]['address']['country_code']).', ';
                                }
                                
                                $address = substr($address, 0, -2);
                                
                                $lat = $jsondata[$i]['lat'];
				$lng = $jsondata[$i]['lon'];
				
				$result[] = array('address' => $address, 'lat' => $lat, 'lng' => $lng);
			}
		}
		
		echo json_encode($result);
	}
?>