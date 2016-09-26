<?
        chdir('../');
        include ('s_insert.php');
	
	// needed for older than PHP 5.4 version
	if ( !function_exists( 'hex2bin' ) )
	{
		function hex2bin( $str ) {
		    $sbin = "";
		    $len = strlen( $str );
		    for ( $i = 0; $i < $len; $i += 2 ) {
			$sbin .= pack( "H*", substr( $str, $i, 2 ) );
		    }
	    
		    return $sbin;
		}
	}
	
	if (@$_POST["op"] == "img_loc")
        {
		ob_start();
		echo "OK";
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
	
		$loc = array();
		
		$imei = $_POST['imei'];
		
		// check if object exists in system
		if (!checkObjectExistsSystem($imei))
		{
			return false;
		}
		
                $loc['dt_server'] = gmdate("Y-m-d H:i:s");
		$loc['dt_tracker'] = $_POST["dt_tracker"];
		$loc['lat'] = $_POST["lat"];
		$loc['lng'] = $_POST["lng"];
		$loc['altitude'] = $_POST["altitude"];
		$loc['angle'] = $_POST["angle"];
		$loc['speed'] = $_POST["speed"];
		$loc['params'] = $_POST["params"];
                
                $img_file = $imei.'_'.$loc['dt_tracker'].'.jpg';
                $img_file = str_replace('-', '', $img_file);
                $img_file = str_replace(':', '', $img_file);
                $img_file = str_replace(' ', '_', $img_file);
                
                // save to database
                $q = "INSERT INTO gs_object_img (img_file,
                                                imei,
                                                dt_server,
                                                dt_tracker,
                                                lat,
                                                lng,
                                                altitude,
                                                angle,
                                                speed,
                                                params
                                                ) VALUES (
                                                '".$img_file."',
                                                '".$imei."',
                                                '".$loc['dt_server']."',
                                                '".$loc['dt_tracker']."',
                                                '".$loc['lat']."',
                                                '".$loc['lng']."',
                                                '".$loc['altitude']."',
                                                '".$loc['angle']."',
                                                '".$loc['speed']."',
                                                '".json_encode($loc['params'])."')";
                                    
                $r = mysqli_query($ms, $q);
		
		 // save file
                $img_path = $gsValues['PATH_ROOT'].'/data/img/';
                $img_path = $img_path.basename($img_file);
                
		$postdata =  $_POST["img"];
		$postdata = hex2bin($postdata);
		                
                if(substr($postdata,0,3) == "\xFF\xD8\xFF")
                {
                        $fp = fopen($img_path,"w");
                        fwrite($fp,$postdata);
                        fclose($fp);
                } 
	}
        
        if ((@$_GET["op"] == "img") && (@$_GET['imei'] != ''))
        {
		$loc = array();
		
		$imei = $_GET['imei'];
		
		// check if object exists in system
		if (!checkObjectExistsSystem($imei))
		{
			return false;
		}
		
                // get previous known location
		$loc = get_gs_objects_data($imei);
                
		if (!$loc) {die;}
		
                $loc['dt_server'] = gmdate("Y-m-d H:i:s");
                
                $img_file = $imei.'_'.$loc['dt_server'].'.jpg';
                $img_file = str_replace('-', '', $img_file);
                $img_file = str_replace(':', '', $img_file);
                $img_file = str_replace(' ', '_', $img_file);
                
                // save to database
                $q = "INSERT INTO gs_object_img (img_file,
                                                imei,
                                                dt_server,
                                                dt_tracker,
                                                lat,
                                                lng,
                                                altitude,
                                                angle,
                                                speed,
                                                params
                                                ) VALUES (
                                                '".$img_file."',
                                                '".$imei."',
                                                '".$loc['dt_server']."',
                                                '".$loc['dt_tracker']."',
                                                '".$loc['lat']."',
                                                '".$loc['lng']."',
                                                '".$loc['altitude']."',
                                                '".$loc['angle']."',
                                                '".$loc['speed']."',
                                                '".json_encode($loc['params'])."')";
                                    
                $r = mysqli_query($ms, $q);
                
                // save file
                $img_path = $gsValues['PATH_ROOT'].'/data/img/';
                $img_path = $img_path.basename($img_file);
                
                $postdata = file_get_contents('php://input', 'r');
                
                if(substr($postdata,0,3) == "\xFF\xD8\xFF")
                {
                        $fp = fopen($img_path,"w");
                        fwrite($fp,$postdata);
                        fclose($fp);
                } 
        }   
?>