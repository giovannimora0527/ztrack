<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();

	if(@$_GET['file'] == 'logo')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'img/logo.png';
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}   
	}
	
	if(@$_GET['file'] == 'driver_photo')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/drivers/'.$_SESSION["user_id"].'_temp.png';
			$file_url = $gsValues['URL_ROOT'].'/data/user/drivers/'.$_SESSION["user_id"].'_temp.png';
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			echo $file_url;
		}
	}
	
	if(@$_GET['file'] == 'object_icon')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/objects/'.$_SESSION["user_id"].'_'.md5(gmdate("Y-m-d H:i:s")).'.png';
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}
	}
	
	if(@$_GET['file'] == 'places_icon')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/places/'.$_SESSION["user_id"].'_'.md5(gmdate("Y-m-d H:i:s")).'.png';
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}
	}
 ?>