<?
	session_start();
	include ('init.php');
	include ('func/fn_common.php');	
	checkUserSession();
	
	setUserSessionSettings($_SESSION["user_id"]);
	loadLanguage($_SESSION['language'], $_SESSION["units"]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<? generatorTag(); ?>
        <title><? echo $gsValues['NAME'].' '.$gsValues['VERSION']; ?></title>
	
        <link type="text/css" href="theme/jquery-ui.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="theme/jquery.qtip.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="theme/ui.jqgrid.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="theme/jquery.pnotify.default.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<link type="text/css" href="theme/leaflet/leaflet.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/leaflet/leaflet.label.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/leaflet/markercluster.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/leaflet/leaflet-routing-machine.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
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
	
        <script type="text/javascript" src="js/leaflet/leaflet.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<?
	if ($gsValues['MAP_MAPBOX'] == 'true')
	{
		echo '<script src="'.$gsValues['HTTP_MODE'].'://api.mapbox.com/mapbox.js/v2.3.0/mapbox.js"></script>';
	}
	?>
	
	<script type="text/javascript" src="js/leaflet/tile/google.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/tile/bing.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/tile/yandex.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.label.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.editable.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/marker.rotate.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.markercluster.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.polylineDecorator.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<?
	if ($gsValues['HTTP_MODE'] == 'http')
	{
		echo '<script type="text/javascript" src="js/leaflet/leaflet-routing-machine.min.js?v='.$gsValues['VERSION_ID'].'"></script>';
	}
	else
	{
		echo '<script type="text/javascript" src="js/leaflet/leaflet-routing-machine-ssl.min.js?v='.$gsValues['VERSION_ID'].'"></script>';
	}
	?>
	
	<script type="text/javascript" src="js/md5.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery-ui.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.qtip.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jscolor.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.jqGrid.locale.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.jqGrid.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.pnotify.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.generatefile.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.blockUI.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>

        <script type="text/javascript" src="js/jquery.flot.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.flot.crosshair.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.flot.navigate.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.flot.selection.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.flot.time.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.flot.resize.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>

	<script type="text/javascript" src="js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <?
	// check if spare parts files exist, if not, use joined file
        if(file_exists('js/src/gs.tracking.js'))
	{
	?>
		<script type="text/javascript" src="js/src/gs.tracking.js"></script>
		<script type="text/javascript" src="js/src/gs.events.js"></script>
		<script type="text/javascript" src="js/src/gs.history.js"></script>
		<script type="text/javascript" src="js/src/gs.history.reports.js"></script>
		<script type="text/javascript" src="js/src/gs.history.inpexp.js"></script>
		<script type="text/javascript" src="js/src/gs.rilogbook.js"></script>
		<script type="text/javascript" src="js/src/gs.places.js"></script>
		<script type="text/javascript" src="js/src/gs.places.inpexp.js"></script>
		<script type="text/javascript" src="js/src/gs.places.markers.js"></script>
		<script type="text/javascript" src="js/src/gs.places.zones.js"></script>
		<script type="text/javascript" src="js/src/gs.places.routes.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.objects.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.sensors.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.service.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.groups.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.drivers.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.passengers.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.trailers.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.events.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.templates.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.subaccounts.js"></script>
		<script type="text/javascript" src="js/src/gs.gui.js"></script>
		<script type="text/javascript" src="js/src/gs.notify.js"></script>
		<script type="text/javascript" src="js/src/gs.misc.js"></script>
		<script type="text/javascript" src="js/src/gs.cmd.js"></script>
		<script type="text/javascript" src="js/src/gs.img.js"></script>
		<script type="text/javascript" src="js/src/gs.chat.js"></script>
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
	<input id="upload_file" type="file" style="display: none;" onchange=""/>
	
        <? include ("inc/inc_panels.php"); ?>
	<? include ("inc/inc_menus.php"); ?>
        <? include ("inc/inc_dialogs.main.php"); ?>
        <? include ("inc/inc_dialogs.places.php"); ?>
        <? include ("inc/inc_dialogs.reports.php"); ?>
	<? include ("inc/inc_dialogs.rilogbook.php"); ?>
	<? include ("inc/inc_dialogs.cmd.php"); ?>
	<? include ("inc/inc_dialogs.img.php"); ?>
	<? include ("inc/inc_dialogs.chat.php"); ?>
        <? include ("inc/inc_dialogs.settings.php"); ?>
    </body>
</html>