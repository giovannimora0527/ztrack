<?
	if(empty($_POST['filename']) || empty($_POST['content'])){
		die;
	}
		
	if(@$_GET['format'] == 'html')
	{
		$filename = preg_replace('/[^a-z0-9\_\.]/i','_',$_POST['filename'].'.html');
		//$filename = $_POST['filename'];
		
		header('Content-type: text/html');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo base64_decode(stripslashes($_POST['content']));
	}
	
	if(@$_GET['format'] == 'pdf')
	{
		$filename = preg_replace('/[^a-z0-9\_\.]/i','_',$_POST['filename'].'.pdf');
		//$filename = $_POST['filename'];
		
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Pragma: no-cache");
		header("Expires: 0");
				
		echo base64_decode(stripslashes($_POST['content']));
	}
	
	if(@$_GET['format'] == 'xls')
	{
		$filename = preg_replace('/[^a-z0-9\_\.]/i','_',$_POST['filename'].'.xls');
		//$filename = $_POST['filename'];
		
		header("Content-type: application/x-msdownload"); 
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo base64_decode(stripslashes($_POST['content']));
	}
?>