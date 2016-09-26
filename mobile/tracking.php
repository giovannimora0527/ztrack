<?	
	session_start();
	include ('../init.php');
	include ('../func/fn_common.php');
	checkUserSession();
        
	setUserSessionSettings($_SESSION["user_id"]);
	loadLanguage($_SESSION['language'], $_SESSION["units"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<? generatorTag(); ?>
        <title><? echo $gsValues['NAME'].' '.$gsValues['VERSION']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	
	<link type="text/css" href="../theme/leaflet/leaflet.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/leaflet/leaflet.label.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/leaflet/markercluster.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
    
        <link type="text/css" href="theme/bootstrap.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="stylesheet">
	<link type="text/css" href="theme/datetimepicker.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="stylesheet">
	<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="stylesheet">
	
	<?
	if ($gsValues['MAP_GOOGLE'] == 'true')
	{
		if ($gsValues['MAP_GOOGLE_KEY'] == '')
		{
			echo '<script src="'.$gsValues['HTTP_MODE'].'://maps.google.com/maps/api/js?sensor=false"></script>';
		}
		else
		{
			echo '<script src="'.$gsValues['HTTP_MODE'].'://maps.google.com/maps/api/js?sensor=false&key='.$gsValues['MAP_GOOGLE_KEY'].'"></script>';
		}
	}
	?>
	
	<?
	if ($gsValues['MAP_YANDEX'] == 'true')
	{
		echo '<script src="'.$gsValues['HTTP_MODE'].'://api-maps.yandex.ru/2.0/?load=package.map&lang=ru-RU"></script>';
	}
	?>
	
	<script type="text/javascript" src="../js/leaflet/leaflet.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<?
	if ($gsValues['MAP_MAPBOX'] == 'true')
	{
		echo '<script src="'.$gsValues['HTTP_MODE'].'://api.mapbox.com/mapbox.js/v2.3.0/mapbox.js"></script>';
	}
	?>
	
	<script type="text/javascript" src="../js/leaflet/tile/google.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/tile/bing.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/tile/yandex.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/leaflet.label.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/marker.rotate.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/leaflet.markercluster.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script type="text/javascript" src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
                <script type="text/javascript" src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <![endif]-->
        
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script type="text/javascript" src="../js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script type="text/javascript" src="js/bootstrap.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/bootbox.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/datetimepicker.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<?
	// check if spare parts files exist, if not, use joined file
        if(file_exists('js/src/gs.tracking.js'))
	{
	?>
		<script type="text/javascript" src="js/src/gs.tracking.js"></script>
		<script type="text/javascript" src="js/src/gs.history.js"></script>
		<script type="text/javascript" src="js/src/gs.gui.js"></script>
		<script type="text/javascript" src="js/src/gs.cmd.js"></script>
		<script type="text/javascript" src="js/src/gs.events.js"></script>
		<script type="text/javascript" src="js/src/gs.misc.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.js"></script>
        <?
	}
	else
	{
	?>
        	<script type="text/javascript" src="js/gs.main.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <? 
	}
	?>
</head>

<body onload="load()" onUnload="unload()">
        <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<a href="#" class="show-menu icon-only pull-left" onclick="switchPage('menu');">
				<i class="glyphicon glyphicon-menu-hamburger"></i>
			</a>
			
			<div class="navbar-header pull-right">
				<select id="map_layer" class="btn btn-default navbar-btn form-control" onChange="switchMapLayer($(this).val());"></select>
			</div>
			
			<div class="navbar-header pull-right">
				<select id="event_list_page" class="navbar-btn form-control" style="display: none;" onChange="eventLoadList();">
					<option value="1">1</option>
				</select>
			</div>
			
			<div class="navbar-header">
				<div class="navbar-brand">
					<div id="page_title">
						<? echo $la['MAP']; ?>
					</div>
				</div>
			</div>	
		</div>
        </nav>
	
	<div id="loading_panel">
		<div class="table">
			<div class="table-cell center-middle">
				<div class="loader">
					<? echo $la['LOADING_PLEASE_WAIT']; ?>
					<span></span>
				</div>
			</div>
		</div>
	</div>
	
	<div id="loading_data" style="display: none;">
		<div class="table">
			<div class="table-cell center-middle">
				<div class="loader">
					<? echo $la['LOADING_PLEASE_WAIT']; ?>
					<span></span>
				</div>
			</div>
		</div>
	</div>
	
	<div id="dt_picker"></div>
	
	<div id="page_map"><div id="map"></div></div>
	
	<? include ("inc/inc_page.menu.php"); ?>
	<? include ("inc/inc_page.objects.php"); ?>
	<? include ("inc/inc_page.events.php"); ?>
	<? include ("inc/inc_page.history.php"); ?>
	<? include ("inc/inc_page.cmd.php"); ?>
	<? include ("inc/inc_page.settings.php"); ?>
</body>
</html>