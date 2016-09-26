<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	if (isset($_GET['imei']))
	{
		if(!checkUserToObjectPrivileges($_GET['imei']))
		{
			die;
		}
	}
	else
	{
		die;
	}
	
	$user_id = $_SESSION['user_id'];
	$imei = $_GET['imei'];
	$map_layer = $_GET['map_layer'];
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $la['FOLLOW'].' ('.getObjectName($imei).')'; ?></title>
        
        <link type="text/css" href="../theme/jquery-ui.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="../theme/ui.jqgrid.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<link type="text/css" href="../theme/leaflet/leaflet.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/leaflet/leaflet.label.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
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
	
	<script type="text/javascript" src="../js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/jquery.jqGrid.locale.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/jquery.jqGrid.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>

	<script type="text/javascript" src="../js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <script>
                // vars
		var la = [];
                var map;
                var mapLayers = new Array();
		var mapPopup;
                var timer_objectFollow;
                var objectsData = new Array();
                
                var settingsValuesUser = new Array();
                var settingsValuesObjects = new Array();
                
                // icons
                var mapMarkerIcons = new Array();
		
		mapMarkerIcons['arrow_black'] = L.icon({
			iconUrl: '../img/markers/arrow_black.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_blue'] = L.icon({
			iconUrl: '../img/markers/arrow_blue.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_green'] = L.icon({
			iconUrl: '../img/markers/arrow_green.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_grey'] = L.icon({
			iconUrl: '../img/markers/arrow_grey.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_orange'] = L.icon({
			iconUrl: '../img/markers/arrow_orange.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_purple'] = L.icon({
			iconUrl: '../img/markers/arrow_purple.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_red'] = L.icon({
			iconUrl: '../img/markers/arrow_red.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
		    
		mapMarkerIcons['arrow_yellow'] = L.icon({
			iconUrl: '../img/markers/arrow_yellow.png',
			iconSize:     [16, 24], // size of the icon
			iconAnchor:   [8, 12], // point of the icon which will correspond to marker's location
			popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
		});
                
		function loadObjectMapMarkerIcons()
		{
			var icon_array = new Array();
			for (var key in settingsValuesObjects)
			{
				var imei = settingsValuesObjects[key];
				icon_array.push(imei.icon);	
			}
			
			icon_array = uniqueArray(icon_array);
			
			for (i=0;i<icon_array.length;i++)
			{
				var name = icon_array[i];
				var file = '../'+icon_array[i]
				
				mapMarkerIcons[name] = L.icon({
					iconUrl: file,
					iconSize:     [26, 26], // size of the icon
					iconAnchor:   [13, 13], // point of the icon which will correspond to marker's location
					popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
				});				
			}
		}
                
                function load()
                {
			loadLanguage();
			
			settingsLoad('server');
                        settingsLoad('user');
                        settingsLoad('objects');
			
			initGrids();
			initGui();
                        
                        map = L.map('map_follow', {minZoom: 3, maxZoom: 18, editable: true});
                        
                        // add map layers
                        initSelectList('map_layer_list');
			
			// define map layers
			defineMapLayers();
                        
                        // define layers	
                        mapLayers['realtime'] = L.layerGroup();
                        mapLayers['realtime'].addTo(map);
                        
			// set map type
			var map_layer = '<? echo $map_layer; ?>';
			switchMapLayer(map_layer);
			
                        map.setView([0, 0], 15);
                        
                        objectFollow('<? echo $imei; ?>');
			
			var load2 = setTimeout("load2()", 2000);
                }
		
		function load2()
                {
                        document.getElementById("loading_panel").style.display = "none";
                }
                
                function unload()
                {
                        
                }
                
                function objectFollow(imei)
                {
                        clearTimeout(timer_objectFollow);
                        
                        var data = {
                                cmd: 'load_object_data',
                                imei: imei
                        };
                        
                        $.ajax({
                                type: "POST",
                                url: "../func/fn_objects.php",
                                data: data,
                                dataType: 'json',
                                cache: false,
                                error: function(statusCode, errorThrown) {
                                        // shedule next object reload
                                        timer_objectFollow = setTimeout("objectFollow('"+imei+"');", gsValues['map_refresh'] * 1000);
                                },
                                success: function(result)
                                {
                                        // convert tracking route to normal format
                                        for (var imei in result)
                                        {
                                                result[imei] = transformToObjectData(result[imei]);
                                        }
                                        
                                        if (Object.keys(objectsData).length != Object.keys(result).length)
                                        {
                                                objectsData = result;
                                        }
                                        else
                                        {
                                                for (var imei in result)
                                                {
                                                        objectsData[imei]['conn_valid'] = result[imei]['conn_valid'];
                                                        objectsData[imei]['loc_valid'] = result[imei]['loc_valid'];
                                                        objectsData[imei]['odometer'] = result[imei]['odometer'];
							objectsData[imei]['status'] = result[imei]['status'];
							objectsData[imei]['status_string'] = result[imei]['status_string'];
                                                        objectsData[imei]['engine_hours'] = result[imei]['engine_hours'];
                                                        
                                                        if (objectsData[imei]['data'] == '')
                                                        {
                                                                objectsData[imei]['data'] = result[imei]['data'];
                                                        }
                                                        else
                                                        {
                                                                if (objectsData[imei]['data'].length >= settingsValuesObjects[imei]['tail_points'])
                                                                {
                                                                        objectsData[imei]['data'].pop(); 
                                                                }
                                                                objectsData[imei]['data'].unshift(result[imei]['data'][0]);
                                                        }
                                                }
                                        }
                                        
                                        objectRemoveFromMap();
                                        if (settingsValuesObjects[imei].active == "true")
                                        {
                                                objectAddToMap(imei);
                                        }
					
					if (document.getElementById("follow").checked == true)
					{
						var lat = objectsData[imei]['data'][0]['lat'];
						var lng = objectsData[imei]['data'][0]['lng'];
						
						map.panTo({lat: lat, lng: lng});
					}
                                        
                                        // shedule next object reload
                                        timer_objectFollow = setTimeout("objectFollow('"+imei+"');", gsValues['map_refresh'] * 1000);
                                }
                        });  
                }
                
                function objectAddToMap(imei)
                {
                        // get data
                        var name = settingsValuesObjects[imei]['name'];
                        
                        if (objectsData[imei]['data'] != '')
                        {
                                var lat = objectsData[imei]['data'][0]['lat'];
                                var lng = objectsData[imei]['data'][0]['lng'];
                                var altitude = objectsData[imei]['data'][0]['altitude'];
                                var angle = objectsData[imei]['data'][0]['angle'];
                                var speed = objectsData[imei]['data'][0]['speed'];
                                var dt_tracker = objectsData[imei]['data'][0]['dt_tracker'];
				var params = objectsData[imei]['data'][0]['params'];
				
				var extra_data = objectsData[imei]['data'][0];
				showExtraData(imei, extra_data);	
                        }
                        else
                        {
                                var lat = 0;
                                var lng = 0;
                                var speed = 0;
				var params = false;
                        }
                        
                        // rotate marker only if icon is arrow
                        var iconAngle = angle;
                        if (settingsValuesObjects[imei]['map_icon'] != 'arrow')
                        {
                                iconAngle = 0;
                        }
                        
			//marker
			var status = objectsData[imei]['status'];
                        var icon = getMarkerIcon(imei, speed, status, false);
                        var marker = L.marker([lat, lng], {icon: icon, iconAngle: iconAngle});
			
			// label (not working with clusters)
			//var label = new L.Label({offset: [20,-12], direction: 'right'});
			//label.setContent(name + " (" + speed + " " + la["UNIT_SPEED"] +")");
			//label.setLatLng([lat, lng]);
			
			// label (working with clusters)
			var label = name + " (" + speed + " " + la["UNIT_SPEED"] +")";
			marker.bindLabel(label, {noHide: true, offset: [20,-12], direction: 'right'});
			
			// set click event
			marker.on('click', function(e) {				
				if (objectsData[imei]['data'] != '')
				{
					geocoderGetAddress(lat, lng, function(responce)
					{
						var address = responce;
						var position = urlPosition(lat, lng);
						
						var text = '<table>\
							<tr><td><strong>' + la['OBJECT'] + ':</strong></td><td>' + name + '</td></tr>\
							<tr><td><strong>' + la['ADDRESS'] + ':</strong></td><td>' + address + '</td></tr>\
							<tr><td><strong>' + la['POSITION'] + ':</strong></td><td>' + position + '</td></tr>\
							<tr><td><strong>' + la['ALTITUDE'] + ':</strong></td>\
							<td>' + altitude + ' ' + la["UNIT_HEIGHT"] + '</td></tr>\
							<tr><td><strong>' + la['ANGLE'] + ':</strong></td><td>' + angle + ' &deg;</td></tr>\
							<tr><td><strong>' + la['SPEED'] + ':</strong></td>\
							<td>' + speed + ' ' + la["UNIT_SPEED"] + '</td></tr>\
							<tr><td><strong>' + la['TIME'] + ':</strong></td><td>' + dt_tracker + '</td></tr>';
						
							// add sensors to popup
							var sensors = settingsValuesObjects[imei]['sensors'];
							for (var key in sensors)
							{
								var sensor = sensors[key];
								if (sensor.popup == 'true')
								{
									var sensor_data = getSensorValue(params, sensor);
									text += '<tr><td><strong>' + sensor.name + ':</strong></td><td>' + sensor_data.value_full + '</td></tr>';
								}
							}
							
						text += '</table>';
							
						addPopupToMap(lat, lng, [0, -12], text);
					});
				}
			});
                        
                        marker.on('add', function(e) {
                                objectAddTailToMap(imei);
                        });
                        
                        marker.on('remove', function(e) {
                                if (objectsData[imei].layers.tail)
                                {
                                        mapLayers['realtime'].removeLayer(objectsData[imei].layers.tail);	
                                }
                        });
                        
                        mapLayers['realtime'].addLayer(marker);
			//mapLayers['realtime'].addLayer(label);
                        
                        // store layer
                        objectsData[imei].layers.marker = marker;
			//objectsData[imei].layers.label = label;
                }
                
                function objectRemoveFromMap()
                {
                        mapLayers['realtime'].clearLayers();
                }
		
		function objectAddTailToMap(imei)
		{
			if (settingsValuesObjects[imei]['tail_points'] > 0)
			{
				if (objectsData[imei].layers.tail)
				{
					mapLayers['realtime'].removeLayer(objectsData[imei].layers.tail);	
				}
				
				var line_points = new Array();
				var i;
				
				for (i=0;i<objectsData[imei]['data'].length;i++)
				{
					var lat = objectsData[imei]['data'][i]['lat'];
					var lng = objectsData[imei]['data'][i]['lng'];
					
					line_points.push(L.latLng(lat, lng));
				}
				
				// draw tail polyline
				var tail = L.polyline(line_points, {color: settingsValuesObjects[imei]['tail_color'], opacity: 0.8, weight: 3});
				
				mapLayers['realtime'].addLayer(tail);
				
				// store layer
				objectsData[imei].layers.tail = tail;
			}
		}
		
		function showExtraData(imei, data)
		{
			var list_id = $("#side_panel_follow_data_list_grid");
			var list_data = [];
			
			list_id.clearGridData(true);
			
			// exit function if no object data
			if (data == '') return;
			
			var dt_server = data['dt_server'];
			var dt_tracker = data['dt_tracker'];
			var lat = data['lat'];
			var lng = data['lng'];
			var altitude = data['altitude'];
			var angle = data['angle'];
			var speed = data['speed'];
			
			var odo = getObjectOdometer(imei, false);
			if (odo != -1)
			{
				list_data.push({data: la['ODOMETER']+':', value: odo + ' ' + la["UNIT_DISTANCE"]});
                        }
			
			var engh = getObjectEngineHours(imei, false);
			if (engh != -1)
			{
				list_data.push({data: la['ENGINE_HOURS']+':', value: engh + ' ' + la["UNIT_H"]});
                        }
			
			var status_string = objectsData[imei]['status_string'];
			if (status_string != '')
			{
				list_data.push({data: la['STATUS'], value: status_string});       
                        }
			
			list_data.push({data: la['TIME_POSITION']+':', value: dt_tracker});
			list_data.push({data: la['TIME_SERVER']+':', value: dt_server});
		 
			var model = settingsValuesObjects[imei]['model']; // get model
			if (model != "")
			{
				list_data.push({data: la['MODEL']+':', value: model});
			}
			
			var vin = settingsValuesObjects[imei]['vin']; // get VIN
			if (vin != "")
			{
				list_data.push({data: la['VIN'] + ':', value: vin});
			}
			
			var plate_number = settingsValuesObjects[imei]['plate_number']; // get plate_number
			if (plate_number != "")
			{
				list_data.push({data: la['PLATE']+':', value: plate_number});
			}
			
			var sim_number = settingsValuesObjects[imei]['sim_number']; // get sim_number
			if (sim_number != "")
			{
				list_data.push({data: la['SIM_CARD_NUMBER']+':', value: sim_number});
			}
			
			var position = urlPosition(lat, lng);
			
			list_data.push({data: la['POSITION']+':', value: position});
			list_data.push({data: la['SPEED']+':', value: speed + ' ' + la["UNIT_SPEED"]});
			list_data.push({data: la['ALTITUDE']+':', value: altitude + ' ' + la["UNIT_HEIGHT"]});
			list_data.push({data: la['ANGLE']+':', value: angle + ' &deg;'});
			
			//// get nearest zone and marker
			//var nearest_zone = getNearestZone(imei, lat, lng);
			//if (nearest_zone['name'] != '')
			//{
			//       list_data.push({data: la['NEAREST_ZONE']+':', value: nearest_zone['name'] + ' (' + nearest_zone['distance'] + ')'});
			//}
			//
			//var nearest_marker = getNearestMarker(imei, lat, lng);
			//if (nearest_marker['name'] != '')
			//{
			//    list_data.push({data: la['NEAREST_MARKER']+':', value: nearest_marker['name'] + ' (' + nearest_marker['distance'] + ')'});
			//}
		 
			// add sensors to object data list
			var sensors = settingsValuesObjects[imei]['sensors'];
			for (var key in sensors)
			{
				var sensor = sensors[key];
				
				if ((sensor.type != 'odo') && (sensor.type != 'engh'))
				{
					var sensor_data = getSensorValue(data['params'], sensor);
					list_data.push({data: sensor.name + ':', value: sensor_data.value_full});
				}
			}
			
			for(var i=0;i<list_data.length;i++)
			{
				list_id.jqGrid('addRowData',i,list_data[i]);
			}
			list_id.setGridParam({sortname:'data', sortorder: 'asc'}).trigger('reloadGrid');
		}
                
		function showHideInfo()
		{
			var map_left = "280px";
			
			if ($(window).width()< 640)
			{
				var map_left = "0px";
			}
			
			if (document.getElementById("info").checked == true) {
				document.getElementById("side_panel_follow").style.display = "block";
				document.getElementById("map_follow").style.left = map_left;
				
				setTimeout( function() { map.invalidateSize(true);}, 200);
			} else {
				document.getElementById("side_panel_follow").style.display = "none";
				document.getElementById("map_follow").style.left = "0px";
				
				setTimeout( function() { map.invalidateSize(true);}, 200);
			}
		}
		
		function initGui()
		{
			$(window).bind('resize', function()
			{
				showHideInfo();
			}).trigger('resize');
		}
		
		function initGrids()
		{
			// define left panel object data list grid
			$("#side_panel_follow_data_list_grid").jqGrid({
				datatype: 'local',
				colNames:[la['DATA'], la['VALUE']],
				colModel:[
					{name:'data',index:'data',width:90,sortable:false},
					{name:'value',index:'value',width:163,sortable:false}
				],
				width: '280',
				height: '100',
				rowNum: 100,
				shrinkToFit: false
			});
			
			$(window).bind('resize', function()
			{
				if ($(window).width()< 640)
				{
					$("#side_panel_follow_data_list_grid").setGridHeight($(window).height() - 105);
				}
				else
				{
					$("#side_panel_follow_data_list_grid").setGridHeight($(window).height() - 30);
				}
			}).trigger('resize');
		}
		
                function initSelectList(list)
                {
                        switch (list)
                        {
                                case "map_layer_list":
                                        var select = document.getElementById('map_layer');
                                        select.options.length = 0; // clear out existing items
                                        
					if (gsValues['map_osm'])
					{
						select.options.add(new Option('OSM Map', 'osm'));
					}
					
					if (gsValues['map_bing'])
					{
						select.options.add(new Option('Bing Road', 'broad'));
						select.options.add(new Option('Bing Aerial', 'baer'));
						select.options.add(new Option('Bing Hybrid', 'bhyb'));	
					}
					
					if (gsValues['map_google'])
					{
						select.options.add(new Option('Google Streets', 'gmap'));
						select.options.add(new Option('Google Satellite', 'gsat'));
						select.options.add(new Option('Google Hybrid', 'ghyb'));
						select.options.add(new Option('Google Terrain', 'gter'));
					}
					
					if (gsValues['map_mapbox'])
					{
						select.options.add(new Option('Mapbox Streets', 'mbmap'));
						select.options.add(new Option('Mapbox Satellite', 'mbsat'));
					}
					
					if (gsValues['map_yandex'])
					{
						select.options.add(new Option('Yandex', 'yandex'));	
					}
					
					for (var i=0;i<gsValues['map_custom'].length;i++)
					{
						var layer_id = gsValues['map_custom'][i].layer_id;
						var name = gsValues['map_custom'][i].name;
						
						select.options.add(new Option(name, layer_id));	
					}
                                break;
                        }
		}
		
                function addPopupToMap(lat, lng, offset, text)
		{	
			mapPopup = L.popup({offset: offset}).setLatLng([lat, lng]).setContent(text).openOn(map);
		}
		
                function settingsLoad(type)
                {
                        switch (type)
                        {
				case "server":
					var data = {
						cmd: 'load_server_values'
					};
						$.ajax({
						type: "POST",
						url: "fn_settings.php",
						data: data,
						dataType: 'json',
						cache: false,
						async: false,
						success: function(result)
						{
							gsValues['map_custom'] = result['map_custom'];
							gsValues['map_osm'] = strToBoolean(result['map_osm']);
							gsValues['map_bing'] = strToBoolean(result['map_bing']);
							gsValues['map_google'] = strToBoolean(result['map_google']);
							gsValues['map_google_traffic'] = strToBoolean(result['map_google_traffic']);
							gsValues['map_mapbox'] = strToBoolean(result['map_mapbox']);
							gsValues['map_yandex'] = strToBoolean(result['map_yandex']);
							gsValues['map_bing_key'] = result['map_bing_key'];
							gsValues['map_mapbox_key'] = result['map_mapbox_key'];
							gsValues['map_lat'] = result['map_lat'];
							gsValues['map_lng'] = result['map_lng'];
							gsValues['map_zoom'] = result['map_zoom'];
							gsValues['map_layer'] = result['map_layer'];
						}
					});
					break;
                                case "user":
                                        var data = {
                                                cmd: 'load_user_settings'
                                        };
                                                $.ajax({
                                                type: "POST",
                                                url: "fn_settings.php",
                                                data: data,
                                                dataType: 'json',
                                                cache: false,
						async: false,
                                                success: function(result)
                                                {
                                                        settingsValuesUser = result;
                                                }
                                        });
                                        break;
                                case "objects":
                                        var data = {
                                                cmd: 'load_object_values'
                                        };
                                        
                                        $.ajax({
                                                type: "POST",
                                                url: "fn_settings.objects.php",
                                                data: data,
                                                dataType: 'json',
                                                cache: false,
						async: false,
                                                success: function(result)
                                                {
                                                        settingsValuesObjects = result;
							
							loadObjectMapMarkerIcons();
                                                }
                                        });
                                        break;
                        }
                }
        </script>
    </head>
    
    <body onload="load()" onUnload="unload()">
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

        <div id="map_follow"></div>
        <div class="object-follow-control">
		<div class="row4">
			<div style="margin-right: 3px;"><? echo $la['INFO']; ?></div>
			<div style="margin-right: 3px;"><input id="info" type="checkbox" class="checkbox" onclick="showHideInfo();"/></div>
			<div style="margin-right: 3px;"><? echo $la['FOLLOW']; ?></div>
			<div style="margin-right: 3px;"><input id="follow" type="checkbox" class="checkbox" checked/></div>
			<div style="margin-right: 0px;"><select id="map_layer" onChange="switchMapLayer($(this).val());"></select></div>
		</div>
	</div>
	<div id="side_panel_follow">
		<div id="side_panel_follow_data_list">
			<table id="side_panel_follow_data_list_grid"></table>
		</div>
	</div>
    </body>
</html>