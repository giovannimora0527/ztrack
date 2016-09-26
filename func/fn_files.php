<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();

	if (isset($_POST['path']))
	{
		$filter = false;
		
		if ($_POST['path'] == 'data/user/places')
		{
			$filter = $_SESSION['user_id'].'_';
		}
		
		if ($_POST['path'] == 'data/user/objects')
		{
			$filter = $_SESSION['user_id'].'_';
		}
		
		$path = $gsValues['PATH_ROOT'].$_POST['path'];
		$dh = opendir($path);
	    
		$result = array();
		    
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				if ($filter != false)
				{
					if (0 === strpos($file, $filter))
					{
						$result[] = $file;
					}
				}
				else
				{
					$result[] = $file;
				}
			}
		}
		
		closedir($dh);
		
		sort($result);
		
		echo json_encode($result);
		die;	
	}	
 ?>
 
 
