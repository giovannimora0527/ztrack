<div id="map"></div>

<div id="history_view_control" class="history-view-control">
	<div class="row4">
		<div class="margin-right-3"><input id="history_view_control_route" type="checkbox" class="checkbox" onclick="historyRouteToggleRoute();" checked/></div>
		<div class="margin-right-3"><? echo $la['ROUTE']; ?></div>
		<div class="margin-right-3"><input id="history_view_control_snap" type="checkbox" class="checkbox" onclick="historyRouteToggleSnap();"/></div>
		<div class="margin-right-3"><? echo $la['SNAP']; ?></div>
		<div class="margin-right-3"><input id="history_view_control_arrows" type="checkbox" class="checkbox" onclick="historyRouteToggleArrows();"/></div>
		<div class="margin-right-3"><? echo $la['ARROWS']; ?></div>
		<div class="margin-right-3"><input id="history_view_control_data_points" type="checkbox" class="checkbox" onclick="historyRouteToggleDataPoints();"/></div>
		<div class="margin-right-3"><? echo $la['DATA_POINTS']; ?></div>
		<div class="margin-right-3"><input id="history_view_control_stops" type="checkbox" class="checkbox" onclick="historyRouteToggleStops();" checked/></div>
		<div class="margin-right-3"><? echo $la['STOPS']; ?></div>
		<div class="margin-right-3"><input id="history_view_control_events" type="checkbox" class="checkbox" onclick="historyRouteToggleEvents();" checked/></div>
		<div class="margin-right-3"><? echo $la['EVENTS']; ?></div>
		<div class="margin-left-3">
			<a class="button icon-close" href="#" onclick="historyHideRoute();" title="<? echo $la['HIDE'];?>">&nbsp;</a>
		</div>
	</div>
</div>

<div id="loading_panel">
    <div class="table">
	<div class="table-cell center-middle">
	    <div id="loading_panel_msg">
		<img style="border:0px" src="<? echo $gsValues['URL_LOGO']; ?>" /><br/><br/>
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
		<img style="border:0px" src="<? echo $gsValues['URL_LOGO']; ?>" /><br/><br/>
		<? echo $la['YOUR_SESSION_HAS_EXPIRED']; ?>
	    </div>
	</div>
    </div>
</div>

<div id="top_panel">
	<ul class="left-menu">
		<? if ($gsValues['SHOW_ABOUT'] == 'true') { ?>
<!--		    <li class="logo-btn">
			<a href="#" onclick="$('#dialog_about').dialog('open');" title="<? echo $la['ABOUT']; ?>">
			    <img src="theme/images/earth.png" border="0"/>
			</a>
		    </li>-->
		<? } ?>
		
<!--		<li>
			<a class="help_btn" href="<? echo $gsValues['URL_HELP']; ?>" target="_blank" title="<? echo $la['HELP']; ?>">
			    <img src="theme/images/help.png" border="0"/>
			</a>
		</li>-->
		<li>
			<a class="settings_btn" href="#" onclick="settingsOpen();" title="<? echo $la['SETTINGS']; ?>">
			    <img src="theme/images/settings.png" border="0"/>
			</a>
		</li>
		<li>
			<a class="fit_btn" href="#" onclick="fitObjectsOnMap();" title="<? echo $la['FIT_OBJECTS_ON_MAP']; ?>">
			    <img src="theme/images/zoom_fit.png" border="0"/>
			</a>
		</li>
<!--		<li>
			<a id="top_panel_button_ruler" class="ruler_btn" href="#" onclick="utilsRuler();" title="<? echo $la['RULER']; ?>">
			    <img src="theme/images/ruler.png" border="0"/>
			</a>
		</li>-->
<!--		<li>
			<a id="top_panel_button_area" class="area_btn" href="#" onclick="utilsArea();" title="<? echo $la['MEASURE_AREA']; ?>">
			    <img src="theme/images/area.png" border="0"/>
			</a>
		</li>-->
		<li>
			<a class="point_btn" href="#" onclick="$('#dialog_show_point').dialog('open');" title="<? echo $la['SHOW_POINT']; ?>">
			    <img src="theme/images/point.png" border="0"/>
			</a>
		</li>
<!--		<li>
			<a class="search_btn" href="#" onclick="$('#dialog_address_search').dialog('open');" title="<? echo $la['ADDRESS_SEARCH']; ?>">
			    <img src="theme/images/search.png" border="0"/>
			</a>
		</li>-->
		<li>
			<a class="report_btn" href="#" onclick="historyReportsOpen();" title="<? echo $la['REPORTS']; ?>">
			    <img src="theme/images/report.png" border="0"/>
			</a>
		</li>
<!--		<li>
			<a class="report_btn" href="#" onclick="rilogbookOpen();" title="<? echo $la['RFID_AND_IBUTTON_LOGBOOK']; ?>">
			    <img src="theme/images/logbook.png" border="0"/>
			</a>
		</li>-->
<!--		<li>
			<a class="cmd_btn" href="#" id="top_panel_button_object_control" onclick="cmdOpen();" title="<? echo $la['OBJECT_CONTROL']; ?>">
			    <img src="theme/images/cmd.png" border="0"/>
			</a>
		</li>-->
<!--		<li>
			<a class="gallery_btn" href="#" onclick="imgOpen();" title="<? echo $la['IMAGE_GALLERY']; ?>">
			    <img src="theme/images/gallery.png" border="0"/>
			</a>
		</li>-->
		<li>
			<a class="chat_btn" href="#" onclick="chatOpen();" title="<? echo $la['CHAT']; ?>">
			    <img class="float-left" src="theme/images/chat.png" border="0"/>
			    <div id="chat_msg_count" class="chat-msg-count float-right">0</div>
			</a>
		</li>
		<li class="select-map">
			<select id="map_layer" onChange="switchMapLayer($(this).val());"></select>
		</li>
	</ul>
    
	<ul class="right-menu">
		<li class="select-language <? if ($_SESSION["cpanel_privileges"]){?>cp<? }?>">
			<select id="system_language" onChange="switchLanguageTracking();">
			<? echo getLanguageList(); ?>
			</select>
		</li>
		<? if ($_SESSION["cpanel_privileges"]){?>
		<li class="cpanel-btn">
<!--			<a href="cpanel.php" title="CPanel">
			    <img src="theme/images/cpanel.png" border="0"/>
			</a>-->
		</li>
		<? }?>
		<li>
			<a class="user-btn" href="#" onclick="settingsOpenUser();" title="<? echo $la['MY_ACCOUNT']; ?>">
			<img src="theme/images/user.png" border="0"/><span><? echo truncateString($_SESSION["username"], 10);?></span>
			</a>
		</li>
		<li>
			<a class="mobile_btn" href="mobile/tracking.php" title="<? echo $la['MOBILE_VERSION']; ?>">
			<img src="theme/images/mobile.png" border="0"/>
			</a>
		</li>
		<li class="logout-btn">
			<a href="#" onclick="connectLogout();" title="<? echo $la['LOGOUT']; ?>">
			<img src="theme/images/sign_out.png" border="0"/>
			</a>
		</li>
	</ul>
</div>

<div id="side_panel">
	<ul>           
		<li><a href="#side_panel_objects"><? echo truncateString($la['OBJECTS'], 10); ?></a></li>
		<li><a href="#side_panel_events"><? echo truncateString($la['EVENTS'], 10); ?></a></li>
		<li><a href="#side_panel_places"><? echo truncateString($la['PLACES'], 10); ?></a></li>
		<li><a href="#side_panel_history_reports"><? echo truncateString($la['HISTORY'], 10); ?></a></li>
	</ul>
	      
	<div id="side_panel_objects">
		<div id="side_panel_objects_object_list">
			<table id="side_panel_objects_object_list_grid"></table>
		</div>
		<div id="side_panel_objects_dragbar">
		</div>
		<div id="side_panel_objects_object_data_list">
			<table id="side_panel_objects_object_data_list_grid"></table>
		</div>
	</div>
	
	<div id="side_panel_events">
		<div id="side_panel_events_event_list">
		       <table id="side_panel_events_event_list_grid"></table>
		       <div id="side_panel_events_event_list_grid_pager"></div>
	       </div>
	       <div id="side_panel_events_dragbar">
	       </div>
	       <div id="side_panel_events_event_data_list">
		       <table id="side_panel_events_event_data_list_grid"></table>
	       </div>
	</div>
    
	<div id="side_panel_places">
		<ul>
			<li><a href="#side_panel_places_markers"><span><? echo $la['MARKERS']; ?> </span><span id="side_panel_places_markers_num"></span></a></li>
			<li><a href="#side_panel_places_routes"><span><? echo $la['ROUTES']; ?> </span><span id="side_panel_places_routes_num"></span></a></li>
			<li><a href="#side_panel_places_zones"><span><? echo $la['ZONES']; ?> </span><span id="side_panel_places_zones_num"></span></a></li>
		</ul>
		
		<div id="side_panel_places_markers">
			<div id="side_panel_places_marker_list">
				<table id="side_panel_places_marker_list_grid"></table>
				<div id="side_panel_places_marker_list_grid_pager"></div>
			</div>
		</div>
		
		<div id="side_panel_places_routes">
			<div id="side_panel_places_route_list">
				<table id="side_panel_places_route_list_grid"></table>
				<div id="side_panel_places_route_list_grid_pager"></div>
			</div>
		</div>
		
		<div id="side_panel_places_zones">
			<div id="side_panel_places_zone_list">
				<table id="side_panel_places_zone_list_grid"></table>
				<div id="side_panel_places_zone_list_grid_pager"></div>
			</div>
		</div>
	</div>
    
	<div id="side_panel_history_reports">
		<div id="side_panel_history_reports_parameters">
			<div class="row2">
			    <div class="width35"><? echo $la['OBJECT']; ?></div>
			    <div class="width65"><select id="side_panel_history_reports_object_list" class="width100"></select></div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['FILTER'];?></div>
				<div class="width65">
				    <select id="side_panel_history_reports_filter" class="width100" onchange="switchHistoryReportsDateFilter('history');">
					<option value="0" selected></option>
					<option value="1"><? echo $la['LAST_HOUR'];?></option>
					<option value="2"><? echo $la['TODAY'];?></option>
					<option value="3"><? echo $la['YESTERDAY'];?></option>
					<option value="4"><? echo $la['BEFORE_2_DAYS'];?></option>
					<option value="5"><? echo $la['BEFORE_3_DAYS'];?></option>
					<option value="6"><? echo $la['THIS_WEEK'];?></option>
					<option value="7"><? echo $la['LAST_WEEK'];?></option>
					<option value="8"><? echo $la['THIS_MONTH'];?></option>
					<option value="9"><? echo $la['LAST_MONTH'];?></option>
				    </select>
				</div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['TIME_FROM']; ?></div>
				<div class="width31">
					<input readonly class="inputbox-calendar inputbox width100" id="side_panel_history_reports_date_from" type="text" value=""/>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="side_panel_history_reports_hour_from">
					<? include ("inc/inc_dt.hours.php"); ?>
					</select>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="side_panel_history_reports_minute_from">
					<? include ("inc/inc_dt.minutes.php"); ?>
					</select>
				</div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['TIME_TO']; ?></div>
				<div class="width31">
					<input readonly class="inputbox-calendar inputbox width100" id="side_panel_history_reports_date_to" type="text" value=""/>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="side_panel_history_reports_hour_to">
					<? include ("inc/inc_dt.hours.php"); ?>
					</select>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="side_panel_history_reports_minute_to">
					<? include ("inc/inc_dt.minutes.php"); ?>
					</select>
				</div>
			</div>
			
			<div class="row">
				<div class="width35"><? echo $la['STOPS']; ?></div>
				<div class="width31">
					<select id="side_panel_history_reports_stop_duration" class="width100">
						<option value=1>> 1 min</option>
						<option value=2>> 2 min</option>
						<option value=5>> 5 min</option>
						<option value=10>> 10 min</option>
						<option value=20>> 20 min</option>
						<option value=30>> 30 min</option>
						<option value=60>> 1 h</option>
						<option value=120>> 2 h</option>
						<option value=300>> 5 h</option>
					</select>
				</div>
			</div>
	    
			<div class="row">
				<input style="width: 100px; margin-right: 3px;" class="button" type="button" value="<? echo $la['SHOW']; ?>" onclick="historyLoadRoute();"/>
				<input style="width: 100px; margin-right: 3px;" class="button" type="button" value="<? echo $la['HIDE']; ?>" onclick="historyHideRoute();"/>
				<input style="width: 134px;" class="button" type="button" value="<? echo $la['IMPORT_EXPORT']; ?>" onclick="$('#dialog_import_export').dialog('open');"/>
			</div>
		</div>
	
		<div id="side_panel_history_reports_route">
			<table id="side_panel_history_reports_route_review_list_grid"></table>
		</div>
		
		<div id="side_panel_history_reports_dragbar">
		</div>
		
		<div id="side_panel_history_reports_route_data_list">
			<table id="side_panel_history_reports_route_data_list_grid"></table>
		</div>
	</div>
</div>

<div id="bottom_panel">
	<div id="bottom_panel_tabs" style="height: 100%;">
		<ul>           
		    <li><a href="#bottom_panel_graph"><? echo $la['GRAPH']; ?></a></li>
		    <li><a href="#bottom_panel_msg"><? echo $la['MESSAGES']; ?></a></li>
		</ul>
		      
		<div id="bottom_panel_graph">
			<div id="bottom_panel_graph_data_control">
				<select style="min-width:100px; float:left;" id="bottom_panel_graph_data_source" onchange="historyRouteChangeGraphSource();"></select>
				
				<div class="graph-controls float-left">
					<a class="button" href="#" onclick="historyRoutePlay();" title="<? echo $la['PLAY'];?>">
						<img src="theme/images/play.png" border="0"/>
					</a>
				    
					<a href="#" onclick="historyRoutePause();" title="<? echo $la['PAUSE'];?>">
						<img src="theme/images/pause.png" border="0"/>
					</a>
				    
					<a href="#" onclick="historyRouteStop();" title="<? echo $la['STOP'];?>">
						<img src="theme/images/stop.png" border="0"/>
					</a>
				</div>
				
				<select id="historyRoutePlaySpeed">
					<option value=1>x1</option>
					<option value=2>x2</option>
					<option value=3>x3</option>
					<option value=4>x4</option>
					<option value=5>x5</option>
					<option value=6>x6</option>
				</select>
			</div>
			
			<div id="bottom_panel_graph_view_control" class="graph-view-control graph-controls float-right">
				<a href="#" onclick="graphPanLeft();" title="<? echo $la['PAN_LEFT'];?>">
					<img src="theme/images/arrow_left.gif" border="0"/>
				</a>
				
				<a href="#" onclick="graphPanRight();" title="<? echo $la['PAN_RIGHT'];?>">
					<img src="theme/images/arrow_right.gif" border="0"/>
				</a>
				  
				<a href="#" onclick="graphZoomIn();" title="<? echo $la['ZOOM_IN'];?>">
					<img src="theme/images/zoom_in.png" border="0"/>
				</a>
				
				<a href="#" onclick="graphZoomOut();" title="<? echo $la['ZOOM_OUT'];?>">
					<img src="theme/images/zoom_out.png" border="0"/>
				</a>
			</div>
			
			<div id="bottom_panel_graph_label"></div>
			<div id="bottom_panel_graph_plot"></div>
		</div>
		
		<div id="bottom_panel_msg">
			<table id="bottom_panel_msg_list_grid"></table>
			<div id="bottom_panel_msg_list_grid_pager"></div>
		</div>
	</div>
</div>

<a href="#" onclick="showhideLeftPanel();">
	<div id="hide_side_panel">    
	</div>
</a>

<a href="#" onclick="showBottomPanel();">
	<div id="hide_bottom_panel">    
	</div>
</a>