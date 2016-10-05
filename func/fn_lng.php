<?php
	session_start();
	include ('../init.php');
	include ('fn_common.php');
        
        if(@$_POST['cmd'] == 'load_language')
	{
                if (isset($_SESSION["language"]))
                {
                    $lng = $_SESSION["language"];
                }
                else
                {
                    $lng = $gsValues['LANGUAGE'];
                }
		
		if (isset($_SESSION["units"]))
		{
			loadLanguage($lng, $_SESSION["units"]);
		}
		else
		{
			loadLanguage($lng);
		}
		
		echo json_encode($la);
		die;
	}
?>