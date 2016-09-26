<?
	include ('../init.php');
	include ('../func/fn_common.php');
	
	if(@$_POST['cmd'] == 'latlng')
	{
		$result = '';
		
		$lat = $_POST["lat"];
		$lng = $_POST["lng"];
		
		if ($gsValues['GEOCODER_CACHE'] == 'true')
		{
			$result = getGeocoderCache($lat, $lng);
		}
		
		if ($result == '')
		{
			for ($i=0; $i<count($gsValues['URL_GC']); ++$i)
			{
				$url = $gsValues['URL_GC'][$i].'?cmd=latlng&lat='.$lat.'&lng='.$lng;
				$result = @file_get_contents($url);				
				$result = json_decode($result);
				
				if (($result != '') && ($result != '""'))
				{
					break;
				}
			}
			
			if ($gsValues['GEOCODER_CACHE'] == 'true')
			{
				insertGeocoderCache($lat, $lng, $result);
			}
		}
		
		echo json_encode($result);
	}
	
	if(@$_POST['cmd'] == 'address')
	{
		$result = '';
		$search = htmlentities(urlencode($_POST["search"]));
		
		for ($i=0; $i<count($gsValues['URL_GC']); ++$i)
		{
			$url = $gsValues['URL_GC'][$i].'?cmd=address&search='.$search;
			$result = @file_get_contents($url);
			
			if ($result != '[]')
			{
				$result = json_decode($result);
				break;
			}
		}
		
		echo json_encode($result);
	}
?>