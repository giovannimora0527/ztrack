<?	
	function geocoderGetAddress($lat, $lng)
	{
		global $ms, $gsValues;
		
		$result = '';
		
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
		
		return $result;
	}
?>