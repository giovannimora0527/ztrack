<?
	session_start();
	include ('init.php');
	include ('func/fn_common.php');
	checkUserSession();
	checkUserCPanelPrivileges();
	
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
		<link type="text/css" href="theme/ui.jqgrid.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		<link type="text/css" href="theme/jquery.tokenize.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
		<script type="text/javascript" src="js/md5.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.jqGrid.locale.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.jqGrid.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.tokenize.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		
		<script type="text/javascript" src="js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		
		<script type="text/javascript" src="js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	</head>
    
	<body id="cpanel" onload="load()" >
		<input id="load_file" type="file" style="display: none;" onchange=""/>
		
		<div id="loading_panel">
			<div class="table">
				<div class="table-cell center-middle">
					<div id="loading_panel_msg">
						<img style="border:0px;" src="<? echo $gsValues['URL_LOGO']; ?>" /><br/><br/>
						<div class="loader">
							<? echo $la['LOADING_PLEASE_WAIT']; ?>
							<span></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="blocking_panel">
			<div class="table">
				<div class="table-cell center-middle">
					<div id="blocking_panel_msg">
						<img style="border:0px;" src="<? echo $gsValues['URL_LOGO']; ?>" /><br/><br/>
						<? echo $la['YOUR_SESSION_HAS_EXPIRED']; ?>
					</div>
				</div>
			</div>
		</div>
		
		<div id="top_panel">
			<ul class="left-menu">
				<li class="back-btn"><a title="<? echo $la['BACK']; ?>" href="tracking.php"><img src="theme/images/home.png" /></a></li>
				
				<? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
				<li class="select-view">
					<? echo $la['VIEW_AS']; ?>: <select id="cpanel_manager_list" onchange="switchCPManager(this.value);"/></select>
				</li>
				<? } ?>
				
				<li>
					<a class="user-list-btn active" id="top_panel_button_user_list" href="#" onClick="switchCPTab('user_list');">
						<img src="theme/images/user.png" />
						<span><? echo $la['USER_LIST']; ?> </span><span id="user_list_stats"></span>
					</a>
				</li>
				
				<li>
					<a class="object-list-btn" id="top_panel_button_object_list" href="#" onClick="switchCPTab('object_list');">
					<img src="theme/images/point.png" />
					<span><? echo $la['OBJECT_LIST']; ?> </span><span id="object_list_stats"></span>
					</a>
				</li>
				
				<?
					if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) {
				?>
					<li>
						<a class="unused-object-list-btn" id="top_panel_button_unused_object_list" href="#" onClick="switchCPTab('unused_object_list');">
						<img src="theme/images/point.png" />
						<span><? echo $la['UNUSED_OBJECT_LIST']; ?> </span><span id="unused_object_list_stats"></span>
						</a>
					</li>
				<? } ?>
				
				<?
					if ($_SESSION["cpanel_privileges"] == 'super_admin') {
				?>
					<li>
						<a class="manage-server-btn" id="top_panel_button_manage_server" href="#" onClick="switchCPTab('manage_server');">
							<img src="theme/images/cpanel-settings.png" />
							<span><? echo $la['MANAGE_SERVER']; ?></span>
						</a>
					</li>
				<? } ?>
			</ul>
			
			<ul class="right-menu">
				<li class="select-language"><select id="system_language" onChange="switchLanguageCPanel();"><? echo getLanguageList(); ?></select></li>
				<li>
					<a class="user-btn" href="#" onclick="userEdit('<? echo $_SESSION["user_id"]; ?>');" title="<? echo $la['MY_ACCOUNT']; ?>">
						<img src="theme/images/user.png" border="0"/><span><? echo truncateString($_SESSION["username"], 15);?></span>
					</a>
				</li>
				<li class="logout-btn">
					<a title="<? echo $la['LOGOUT']; ?>" href="#" onclick="connectLogout();">
						<img src="theme/images/sign_out.png" />
					</a>
				</li>
			</ul>
		</div>
	    
		<div id="cpanel_user_list">
			<div class="float-left cpanel-title">
				<div class="version">v<? echo $gsValues['VERSION']; ?></div>
				<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['USER_LIST']; ?></span></h1>
			</div>
			<table id="cpanel_user_list_grid"></table>
			<div id="cpanel_user_list_grid_pager"></div>
		</div>
		
		<div id="cpanel_object_list" style="display:none;">
			<div class="float-left cpanel-title">
				<div class="version">v<? echo $gsValues['VERSION']; ?></div>
				<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['OBJECT_LIST']; ?></span></h1>
			</div>	
			<table id="cpanel_object_list_grid"></table>
			<div id="cpanel_object_list_grid_pager"></div>
		</div>
		
		<div id="cpanel_unused_object_list" style="display:none;">
			<div class="float-left cpanel-title">
				<div class="version">v<? echo $gsValues['VERSION']; ?></div>
				<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['UNUSED_OBJECT_LIST']; ?></span></h1>
			</div>	
			<table id="cpanel_unused_object_list_grid"></table>
			<div id="cpanel_unused_object_list_grid_pager"></div>
		</div>
		
		<div id="cpanel_manage_server" style="display:none;">
			<div class="float-left cpanel-title">
				<div class="version">v<? echo $gsValues['VERSION']; ?></div>
				<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['MANAGE_SERVER']; ?></span></h1>
			</div>
			<div id="manage_server_tabs" class="clearfix">
				<ul>
					<li class="cp-server"><a href="#manage_server_server"><? echo $la['SERVER']; ?></a></li>
					<li class="cp-maps"><a href="#manage_server_maps"><? echo $la['MAPS']; ?></a></li>
					<li class="cp-user"><a href="#manage_server_user"><? echo $la['USER']; ?></a></li>
					<li class="cp-templates"><a href="#manage_server_templates"><? echo $la['TEMPLATES']; ?></a></li>
					<li class="cp-email"><a href="#manage_server_email"><? echo $la['EMAIL']; ?></a></li>
					<li class="cp-sms"><a href="#manage_server_sms"><? echo $la['SMS']; ?></a></li>
					<li class="cp-tools"><a href="#manage_server_tools"><? echo $la['TOOLS']; ?></a></li>
					<li class="cp-logs"><a href="#manage_server_logs"><? echo $la['LOGS']; ?></a></li>
					<li class="save-btn"><input class="button icon-save icon ms-save" type="button" onclick="saveServerValues();" value="<? echo $la['SAVE']; ?>"></li>
				</ul>
				<div class="cpanel-tabs-content">
				<div id="manage_server_server">
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['INFORMATION']; ?></div>
							<div class="row">
								<div class="width50">
									<? echo $la['HARDWARE_KEY']; ?>:
								</div>
								<div class="width50">
									<? echo $gsValues['HW_KEY']; ?>
								</div>
							</div>
							<div class="row">
								<div class="width50">
									<? echo $la['SERVER_IP']; ?>:
								</div>
								<div class="width50">
									<? echo $gsValues['SERVER_IP']; ?>
								</div>
							</div>
							<div class="row">
								<div class="width50">
									<? echo $la['SERVER_PORTS']; ?>:
								</div>
								<div class="width50">
									<a href="<? echo $gsValues['URL_SERVER_PORTS']; ?>" target="_blank"><? echo $gsValues['URL_SERVER_PORTS']; ?></a>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['GENERAL']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['GPS_SERVER_NAME']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_name" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EX_MY_GPS_SERVER']; ?>" />
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAGE_GENERATOR_TAG']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_generator" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EX_MY_GPS_SERVER']; ?>" />
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SHOW_ABOUT_BUTTON']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_show_about">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['LANGUAGES']; ?></br>
									<span class="subinfo"><? echo $la['HOLD_CTRL_TO_SELECT_MULTIPLE_ITEMS']; ?></span>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_languages" style="width: 100px; height:150px;" multiple="multiple">
										<? echo getLanguageListFiles(); ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['LOGO']; ?></div>
							<div class="row2">
								<div class="width50">
									<center><img id="cpanel_manage_server_logo" style="border:0px;" src="<? echo $gsValues['URL_LOGO']; ?>" /><br/>
									<? echo $la['LOGO_SIZE_FORMAT']; ?></center>
								</div>
								<div class="width50">
									<input style="width: 100px;" class="button" type="button" value="<? echo $la['UPLOAD']; ?>" onclick="uploadLogo();"/>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['URL_ADDRESSES']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['URL_TO_LOGIN_DIALOG']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_url_login" class="inputbox width70" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['URL_TO_HELP_PAGE']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_url_help" class="inputbox width70" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['URL_TO_CONTACT_PAGE']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_url_contact" class="inputbox width70" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['URL_TO_SHOP_PAGE']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_url_shop" class="inputbox width70" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['URL_TO_SMS_GATEWAY_APP']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_url_sms_gateway_app" class="inputbox width70" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['GEOCODER']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['USE_GEOCODER_CACHE']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_geocoder_cache" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['CLEAR_GEOCODER_CACHE']; ?>
								</div>
								<div class="width50">
									<input style="width: 100px;" class="button" type="button" onclick="geocoderClearCache();" value="<? echo $la['CLEAR']; ?>" />
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['OBJECTS']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['OBJECT_CONNECTION_TIMEOUT_RESETS_CONNECTION_AND_GPS_STATUS']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_connection_timeout">
										<option value="1">1 <? echo $la['UNIT_MIN']; ?></option>
										<option value="2">2 <? echo $la['UNIT_MIN']; ?></option>
										<option value="3">3 <? echo $la['UNIT_MIN']; ?></option>
										<option value="4">4 <? echo $la['UNIT_MIN']; ?></option>
										<option value="5">5 <? echo $la['UNIT_MIN']; ?></option>
										<option value="10">10 <? echo $la['UNIT_MIN']; ?></option>
										<option value="20">20 <? echo $la['UNIT_MIN']; ?></option>
										<option value="30">30 <? echo $la['UNIT_MIN']; ?></option>
										<option value="40">40 <? echo $la['UNIT_MIN']; ?></option>
										<option value="50">50 <? echo $la['UNIT_MIN']; ?></option>
										<option value="60">60 <? echo $la['UNIT_MIN']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['KEEP_HISTORY_PERIOD']; ?><br/>
									<? echo $la['WARNING_CHANGING_THIS_VALUE_WILL_AFFECT_EXISTING_DATA']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_history_period" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 30"/>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['BACKUP']; ?></div>
							<div class="row2">
								<div class="width50"><? echo $la['SEND_DB_BACKUP_TO_EMAIL_EVERY_24_HOURS']; ?></div>
								<div class="width50"><input id="cpanel_manage_server_backup_email" class="inputbox width70" maxlength="50"/></div>
							</div>
						</div>
					</div>
				</div>
				<div id="manage_server_maps">		
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['AVAILABLE_MAPS']; ?></div>
							<div class="row2">
								<div class="width50">
									OSM Map
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_osm" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									Bing Maps
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_bing" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									Google Maps
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_google" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									Google Maps Traffic
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_google_traffic" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									Mapbox Maps
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_mapbox" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									Yandex Map
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_yandex" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="width-1000">
							<div class="title-block"><? echo $la['LICENSE_KEYS']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['BING_MAPS_KEY']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_map_bing_key" class="inputbox"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['GOOGLE_MAPS_KEY']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_map_google_key" class="inputbox"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['MAPBOX_MAPS_KEY']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_map_mapbox_key" class="inputbox"/>
								</div>
							</div>
						</div>
						<div class="width-1000">
							<div class="title-block"><? echo $la['MAP_LAYER_ZOOM_POSITION_AFTER_LOGIN']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['LAYER']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_layer" />
										<option value="osm">OSM Map</option>
										<option value="broad">Bing Road</option>
										<option value="baer">Bing Aerial</option>
										<option value="bhyb">Bing Hybrid</option>
										<option value="gmap">Google Streets</option>
										<option value="gsat">Google Satellite</option>
										<option value="ghyb">Google Hybrid</option>
										<option value="gter">Google Terrain</option>
										<option value="mbmap">Mapbox Streets</option>
										<option value="mbsat">Mapbox Satellite</option>
										<option value="yandex">Yandex</option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['ZOOM']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_map_zoom" />
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
										<option value="13">13</option>
										<option value="14">14</option>
										<option value="15">15</option>
										<option value="16">16</option>
										<option value="17">17</option>
										<option value="18">18</option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['LATITUDE']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_map_lat" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="10" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 25.000000"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['LONGITUDE']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_map_lng" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="10" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 0.000000"/>
								</div>
							</div>
						</div>
						<div class="width-1000">
							<div class="title-block"><? echo $la['CUSTOM_MAPS']; ?></div>
							<div class="row2">
								<div class="width100">
									<div class="float-right">
										<a href="#" onclick="serverTools('custom_maps');">
											<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
												<img src="theme/images/refresh2.png" border="0"/>
											</div>
										</a>
										<a href="#" onclick="customMapProperties('add');">
											<div class="panel-button"  title="<? echo $la['ADD']; ?>">
												<img src="theme/images/map.png" border="0"/>
											</div>
										</a>
										<a href="#" onclick="customMapDeleteAll();">
											<div class="panel-button"  title="<? echo $la['DELETE_ALL']; ?>">
												<img src="theme/images/trash.png" border="0"/>
											</div>
										</a>
									</div>
								</div>
							</div>
							<div class="width100">
								<table id="cpanel_manage_server_custom_map_list_grid"></table>
							</div>
						</div>
					</div>
				</div>
				<div id="manage_server_user">				
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['LOGIN']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAGE_AFTER_ADMIN_OR_MANAGER_LOGIN']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_page_after_login" />
										<option value="account"><? echo $la['ACCOUNT']; ?></option>
										<option value="cpanel"><? echo $la['CPANEL']; ?></option>
									</select>
								</div>
							</div>
							<div class="title-block"><? echo $la['REGISTRATION']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['ALLOW_USER_REGISTRATION_FROM_LOGIN_DIALOG']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_allow_registration" onChange="serverValuesCheck();" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['EXPIRE_ACCOUNT_DAYS_AFTER_REGISTRATION']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_account_expire" style="width:100px;" onChange="serverValuesCheck();">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
									<input id="cpanel_manage_server_account_expire_period" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
								</div>
							</div>
							<div class="title-block"><? echo $la['DEFAULTS']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['LANGUAGE']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_language">
										<? echo getLanguageList(); ?>
									</select>
								</div>
							</div>
							<div class="row2">
							<div class="width50"><? echo $la['UNIT_OF_DISTANCE']; ?></div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_distance_unit">
										<option value="km"><? echo $la['KILOMETER'];?></option>
										<option value="mi"><? echo $la['MILE'];?></option>
										<option value="nm"><? echo $la['NAUTICAL_MILE'];?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50"><? echo $la['UNIT_OF_CAPACITY']; ?></div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_capacity_unit">
										<option value="l"><? echo $la['LITER'];?></option>
										<option value="g"><? echo $la['GALLON'];?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50"><? echo $la['UNIT_OF_TEMPERATURE']; ?></div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_temperature_unit">
										<option value="c"><? echo $la['CELSIUS'];?></option>
										<option value="f"><? echo $la['FAHRENHEIT'];?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50"><? echo $la['CURRENCY']; ?></div>
								<div class="width50">
									<input style="width: 100px;" id="cpanel_manage_server_currency" class="inputbox" type="text" value="" maxlength="3">
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['TIMEZONE']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_timezone">
										<? include ("inc/inc_timezones.php"); ?>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50"><? echo $la['DAYLIGHT_SAVING_TIME']; ?></div>
								<div class="width2">
									<input id="cpanel_manage_server_dst" type="checkbox" class="checkbox" onchange="serverValuesCheck();"/>
								</div>
								<div class="width8">
									<input readonly class="inputbox-calendar-mmdd inputbox width100" id="cpanel_manage_server_dst_start_mmdd" type="text" value=""/>
								</div>
								<div class="width1"></div>
								<div class="width8">
									<select class="width100" id="cpanel_manage_server_dst_start_hhmm">
										<? include ("inc/inc_dt.hours_minutes.php"); ?>
									</select>
								</div>
								<div class="width2 center-middle">-</div>
								<div class="width8">
									<input readonly class="inputbox-calendar-mmdd inputbox width100" id="cpanel_manage_server_dst_end_mmdd" type="text" value=""/>
								</div>
								<div class="width1"></div>
								<div class="width8">
									<select class="width100" id="cpanel_manage_server_dst_end_hhmm">
										<? include ("inc/inc_dt.hours_minutes.php"); ?>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['ALLOW_TO_ADD_OBJECTS']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_obj_add" style="width:100px;" onChange="serverValuesCheck();">
										<option value="false"><? echo $la['NO']; ?></option>
										<option value="trial"><? echo $la['TRIAL']; ?></option>
										<option value="limited"><? echo $la['LIMITED']; ?></option>
										<option value="unlimited"><? echo $la['UNLIMITED']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['OBJECT_LIMIT']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_obj_num" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 10"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['OBJECT_VALIDITY_PERIOD_DAYS_AFTER_REGISTRATION']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_obj_dt" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 30"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['OBJECT_TRIAL_PERIOD']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_obj_trial_period" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['ALLOW_TO_EDIT_OBJECTS']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_obj_edit" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['ALLOW_TO_CLEAR_OBJECT_HISTORY']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_obj_history_clear" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['HISTORY']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_history" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['REPORTS']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_reports" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['RFID_AND_IBUTTON_LOGBOOK']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_rilogbook" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['OBJECT_CONTROL']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_object_control" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['IMAGE_GALLERY']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_image_gallery" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['CHAT']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_chat" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['USE_SERVER_SMS_GATEWAY']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_sms_gateway_server" style="width:100px;">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['NOTIFICATIONS']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['REMIND_USER_ABOUT_EXPIRING_OBJECTS']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_notify_obj_expire" style="width:100px;" onChange="serverValuesCheck();">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
									<input id="cpanel_manage_server_notify_obj_expire_period" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['REMIND_USER_ABOUT_EXPIRING_ACCOUNT']; ?>
								</div>
								<div class="width50">
									<select id="cpanel_manage_server_notify_account_expire" style="width:100px;" onChange="serverValuesCheck();">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
									<input id="cpanel_manage_server_notify_account_expire_period" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['BILLING']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYMENT_TYPE']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_payment_type" onChange="serverValuesCheck();">
										<option value=""><? echo $la['NONE']; ?></option>
										<option value="url">URL</option>
										<option value="paypal">PayPal</option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['URL_TO_PAYMENT']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_url" class="inputbox width70" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYPAL_ACCOUNT']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_paypal_account" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> my@email.com"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYPAL_PAYMENT_NAME']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_paypal_name" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EX_ANNUAL_FEE']; ?>"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYPAL_PAYMENT_CURRENCY']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_paypal_cur" class="inputbox" style="width: 100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> EUR"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYPAL_PAYMENT_AMOUNT_FOR_ONE_OBJECT']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_paypal_amount" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 20"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYPAL_CUSTOM']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_paypal_custom" class="inputbox" style="width: 100px;" />
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['PAYPAL_IPN_URL']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_payment_paypal_ipn_url" class="inputbox width70" readOnly="true"/>
								</div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['OTHER']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['ALLOW_TO_SCHEDULE_REPORTS']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_reports_schedule" />
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['MAX_MARKERS']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_places_markers" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" />
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['MAX_ROUTES']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_places_routes" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" />
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['MAX_ZONES']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_places_zones" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="manage_server_templates">
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['TEMPLATES']; ?></div>
							<div class="width100">
								<table id="cpanel_manage_server_template_list_grid"></table>
							</div>
						</div>
					</div>
				</div>
				<div id="manage_server_email">				
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['EMAIL_SETTINGS']; ?></div>
							<div class="row2">
								<div class="width50">
									<? echo $la['EMAIL_ADDRESS']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_email" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> server@email.com"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['NO_REPLY_EMAIL_ADDRESS']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_email_no_reply" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> no_reply@email.com"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SIGNATURE']; ?>
								</div>
								<div class="width50">
									<textarea id="cpanel_manage_server_email_signature" class="inputbox width70" style="height: 50px;" type='text' maxlength="200"></textarea>
								</div>
							</div>	
							<div class="row2">
								<div class="width50">
									<? echo $la['USE_SMTP_SERVER']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_email_smtp" onChange="serverValuesCheck();">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SMTP_SERVER_HOST']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_email_smtp_host" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> smtp.gmail.com"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SMTP_SERVER_PORT']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_email_smtp_port" class="inputbox width70" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 465"/>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SMTP_AUTH']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_email_smtp_auth">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SMTP_SECURITY']; ?>
								</div>
								<div class="width50">
									<select style="width: 100px;" id="cpanel_manage_server_email_smtp_secure">
										<option value=""><? echo $la['NONE']; ?></option>
										<option value="ssl">SSL</option>
										<option value="tls">TLS</option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SMTP_USERNAME']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_email_smtp_username" class="inputbox width70" maxlength="50" />
								</div>
							</div>
							<div class="row2">
								<div class="width50">
									<? echo $la['SMTP_PASSWORD']; ?>
								</div>
								<div class="width50">
									<input id="cpanel_manage_server_email_smtp_password" type="password" class="inputbox width70" maxlength="50" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="manage_server_sms">				
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['SMS_GATEWAY']; ?></div>
							<div class="row2">
								<div class="width40">
									<? echo $la['ENABLE_SMS_GATEWAY']; ?>
								</div>
								<div class="width21">
									<select class="width100" id="cpanel_manage_server_sms_gateway">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['SMS_GATEWAY_TYPE']; ?></div>
								<div class="width21">
									<select class="width100" id="cpanel_manage_server_sms_gateway_type" onchange="serverValuesCheck()">
										<option value="app" selected><? echo $la['MOBILE_APPLICATION'];?></option>
										<option value="http">HTTP</option>
									</select>
								</div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['SMS_GATEWAY_NUMBER_FILTER']; ?></div>
								<div class="width60">
									<input class="inputbox" id="cpanel_manage_server_sms_gateway_number_filter" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> +370, +7, +44, +..."/>
								</div>
							</div>
							<div id="cpanel_manage_server_sms_app">
								<div class="title-block"><? echo $la['MOBILE_APPLICATION'];?></div>
								<div class="row3"><? echo $la['SMS_GATEWAY_MOBILE_APPLICATION_EXPLANATION']; ?></div>
								<div class="row2">
									<div class="width40"><? echo $la['SMS_GATEWAY_IDENTIFIER']; ?></div>
									<div class="width60">
										<input class="inputbox" id="cpanel_manage_server_sms_gateway_identifier" readonly />
									</div>
								</div>
								<div class="row2">
									<div class="width40"><? echo $la['TOTAL_SMS_IN_QUEUE_TO_SEND']; ?></div>
									<div class="width10" id="cpanel_manage_server_sms_gateway_total_in_queue">0</div>
									<div class="width1"></div>
									<div class="width10">
										<input class="button width100" type="button" onclick="SMSGatewayClearQueue();" value="<? echo $la['CLEAR']; ?>" />
									</div>
								</div>
							</div>
							
							<div id="cpanel_manage_server_sms_http" style="display: none;">
								<div class="title-block">HTTP</div>
								<div class="row3"><? echo $la['SMS_GATEWAY_EXPLANATION']; ?></div>
								<div class="row3"><? echo $la['SMS_GATEWAY_EXAMPLE']; ?></div>
								<div class="row2">
									<div class="width40"><? echo $la['SMS_GATEWAY_URL']; ?></div>
									<div class="width60">
										<textarea id="cpanel_manage_server_sms_gateway_url" style="height: 75px;" class="inputbox width100" maxlength="2048" placeholder="<? echo $la['EXAMPLE_SHORT'].' '.$la['HTTP_FULL_ADDRESS_HERE']; ?>"/></textarea>
									</div>
								</div>
								
								<div class="title-block"><? echo $la['VARIABLES']; ?></div>
								<div class="row3"><? echo $la['VAR_SMS_GATEWAY_NUMBER']; ?></div>
								<div class="row3"><? echo $la['VAR_SMS_GATEWAY_MESSAGE']; ?></div>
							</div>
						</div>
					</div>
				</div>
				    
				<div id="manage_server_tools">
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['SERVER_CLEANUP']; ?></div>
							<div class="row2">
								<div class="width40">
									<? echo $la['SERVER_CLEANUP_USERS']; ?>
								</div>
								<div class="width12">
									<? echo $la['LAST_LOGIN_DAYS_AGO']; ?>
								</div>
								<div class="width12">
									<input id="cpanel_manage_server_tools_server_cleanup_users_days" onkeypress="return isNumberKey(event);" class="inputbox width90" maxlength="5" />
								</div>
								<div class="width12">
									<? echo $la['AUTO_EXECUTE']; ?>
								</div>
								<div class="width12">
									<select id="cpanel_manage_server_tools_server_cleanup_users_ae" class="width90">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
								<div class="width12">
									<input class="button icon-create icon" type="button" onclick="serverTools('server_cleanup_users');" value="<? echo $la['EXECUTE_NOW']; ?>" />
								</div>
							</div>
							<div class="row2">
								<div class="width40">
									<? echo $la['SERVER_CLEANUP_OBJECTS_NOT_ACTIVATED']; ?>
								</div>
								<div class="width12">
									<? echo $la['MORE_THAN_DAYS']; ?>
								</div>
								<div class="width12">
									<input id="cpanel_manage_server_tools_server_cleanup_objects_not_activated_days" onkeypress="return isNumberKey(event);" class="inputbox width90" maxlength="5" />
								</div>
								<div class="width12">
									<? echo $la['AUTO_EXECUTE']; ?>
								</div>
								<div class="width12">
									<select id="cpanel_manage_server_tools_server_cleanup_objects_not_activated_ae" class="width90">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
								<div class="width12">
									<input class="button icon-create icon" type="button" onclick="serverTools('server_cleanup_objects_not_activated');" value="<? echo $la['EXECUTE_NOW']; ?>" />
								</div>
							</div>
							<div class="row2">
								<div class="width40">
									<? echo $la['SERVER_CLEANUP_OBJECTS_NOT_USED']; ?>
								</div>
								<div class="width12">
								</div>
								<div class="width12">
								</div>
								<div class="width12">
									<? echo $la['AUTO_EXECUTE']; ?>
								</div>
								<div class="width12">
									<select id="cpanel_manage_server_tools_server_cleanup_objects_not_used_ae" class="width90">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
								<div>
									<input class="button icon-create icon" type="button" onclick="serverTools('server_cleanup_objects_not_used');" value="<? echo $la['EXECUTE_NOW']; ?>" />
								</div>
							</div>
							<div class="row2">
								<div class="width40">
									<? echo $la['SERVER_CLEANUP_DB_JUNK']; ?>
								</div>
								<div class="width12">
								</div>
								<div class="width12">
								</div>
								<div class="width12">
									<? echo $la['AUTO_EXECUTE']; ?>
								</div>
								<div class="width12">
									<select id="cpanel_manage_server_tools_server_cleanup_db_junk_ae" class="width90">
										<option value="true"><? echo $la['YES']; ?></option>
										<option value="false"><? echo $la['NO']; ?></option>
									</select>
								</div>
								<div class="width12">
									<input class="button icon-create icon" type="button" onclick="serverTools('server_cleanup_db_junk');" value="<? echo $la['EXECUTE_NOW']; ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="manage_server_logs">
					<div class="row3">
						<div class="width-1000">
							<div class="title-block"><? echo $la['LOG_VIEWER']; ?></div>
							<div class="row2">
								<div class="width100">
									<div class="float-right">
										<a href="#" onclick="serverTools('logs');">
											<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
												<img src="theme/images/refresh2.png" border="0"/>
											</div>
										</a>
										<a href="#" onclick="logDeleteAll();">
											<div class="panel-button"  title="<? echo $la['DELETE_ALL']; ?>">
												<img src="theme/images/trash.png" border="0"/>
											</div>
										</a>
									</div>
								</div>
							</div>
							<div class="width100">
								<table id="cpanel_manage_server_log_list_grid"></table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	    
		<div id="dialog_send_email" title="<? echo $la['SEND_EMAIL']; ?>">
			<div class="row3">
				<div class="row2">
					<div class="width20"><? echo $la['SEND_TO']; ?></div>
					<div class="width80">
						<select id="send_email_send_to" class="width100" onchange="sendEmailSendToSwitch('test');">
							<option value="all"><? echo $la['ALL_USER_ACCOUNTS']; ?></option>
							<option value="selected"><? echo $la['SELECTED_USER_ACCOUNTS']; ?></option>
						</select>
					</div>
				</div>
				<div class="row2" id="send_email_username_row">
					<div class="width20"><? echo $la['USERNAME']; ?></div>
					<div class="width80"><select id="send_email_username" multiple="multiple" class="width100"></select></div>
				</div>
				<div class="row2">
					<div class="width20"><? echo $la['SUBJECT']; ?></div>
					<div class="width80"><input id="send_email_subject" class="inputbox" type="text" value="" maxlength="50"></div>
				</div>
				<div class="row">
					<div class="width20"><? echo $la['MESSAGE']; ?></div>
					<div class="width80"><textarea id="send_email_message" class="inputbox" style="height: 250px;" type='text'></textarea></div>
				</div>
				<div class="row">
					<div class="width20"><? echo $la['STATUS']; ?></div>
					<div class="width80"><div id="send_email_status" style="text-align:center;"></div></div>
				</div>
			</div>
			
			<center>
				<input class="button icon-time icon" type="button" onclick="sendEmail('test');" value="<? echo $la['TEST']; ?>" />&nbsp;
				<input class="button icon-create icon" type="button" onclick="sendEmail('send');" value="<? echo $la['SEND']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="sendEmail('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
		
		<div id="dialog_object_add" title="<? echo $la['ADD_OBJECT'] ?>">		
			<div class="row3">
				<div class="row2">
					<div class="width40"><? echo $la['NAME']; ?></div>
					<div class="width60"><input id="dialog_object_add_name" class="inputbox" type="text" value="" maxlength="20"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['IMEI']; ?></div>
					<div class="width60"><input id="dialog_object_add_imei" class="inputbox" type="text" maxlength="15"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['TRANSPORT_MODEL']; ?></div>
					<div class="width60"><input id="dialog_object_add_model" class="inputbox" type="text" value="" maxlength="30"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['VIN']; ?></div>
					<div class="width60"><input id="dialog_object_add_vin" class="inputbox" type="text" maxlength="20"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['PLATE_NUMBER']; ?></div>
					<div class="width60"><input id="dialog_object_add_plate_number" class="inputbox" type="text" maxlength="15"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['GPS_DEVICE']; ?></div>
					<div class="width60"><select class="width100" id="dialog_object_add_device"></select></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['SIM_CARD_NUMBER']; ?></div>
					<div class="width60"><input id="dialog_object_add_sim_number" class="inputbox" type="text" value="" maxlength="30"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['MANAGER']; ?></div>
					<div class="width60"><select class="width100" id="dialog_object_add_manager_id"></select></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['ACTIVE']; ?></div>
					<div class="width60"><input id="dialog_object_add_active" class="checkbox" type="checkbox" /></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['ACTIVE_TILL']; ?></div>
					<div class="width60"><input class="inputbox-calendar inputbox width100" id="dialog_object_add_active_dt"/></div>
				</div>
				<div class="row2">
					<div class="width100">
						<select id="dialog_object_add_users" multiple="multiple" class="width100"></select>
					</div>
				</div>	
			</div>
			
			<center>
				<input class="button icon-new icon" type="button" onclick="objectAdd('add');" value="<? echo $la['ADD']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="objectAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
	    
		<div id="dialog_user_object_add" title="<? echo $la['ADD_OBJECT'] ?>">
			<div class="row3">
				<div class="row2">
					<div class="width100">
						<select id="dialog_user_object_add_objects" multiple="multiple" class="width100"></select>
					</div>
				</div>
			</div>
			<center>
				<input class="button icon-new icon" type="button" onclick="userObjectAdd('add');" value="<? echo $la['ADD']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="userObjectAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
		
		<div id="dialog_user_add" title="<? echo $la['ADD_USER']; ?>">
			<div class="row3">
				<div class="row2">
					<div class="width40"><? echo $la['EMAIL']; ?></div>
					<div class="width60"><input id="dialog_user_add_email" class="inputbox" type="text" maxlength="50"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['SEND_CREDENTIALS']; ?></div>
					<div class="width60"><input id="dialog_user_add_send" type="checkbox" class="checkbox" checked/></div>
				</div>
			</div>
			
			<center>
				<input class="button icon-new icon" type="button" onclick="userAdd('register');" value="<? echo $la['REGISTER']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="userAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
		
		<div id="dialog_object_edit" title="<? echo $la['EDIT_OBJECT']; ?>">
			<div class="row3">
				<div class="row2">
					<div class="width40"><? echo $la['NAME']; ?></div>
					<div class="width60"><input id="dialog_object_edit_name" class="inputbox" type="text" maxlength="20" /></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['IMEI']; ?></div>
					<div class="width60"><input id="dialog_object_edit_imei" class="inputbox" type="text" maxlength="15" disabled /></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['TRANSPORT_MODEL']; ?></div>
					<div class="width60"><input id="dialog_object_edit_model" class="inputbox" type="text" value="" maxlength="30"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['VIN']; ?></div>
					<div class="width60"><input id="dialog_object_edit_vin" class="inputbox" type="text" maxlength="20"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['PLATE_NUMBER']; ?></div>
					<div class="width60"><input id="dialog_object_edit_plate_number" class="inputbox" type="text" maxlength="15"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['GPS_DEVICE']; ?></div>
					<div class="width60"><select class="width100" id="dialog_object_edit_device"></select></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['SIM_CARD_NUMBER']; ?></div>
					<div class="width60"><input id="dialog_object_edit_sim_number" class="inputbox" type="text" value="" maxlength="30"></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['MANAGER']; ?></div>
					<div class="width60"><select class="width100" id="dialog_object_edit_manager_id"></select></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['ACTIVE']; ?></div>
					<div class="width60"><input id="dialog_object_edit_active" class="checkbox" type="checkbox" /></div>
				</div>
				<div class="row2">
					<div class="width40"><? echo $la['ACTIVE_TILL']; ?></div>
					<div class="width60"><input class="inputbox-calendar inputbox width100" id="dialog_object_edit_active_dt"/></div>
				</div>
				<div class="row2">
					<div class="width100">
						<select id="dialog_object_edit_users" multiple="multiple" class="width100"></select>
					</div>
				</div>	
			</div>
			
			<center>
				<input class="button icon-save icon" type="button" onclick="objectEdit('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="objectEdit('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
		
		<div id="dialog_user_edit" title="<? echo $la['EDIT_USER']; ?>">
			<div id="dialog_user_edit_tabs">
				<ul>           
					<li><a href="#dialog_user_edit_account"><? echo $la['ACCOUNT']; ?></a></li>
					<li><a href="#dialog_user_edit_contact_info"><? echo $la['CONTACT_INFO']; ?></a></li>
					<li><a href="#dialog_user_edit_subaccounts"><? echo $la['SUB_ACCOUNTS']; ?></a></li>
					<li><a href="#dialog_user_edit_objects"><? echo $la['OBJECTS']; ?></a></li>
				</ul>
				
				<div id="dialog_user_edit_account">
					<div class="controls">
						<input class="button icon-save icon" type="button" onclick="userEdit('save');" value="<? echo $la['SAVE']; ?>">
						<input class="button icon-key icon" type="button" onclick="userEditLogin();" value="<? echo $la['LOGIN_AS_USER']; ?>">
					</div>					
					<div class="block width50">							
						<div class="container">
							<div class="title-block"><? echo $la['USER']; ?></div>
							<div class="row2">
								<div class="width40"><? echo $la['ACTIVE']; ?></div>
								<div class="width60"><input id="dialog_user_edit_account_active" class="checkbox" type="checkbox" /></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['USERNAME']; ?></div>
								<div class="width60"><input id="dialog_user_edit_account_username" class="inputbox" maxlength="50" /></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['EMAIL']; ?></div>
								<div class="width60"><input id="dialog_user_edit_account_email" class="inputbox" maxlength="50" /></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['PASSWORD']; ?></div>
								<div class="width60"><input id="dialog_user_edit_account_password" class="inputbox" maxlength="20" placeholder="<? echo $la['ENTER_NEW_PASSWORD']; ?>"/></div>
							</div>
							<div class="row2">
								<div class="width40">
									<? echo $la['EXPIRE_ACCOUNT']; ?>
								</div>
								<div class="width10">
									<input id="dialog_user_edit_account_expire" type="checkbox" class="checkbox"/>
								</div>
								<div class="width50">
									<input readonly class="inputbox-calendar inputbox width100" id="dialog_user_edit_account_expire_dt"/>
								</div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['PRIVILEGES']; ?></div>
								<div class="width60"><select class="width100" id="dialog_user_edit_account_privileges" onChange="userEditCheck();"></select></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['MANAGER']; ?></div>
								<div class="width60"><select class="width100" id="dialog_user_edit_account_manager_id" onChange="userEditCheck();"></select></div>
							</div>
						</div>
					</div>
					
					<div class="block width50">			
						<div class="container last">
							<div class="title-block"><? echo $la['PRIVILEGES']; ?></div>
							<div style="height: 460px; overflow-y: scroll;">
								<div class="row2">
									<div class="width50"><? echo $la['ALLOW_TO_ADD_OBJECTS']; ?></div>
									<div class="width50">
										<select id="dialog_user_edit_account_obj_add" style="width: 100px;" onChange="userEditCheck();">
											<option value="false"><? echo $la['NO']; ?></option>
											<option value="trial"><? echo $la['TRIAL']; ?></option>
											<option value="limited"><? echo $la['LIMITED']; ?></option>
											<option value="unlimited"><? echo $la['UNLIMITED']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['OBJECT_LIMIT_ACCOUNT']; ?></div>
									<div class="width50"><input id="dialog_user_edit_account_obj_num" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4"/></div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['OBJECT_LIMIT_MANAGER']; ?></div>
									<div class="width50"><input id="dialog_user_edit_account_manager_obj_num" onkeypress="return isNumberKey(event);" class="inputbox" style="width:100px;" maxlength="4"/></div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['DATE_LIMIT']; ?></div>
									<div class="width50"><input class="inputbox-calendar inputbox" style="width:100px;" id="dialog_user_edit_account_obj_dt"/></div>
								</div>									
								<div class="row2">
									<div class="width50"><? echo $la['ALLOW_TO_EDIT_OBJECTS']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_obj_edit">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['ALLOW_TO_CLEAR_OBJECT_HISTORY']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_obj_history_clear">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>																		
								<div class="row2">
									<div class="width50"><? echo $la['HISTORY']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_history">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['REPORTS']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_reports">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['RFID_AND_IBUTTON_LOGBOOK']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_rilogbook">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['OBJECT_CONTROL']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_object_control">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['IMAGE_GALLERY']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_image_gallery">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['CHAT']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_chat">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['USE_SERVER_SMS_GATEWAY']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_account_sms_gateway_server">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50">
										<? echo $la['MAX_MARKERS']; ?>
									</div>
									<div class="width50">
										<input id="dialog_user_edit_places_markers" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" />
									</div>
								</div>
								<div class="row2">
									<div class="width50">
										<? echo $la['MAX_ROUTES']; ?>
									</div>
									<div class="width50">
										<input id="dialog_user_edit_places_routes" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" />
									</div>
								</div>
								<div class="row2">
									<div class="width50">
										<? echo $la['MAX_ZONES']; ?>
									</div>
									<div class="width50">
										<input id="dialog_user_edit_places_zones" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="4" />
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['ALLOW_TO_USE_API']; ?></div>
									<div class="width50">
										<select style="width: 100px;" id="dialog_user_edit_api_active">
											<option value="true"><? echo $la['YES']; ?></option>
											<option value="false"><? echo $la['NO']; ?></option>
										</select>
									</div>
								</div>
								<div class="row2">
									<div class="width50"><? echo $la['API_KEY']; ?></div>
									<div class="width50">
										<input id="dialog_user_edit_api_key" class="inputbox width95" readOnly="true"/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="dialog_user_edit_contact_info">
					<div class="controls">
						<input class="button icon-save icon" type="button" onclick="userEdit('save');" value="<? echo $la['SAVE']; ?>">
						<input class="button icon-key icon" type="button" onclick="userEditLogin();" value="<? echo $la['LOGIN_AS_USER']; ?>">
					</div>
					
					<div class="block width100">	
						<div class="container last">
							<div class="title-block"><? echo $la['CONTACT_INFO']; ?></div>
							<div class="row2">
								<div class="width40"><? echo $la['NAME_SURNAME']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_surname"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['COMPANY']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_company"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['ADDRESS']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_address"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['POST_CODE']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_post_code"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['CITY']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_city"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['COUNTRY_STATE']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_country"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['PHONE_NUMBER_1']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_phone1"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['PHONE_NUMBER_2']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_phone2"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['EMAIL']; ?></div>
								<div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_email"></div>
							</div>
							<div class="row2">
								<div class="width40"><? echo $la['COMMENT']; ?></div>
								<div class="width60">
									<textarea id="dialog_user_edit_account_comment" class="inputbox" style="height:109px;" maxlength="500" placeholder="<? echo $la['COMMENT_ABOUT_USER']; ?>"></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="dialog_user_edit_subaccounts">
					<div class="row2">
						<div class="width100">
							<div class="float-right">
								<a href="#" onclick="userSubaccountDeleteSelected();">
									<div class="panel-button"  title="<? echo $la['DELETE_SELECTED_USERS']; ?>">
										<img src="theme/images/trash.png" border="0"/>
									</div>
								</a>
							</div>
						</div>
					</div>
					<div id="dialog_user_edit_subaccount_list">
						<table id="dialog_user_edit_subaccount_list_grid"></table>
						<div id="dialog_user_edit_subaccount_list_grid_pager"></div>
					</div>
				</div>
				
				<div id="dialog_user_edit_objects">
					<div class="row2">
						<div class="width15">
							<? echo $la['ACTIVE_PERIOD']; ?>	
						</div>
						<div class="width5">
							<input class="checkbox" type="checkbox" id="dialog_user_edit_object_active_period_active" checked/>
						</div>
						<div class="width80">
							<input class="inputbox-calendar inputbox float-left" style="width:100px; margin-right: 2px;" id="dialog_user_edit_object_active_period_active_dt"/>
							<input class="button icon-refresh icon float-left" style="margin-right: 2px;" type="button" value="<? echo $la['GET_AVG_DATE']; ?>" onclick="userObjectActivePeriodGetAvgDate();"/>
							<input class="button icon-save icon float-left" style="margin-right: 2px;" type="button" value="<? echo $la['SET_SELECTED']; ?>" onclick="userObjectActivePeriodSetSelected();"/>
							<div class="float-right">
								<a href="#" onclick="userObjectDeleteSelected();">
									<div class="panel-button"  title="<? echo $la['DELETE_SELECTED_OBJECTS']; ?>">
										<img src="theme/images/trash.png" border="0"/>
									</div>
								</a>
							</div>
						</div>
					</div>
					<div id="dialog_user_edit_object_list">
						<table id="dialog_user_edit_object_list_grid"></table>
						<div id="dialog_user_edit_object_list_grid_pager"></div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="dialog_custom_map_properties" title="<? echo $la['CUSTOM_MAP_PROPERTIES'];?>">
			<div class="row3">
				<div class="title-block"><? echo $la['CUSTOM_MAP']; ?></div>
				<div class="row2">
					<div class="width30"><? echo $la['NAME']; ?></div>
					<div class="width30"><input id="dialog_custom_map_name" class="inputbox" type="text" value="" maxlength="50"></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['ACTIVE']; ?></div>
					<div class="width70"><input id="dialog_custom_map_active" type="checkbox" checked="checked"/></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['TYPE']; ?></div>
					<div class="width70">
						<select style="width: 100px;" id="dialog_custom_map_type">
							<option value="tms">TMS</option>
							<option value="wms">WMS</option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['URL']; ?></div>
					<div class="width70"><input id="dialog_custom_map_url" class="inputbox" type="text" value=""></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['LAYERS']; ?></div>
					<div class="width70"><input id="dialog_custom_map_layers" class="inputbox" type="text" value=""></div>
				</div>
			</div>
			
			<center>
				<input class="button icon-save icon" type="button" onclick="customMapProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="customMapProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
	    
		<div id="dialog_template_properties" title="<? echo $la['TEMPLATE_PROPERTIES'];?>">
			<div class="row3">
				<div class="block width60">
					<div class="container">
						<div class="title-block"><? echo $la['TEMPLATE']; ?></div>
						<div class="row2">
							<div class="width30"><? echo $la['NAME']; ?></div>
							<div class="width70"><input id="dialog_template_name" class="inputbox" type="text" value="" maxlength="50" readonly></div>
						</div>
						<div class="row2">
							<div class="width30"><? echo $la['LANGUAGE']; ?>
							</div>
							<div class="width70">
								<select id="dialog_template_language" onChange="templateProperties('load');">
									<? echo getLanguageList(); ?>
								</select>
							</div>
						</div>
						<div class="row2">
							<div class="width30"><? echo $la['SUBJECT']; ?></div>
							<div class="width70"><input id="dialog_template_subject" class="inputbox" maxlength="100"></div>
						</div>
						<div class="row2">
							<div class="width30"><? echo $la['MESSAGE']; ?></div>
							<div class="width70"><textarea id="dialog_template_message" class="inputbox" style="height:255px;" maxlength="2000"></textarea></div>
						</div>
					</div>
				</div>
				<div class="block width40">
					<div class="container last">
						<div class="title-block"><? echo $la['VARIABLES']; ?></div>
						<div class="row2">
							<div id="dialog_template_variables" style="height: 334px; overflow-y: scroll;"></div>
						</div>
					</div>
				</div>
			</div>
			
			<center>
				<input class="button icon-save icon" type="button" onclick="templateProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
				<input class="button icon-close icon" type="button" onclick="templateProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
			</center>
		</div>
	</body>
</html>