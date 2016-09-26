//#################################################
// VARS
//#################################################

// language array/vars
var la = [];

var cpValues = new Array();
cpValues['edit_custom_map_id'] = false;

// timers
var timer_loadStats;
var timer_sessionCheck;

//#################################################
// END VARS
//#################################################


//#################################################
// DIALOGS, TABS, DATATABLES
//#################################################

function initGui()
{
	// define calendar
	$('.inputbox-calendar').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
		dayNamesMin: [la['DAY_SUNDAY_S'], la['DAY_MONDAY_S'], la['DAY_TUESDAY_S'], la['DAY_WEDNESDAY_S'], la['DAY_THURSDAY_S'], la['DAY_FRIDAY_S'], la['DAY_SATURDAY_S']],
		monthNames: [la['MONTH_JANUARY'], la['MONTH_FEBRUARY'], la['MONTH_MARCH'], la['MONTH_APRIL'], la['MONTH_MAY'], la['MONTH_JUNE'], la['MONTH_JULY'], la['MONTH_AUGUST'], la['MONTH_SEPTEMBER'], la['MONTH_OCTOBER'], la['MONTH_NOVEMBER'], la['MONTH_DECEMBER']]
	});
	
	$('.inputbox-calendar-mmdd').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "mm-dd",
		dayNamesMin: [la['DAY_SUNDAY_S'], la['DAY_MONDAY_S'], la['DAY_TUESDAY_S'], la['DAY_WEDNESDAY_S'], la['DAY_THURSDAY_S'], la['DAY_FRIDAY_S'], la['DAY_SATURDAY_S']],
		monthNames: [la['MONTH_JANUARY'], la['MONTH_FEBRUARY'], la['MONTH_MARCH'], la['MONTH_APRIL'], la['MONTH_MAY'], la['MONTH_JUNE'], la['MONTH_JULY'], la['MONTH_AUGUST'], la['MONTH_SEPTEMBER'], la['MONTH_OCTOBER'], la['MONTH_NOVEMBER'], la['MONTH_DECEMBER']],
		monthNamesShort: [la['MONTH_JANUARY_S'], la['MONTH_FEBRUARY_S'], la['MONTH_MARCH_S'], la['MONTH_APRIL_S'], la['MONTH_MAY_S'], la['MONTH_JUNE_S'], la['MONTH_JULY_S'], la['MONTH_AUGUST_S'], la['MONTH_SEPTEMBER_S'], la['MONTH_OCTOBER_S'], la['MONTH_NOVEMBER_S'], la['MONTH_DECEMBER_S']]
	});
	
	
	$("#manage_server_tabs, #dialog_user_edit_tabs").tabs({
		show: function() {
		var $target = $(ui.panel);
		$('.content:visible').effect(function(){ $target.fadeIn()}); }
	});
	
	$('#dialog_user_object_add_objects').tokenize({
		datas: "func/fn_cpanel.php?cmd=load_object_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_OBJECT_NAME_OR_IMEI'],
		newElements: false
	});
	
	$('#dialog_object_add_users').tokenize({
		datas: "func/fn_cpanel.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_ACCOUNT_USERNAME_OR_EMAIL'],
		newElements: false
	});
	
	$('#dialog_object_edit_users').tokenize({
		datas: "func/fn_cpanel.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_ACCOUNT_USERNAME_OR_EMAIL'],
		newElements: false
	});
	
	$('#send_email_username').tokenize({
		datas: "func/fn_cpanel.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_ACCOUNT_USERNAME_OR_EMAIL'],
		newElements: false
	});
	
	$("#dialog_send_email").dialog({
		autoOpen: false,
		width: "700px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_user_add").dialog({
		autoOpen: false,
		width: "320px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_user_edit").dialog({
		autoOpen: false,
		width: "785px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		close: function(event, ui) {
						$('#cpanel_object_list_grid').trigger("reloadGrid");
						$('#cpanel_user_list_grid').trigger("reloadGrid");
					}
	});
	
	$("#dialog_object_edit").dialog({
		autoOpen: false,
		width: "450px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		close: function(event, ui) {
						$('#cpanel_object_list_grid').trigger("reloadGrid");
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						if ($('#dialog_user_edit').dialog('isOpen') == true)
						{
						       $('#dialog_user_edit_object_list_grid').trigger("reloadGrid"); 
						}
					}
	});
	
	$("#dialog_user_object_add").dialog({
		autoOpen: false,
		width: "320px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_object_add").dialog({
		autoOpen: false,
		width: "450px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_custom_map_properties").dialog({
		autoOpen: false,
		width: "600",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_template_properties").dialog({
		autoOpen: false,
		width: "800",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
}

function initGrids()
{
	// define user list grid
	$("#cpanel_user_list_grid").jqGrid({
		url:'func/fn_cpanel.php?cmd=load_user_list',
		datatype: "json",
		colNames:['ID',la['ACTIVE'],la['ACTIVE_TILL'],la['PRIVILEGES'],la['USERNAME'], la['EMAIL'], 'API', la['REG_DATE'],la['LOGIN_DATE'],'IP',la['SUB_ACC'],la['OBJECTS'],'',''],
		colModel:[
			{name:'id',index:'id',width:50,align:"center",key: true},
			{name:'active',index:'active',width:50,align:"center"},
			{name:'account_expire_dt',index:'account_expire_dt',width:60,align:"center"},
			{name:'privileges',index:'privileges',width:70,align:"center"},
			{name:'username',index:'username',width:150},
			{name:'email',index:'email',width:150},
			{name:'api',index:'api',width:50,align:"center"},
			{name:'dt_reg',index:'dt_reg',width:110,align:"center"},
			{name:'dt_login',index:'dt_login',width:110,align:"center"},		
			{name:'ip',index:'ip',width:110},
			{name:'suba_num',index:'suba_num',width:50,align:"center",sortable: false},
			{name:'obj_num',index:'obj_num',width:50,align:"center",sortable: false},
			{name:'modify',index:'modify',width:90,align:"center",sortable: false, fixed: true},
			{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
		],
		//altRows: true,
		//altclass: 'myAltRowClass',
		rowNum:50,
		rowList:[25,50,100,200,300,400,500],
		pager: '#cpanel_user_list_grid_pager',
		sortname: 'id',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '400px',
		shrinkToFit: true,
		multiselect: true
		//forceFit: true
	});
	$("#cpanel_user_list_grid").jqGrid('navGrid','#cpanel_user_list_grid_pager',{ 	add:true,
											edit:false,
											del:false,
											search:false,
											addfunc: function (e) {userAdd('open');}																		
											});

	$("#cpanel_user_list_grid").setCaption(	'<div class="row4">\
							<div class="float-left">\
								<a href="#" onclick="sendEmail(\'open\');" title="'+la['SEND_EMAIL']+'">\
								<div class="panel-button">\
									<img src="theme/images/email.png" border="0"/>\
								</div>\
								</a>\
								<a href="#" onclick="userDeleteSelected();">\
								<div class="panel-button"  title="'+la['DELETE_SELECTED_USERS']+'">\
								<img src="theme/images/trash.png" border="0"/>\
								</div>\
								</a>\
							</div>\
							<input id="cpanel_user_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
						</div>');
	
	$("#cpanel_user_list_search").bind("keyup", function(e) {
		var manager_id = '&manager_id=' + cpValues['manager_id'];
		$('#cpanel_user_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_user_list&s=' + this.value + manager_id});
		$('#cpanel_user_list_grid').trigger("reloadGrid");	
	});
	
	$("#cpanel_user_list_grid").setGridWidth($(window).width() -60 );
	$("#cpanel_user_list_grid").setGridHeight($(window).height() - 208);
	$(window).bind('resize', function() {$("#cpanel_user_list_grid").setGridWidth($(window).width() - 60);});
	$(window).bind('resize', function() {$("#cpanel_user_list_grid").setGridHeight($(window).height() - 208);});
	
	// define object list grid
	$("#cpanel_object_list_grid").jqGrid({
		url:'func/fn_cpanel.php?cmd=load_object_list',
		datatype: "json",
		colNames:[la['NAME'],la['IMEI'],la['SIM_CARD_NUMBER'],la['ACTIVE'],la['ACTIVE_TILL'],la['LAST_CONNECTION'],la['PROTOCOL'],la['PORT'],la['STATUS'],la['USER_ACCOUNT'],'',''],
		colModel:[
			{name:'name',index:'name',width:80},
			{name:'imei',index:'imei',width:80,key:true},
			{name:'sim_number',index:'sim_number',width:80},
			{name:'active',index:'active',width:50,align:"center"},
			{name:'active_dt',index:'active_dt',width:60,align:"center"},
			{name:'dt_server',index:'dt_server',width:80,align:"center"},
			{name:'protocol',index:'protocol',width:60,align:"center"},
			{name:'port',index:'port',width:40,align:"center"},
			{name:'status',index:'status',width:80,sortable: false,align:"center"},
			{name:'used_in',index:'used_in',width:150,sortable: false},
			{name:'modify',index:'modify',width:90,align:"center",sortable: false, fixed: true},
			{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
		],
		rowNum:50,
		rowList:[25,50,100,200,300,400,500],
		pager: '#cpanel_object_list_grid_pager',
		sortname: 'imei',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '400px',
		shrinkToFit: true,
		multiselect: true
		//loadComplete: function (textStatus) {
		//	alert(textStatus);
		//	alert(JSON.stringify(textStatus));
		//}
	});
	$("#cpanel_object_list_grid").jqGrid('navGrid','#cpanel_object_list_grid_pager',{	add:true,
												edit:false,																								
												del:false,
												search:false,
												addfunc: function (e) {objectAdd('open');}	
												});
	$("#cpanel_object_list_grid").setCaption(	'<div class="row4">\
								<div class="float-left">\
									<a href="#" onclick="objectDeleteSelected();">\
									<div class="panel-button"  title="'+la['DELETE_SELECTED_OBJECTS']+'">\
										<img src="theme/images/trash.png" border="0"/>\
									</div>\
									</a>\
								</div>\
								<input id="cpanel_object_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
							</div>');
	
	$("#cpanel_object_list_search").bind("keyup", function(e) {
		var manager_id = '&manager_id=' + cpValues['manager_id'];
		$('#cpanel_object_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_object_list&s=' + this.value + manager_id});
		$('#cpanel_object_list_grid').trigger("reloadGrid");
	});
	
	$("#cpanel_object_list_grid").setGridWidth($(window).width() -60 );
	$("#cpanel_object_list_grid").setGridHeight($(window).height() - 208);
	$(window).bind('resize', function() {$("#cpanel_object_list_grid").setGridWidth($(window).width() -60 );});
	$(window).bind('resize', function() {$("#cpanel_object_list_grid").setGridHeight($(window).height() - 208);});
	
	// define unused object list grid
	if (document.getElementById('top_panel_button_unused_object_list') != undefined)
	{
		$("#cpanel_unused_object_list_grid").jqGrid({
			url:'func/fn_cpanel.php?cmd=load_unused_object_list',
			datatype: "json",
			colNames:[la['IMEI'],la['LAST_CONNECTION'],la['PROTOCOL'],la['PORT'],la['CONNECTION_ATTEMPTS'],'',''],
			colModel:[
				{name:'imei',index:'imei',width:160,key:true},
				{name:'dt_server',index:'dt_server',width:160,align:"center"},
				{name:'protocol',index:'protocol',width:100,align:"center"},
				{name:'port',index:'port',width:100,align:"center"},
				{name:'count',index:'count',width:100,align:"center"},
				{name:'modify',index:'modify',width:90,align:"center",sortable: false, fixed: true},
				{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
			],
			rowNum:50,
			rowList:[25,50,100,200,300,400,500],
			pager: '#cpanel_unused_object_list_grid_pager',
			sortname: 'imei',
			sortorder: "asc",
			viewrecords: true,
			rownumbers: true,
			height: '400px',
			shrinkToFit: true,
			multiselect: true
			//loadComplete: function (textStatus) {
			//	alert(textStatus);
			//	alert(JSON.stringify(textStatus));
			//}
		});
		$("#cpanel_unused_object_list_grid").jqGrid('navGrid','#cpanel_unused_object_list_grid_pager',{	add:false,
														edit:false,																								
														del:false,
														search:false
														});
		$("#cpanel_unused_object_list_grid").setCaption(	'<div class="row4">\
										<div class="float-left">\
											<a href="#" onclick="unusedObjectDeleteSelected();">\
											<div class="panel-button"  title="'+la['DELETE_SELECTED_OBJECTS']+'">\
												<img src="theme/images/trash.png" border="0"/>\
											</div>\
											</a>\
										</div>\
										<input id="cpanel_unused_object_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
									</div>');
		
		$("#cpanel_unused_object_list_search").bind("keyup", function(e) {
			$('#cpanel_unused_object_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_unused_object_list&s=' + this.value});
			$('#cpanel_unused_object_list_grid').trigger("reloadGrid");
		});
		
		$("#cpanel_unused_object_list_grid").setGridWidth($(window).width() -60 );
		$("#cpanel_unused_object_list_grid").setGridHeight($(window).height() - 208);
		$(window).bind('resize', function() {$("#cpanel_unused_object_list_grid").setGridWidth($(window).width() -60 );});
		$(window).bind('resize', function() {$("#cpanel_unused_object_list_grid").setGridHeight($(window).height() - 208);});
	}
	
	// define user object list grid
	$("#dialog_user_edit_object_list_grid").jqGrid({
		url:'func/fn_cpanel.php',
		datatype: "json",
		colNames:[la['NAME'],la['IMEI'],la['ACTIVE'],la['ACTIVE_TILL'],la['LAST_CONNECTION'],la['STATUS'],''],
		colModel:[
			{name:'name',index:'name',width:153},
			{name:'imei',index:'imei',width:120,key:true},
			{name:'active',index:'active',width:40,align:"center",sortable: false},
			{name:'active_dt',index:'active_dt',width:80,align:"center",sortable: false},
			{name:'dt_last',index:'dt_last',width:130,align:"center",sortable: false},
			{name:'status',index:'status',width:40,align:"center",sortable: false},
			{name:'modify',index:'modify',width:85,align:"center",sortable: false},
		],
		rowNum:25,
		rowList:[25,50,100],
		pager: '#dialog_user_edit_object_list_grid_pager',
		sortname: 'name',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '417px',
		width: '755',
		shrinkToFit: false,
		multiselect: true
		//loadComplete: function (textStatus) {
			//alert(textStatus);
			//alert(JSON.stringify(textStatus));
		//}
	});
	$("#dialog_user_edit_object_list_grid").jqGrid('navGrid','#dialog_user_edit_object_list_grid_pager',{ 	add:true,
														edit:false,
														del:false,
														search:false,
														addfunc: function (e) {userObjectAdd('open');}																		
														});
	
	// define user subaccount list grid
	$("#dialog_user_edit_subaccount_list_grid").jqGrid({
		url:'func/fn_cpanel.php',
		datatype: "json",
		colNames:['',la['ACTIVE'],la['USERNAME'],la['EMAIL'],la['PASSWORD'],'IP',''],
		colModel:[
			{name:'id',index:'id',hidden:true,key:true},
			{name:'active',index:'active',width:40,align:"center"},
			{name:'username',index:'username',width:160},
			{name:'email',index:'email',width:140},
			{name:'password',index:'password',width:140,sortable: false},
			{name:'ip',index:'ip',width:108},
			{name:'modify',index:'modify',width:65,align:"center",sortable: false},
		],
		rowNum:25,
		rowList:[25,50,100],
		pager: '#dialog_user_edit_subaccount_list_grid_pager',
		sortname: 'username',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '417px',
		width: '755',
		shrinkToFit: false,
		multiselect: true
		//loadComplete: function (textStatus) {
			//alert(textStatus);
			//alert(JSON.stringify(textStatus));
		//}
	});
	$("#dialog_user_edit_subaccount_list_grid").jqGrid('navGrid','#dialog_user_edit_subaccount_list_grid_pager',{ 	add:false,
															edit:false,
															del:false,
															search:false													
															});
	// define custom map list grid
	$("#cpanel_manage_server_custom_map_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  la['ACTIVE'],
			  la['TYPE'],
			  la['URL'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:230,fixed:true,align:"left",sortable:true},
			{name:'active',index:'active',width:80,fixed:true,align:"center",sortable:true},
			{name:'type',index:'type',width:80,fixed:true,align:"center",sortable:true},
			{name:'url',index:'url',width:510,fixed:true,align:"left",sortable:false},
			{name:'modify',index:'modify',width:63,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define template list grid
	$("#cpanel_manage_server_template_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:910,fixed:true,align:"left",sortable:true},
			{name:'modify',index:'modify',width:63,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define log list grid
	$("#cpanel_manage_server_log_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  la['MODIFIED'],
			  la['SIZE_MB'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:600,fixed:true,align:"left",sortable:true},
			{name:'modified',index:'modified',width:150,fixed:true,align:"center",sortable:true},
			{name:'size_mb',index:'size_mb',width:150,fixed:true,align:"center",sortable:true},
			{name:'modify',index:'modify',width:63,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// hide jqgrid close button
	$(".ui-jqgrid-titlebar-close").hide();
}

//#################################################
// END DIALOGS, TABS, DATATABLES
//#################################################

function load()
{
	loadLanguage();
	loadCPanelValues();
	loadServerValues();
	
	var load2 = setTimeout("load2()", 2000);
}

function load2()
{
	initGui();
	initGrids();
	initSelectList('object_device_list');
	serverTools('stats');
	serverTools('custom_maps');
	serverTools('templates');
	serverTools('logs');

	document.getElementById("loading_panel").style.display = "none";
	
	notifyCheck('session_check');
}

function loadCPanelValues()
{
	var data = {
		cmd: 'load_cpanel_values'
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		dataType: 'json',
		cache: false,
		async: false,
		success: function(result)
		{
			cpValues = result;
			cpValues['user_edit_id'] = '';
			cpValues['user_edit_privileges'] = '';
			cpValues['template_edit_name'] = '';
			cpValues['manager_id'] = 0;
			
			document.getElementById("system_language").value = cpValues['language'];
			
			initSelectList('manager_list');
		}
	});
}

function loadServerValues()
{
	var data = {
		cmd: 'load_server_values'
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		dataType: 'json',
		cache: false,
		async: false,
		success: function(result)
		{			
			// general
			document.getElementById('cpanel_manage_server_name').value = result['name'];
			document.getElementById('cpanel_manage_server_generator').value = result['generator'];
			document.getElementById('cpanel_manage_server_show_about').value = result['show_about'];
			
			var languages = document.getElementById('cpanel_manage_server_languages');
			var languages_selected = result['languages'].split(",");
			multiselectSetValues(languages, languages_selected);
			
			// url addresses
			document.getElementById('cpanel_manage_server_url_login').value = result['url_login'];
			document.getElementById('cpanel_manage_server_url_help').value = result['url_help'];
			document.getElementById('cpanel_manage_server_url_contact').value = result['url_contact'];
			document.getElementById('cpanel_manage_server_url_shop').value = result['url_shop'];
			document.getElementById('cpanel_manage_server_url_sms_gateway_app').value = result['url_sms_gateway_app'];
			
			// geocoder
			document.getElementById('cpanel_manage_server_geocoder_cache').value = result['geocoder_cache'];
			
			// objects
			document.getElementById('cpanel_manage_server_connection_timeout').value = result['connection_timeout'];
			document.getElementById('cpanel_manage_server_history_period').value = result['history_period'];
			
			// db backup email
			document.getElementById('cpanel_manage_server_backup_email').value  = result['db_backup_email'];
			
			// maps
			document.getElementById('cpanel_manage_server_map_osm').value = result['map_osm'];
			document.getElementById('cpanel_manage_server_map_bing').value = result['map_bing'];
			document.getElementById('cpanel_manage_server_map_google').value = result['map_google'];
			document.getElementById('cpanel_manage_server_map_google_traffic').value = result['map_google_traffic'];
			document.getElementById('cpanel_manage_server_map_mapbox').value = result['map_mapbox'];
			document.getElementById('cpanel_manage_server_map_yandex').value = result['map_yandex'];
			document.getElementById('cpanel_manage_server_map_bing_key').value = result['map_bing_key'];
			document.getElementById('cpanel_manage_server_map_google_key').value = result['map_google_key'];
			document.getElementById('cpanel_manage_server_map_mapbox_key').value = result['map_mapbox_key'];
			document.getElementById('cpanel_manage_server_map_layer').value = result['map_layer'];
			document.getElementById('cpanel_manage_server_map_zoom').value = result['map_zoom'];
			document.getElementById('cpanel_manage_server_map_lat').value = result['map_lat'];
			document.getElementById('cpanel_manage_server_map_lng').value = result['map_lng'];
			
			// user
			document.getElementById('cpanel_manage_server_page_after_login').value = result['page_after_login'];
			document.getElementById('cpanel_manage_server_allow_registration').value = result['allow_registration'];
			document.getElementById('cpanel_manage_server_account_expire').value = result['account_expire'];
			document.getElementById('cpanel_manage_server_account_expire_period').value = result['account_expire_period'];
			document.getElementById('cpanel_manage_server_language').value = result['language'];			
			document.getElementById('cpanel_manage_server_distance_unit').value = result['unit_of_distance'];
			document.getElementById('cpanel_manage_server_capacity_unit').value = result['unit_of_capacity'];
			document.getElementById('cpanel_manage_server_temperature_unit').value = result['unit_of_temperature'];			
			document.getElementById('cpanel_manage_server_currency').value = result['currency'];			
			document.getElementById('cpanel_manage_server_timezone').value = result['timezone'];
			
			if ((result['dst_start'].length == 11) && (result['dst_end'].length == 11))
			{
				document.getElementById('cpanel_manage_server_dst').checked = strToBoolean(result['dst']);
				
				var dst_start = result['dst_start'].split(" ");
				document.getElementById('cpanel_manage_server_dst_start_mmdd').value = dst_start[0];
				document.getElementById('cpanel_manage_server_dst_start_hhmm').value = dst_start[1];
				
				var dst_end = result['dst_end'].split(" ");
				document.getElementById('cpanel_manage_server_dst_end_mmdd').value = dst_end[0];
				document.getElementById('cpanel_manage_server_dst_end_hhmm').value = dst_end[1];	
			}
			
			document.getElementById('cpanel_manage_server_obj_add').value = result['obj_add'];
			document.getElementById('cpanel_manage_server_obj_num').value = result['obj_num'];
			document.getElementById('cpanel_manage_server_obj_dt').value = result['obj_dt'];
			document.getElementById('cpanel_manage_server_obj_trial_period').value = result['obj_trial_period'];
			document.getElementById('cpanel_manage_server_obj_edit').value = result['obj_edit'];
			document.getElementById('cpanel_manage_server_obj_history_clear').value = result['obj_history_clear'];
			
			document.getElementById('cpanel_manage_server_history').value = result['history'];
			document.getElementById('cpanel_manage_server_reports').value = result['reports'];
			document.getElementById('cpanel_manage_server_rilogbook').value = result['rilogbook'];
			document.getElementById('cpanel_manage_server_object_control').value = result['object_control'];
			document.getElementById('cpanel_manage_server_image_gallery').value = result['image_gallery'];
			document.getElementById('cpanel_manage_server_chat').value = result['chat'];
			document.getElementById('cpanel_manage_server_sms_gateway_server').value = result['sms_gateway_server'];
			
			// notify
			document.getElementById('cpanel_manage_server_notify_obj_expire').value = result['notify_obj_expire'];
			document.getElementById('cpanel_manage_server_notify_obj_expire_period').value = result['notify_obj_expire_period'];
			document.getElementById('cpanel_manage_server_notify_account_expire').value = result['notify_account_expire'];
			document.getElementById('cpanel_manage_server_notify_account_expire_period').value = result['notify_account_expire_period'];
			
			// user billing
			document.getElementById('cpanel_manage_server_payment_type').value = result['payment_type'];
			document.getElementById('cpanel_manage_server_payment_url').value = result['payment_url'];
			document.getElementById('cpanel_manage_server_payment_paypal_account').value = result['payment_paypal_account'];
			document.getElementById('cpanel_manage_server_payment_paypal_name').value = result['payment_paypal_name'];
			document.getElementById('cpanel_manage_server_payment_paypal_cur').value = result['payment_paypal_cur'];
			document.getElementById('cpanel_manage_server_payment_paypal_amount').value = result['payment_paypal_amount'];
			document.getElementById('cpanel_manage_server_payment_paypal_custom').value = result['payment_paypal_custom'];
			document.getElementById('cpanel_manage_server_payment_paypal_ipn_url').value = result['payment_paypal_ipn_url'];
			
			// e-mail settings
			result['email_signature'] = result['email_signature'].replace(/\\'/g,"'");
			
			document.getElementById('cpanel_manage_server_email').value = result['email'];
			document.getElementById('cpanel_manage_server_email_no_reply').value = result['email_no_reply'];
			document.getElementById('cpanel_manage_server_email_signature').value = result['email_signature'];
			document.getElementById('cpanel_manage_server_email_smtp').value = result['email_smtp'];
			document.getElementById('cpanel_manage_server_email_smtp_host').value = result['email_smtp_host'];
			document.getElementById('cpanel_manage_server_email_smtp_port').value = result['email_smtp_port'];
			document.getElementById('cpanel_manage_server_email_smtp_auth').value = result['email_smtp_auth'];
			document.getElementById('cpanel_manage_server_email_smtp_secure').value = result['email_smtp_secure'];
			document.getElementById('cpanel_manage_server_email_smtp_username').value = result['email_smtp_username'];
			document.getElementById('cpanel_manage_server_email_smtp_password').value = result['email_smtp_password'];
			
			//other
			document.getElementById('cpanel_manage_server_reports_schedule').value = result['reports_schedule'];
			document.getElementById('cpanel_manage_server_places_markers').value = result['places_markers'];
			document.getElementById('cpanel_manage_server_places_routes').value = result['places_routes'];
			document.getElementById('cpanel_manage_server_places_zones').value = result['places_zones'];
			
			// sms settings
			document.getElementById('cpanel_manage_server_sms_gateway').value = result['sms_gateway'];
			if (result['sms_gateway_type'] == '')
			{
                                result['sms_gateway_type'] = 'app';
                        }
			document.getElementById('cpanel_manage_server_sms_gateway_type').value = result['sms_gateway_type'];
			document.getElementById('cpanel_manage_server_sms_gateway_number_filter').value = result['sms_gateway_number_filter'];
			document.getElementById('cpanel_manage_server_sms_gateway_url').value = result['sms_gateway_url'];
			if (result['sms_gateway_identifier'].length != 20)
			{
				result['sms_gateway_identifier'] = SMSGatewayGenerateIdentifier();
			}
			document.getElementById('cpanel_manage_server_sms_gateway_identifier').value = result['sms_gateway_identifier'];
			
			// tools
			document.getElementById('cpanel_manage_server_tools_server_cleanup_users_ae').value = result['server_cleanup_users_ae'];
			document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_activated_ae').value = result['server_cleanup_objects_not_activated_ae'];
			document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_used_ae').value = result['server_cleanup_objects_not_used_ae'];
			document.getElementById('cpanel_manage_server_tools_server_cleanup_db_junk_ae').value = result['server_cleanup_db_junk_ae'];
			document.getElementById('cpanel_manage_server_tools_server_cleanup_users_days').value = result['server_cleanup_users_days'];
			document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_activated_days').value = result['server_cleanup_objects_not_activated_days'];
			
			serverValuesCheck();
		}
	});
}

function saveServerValues()
{
	var languages = document.getElementById('cpanel_manage_server_languages');
	languages = multiselectGetValues(languages);
		
	var account_expire = document.getElementById('cpanel_manage_server_account_expire').value;
	var account_expire_period = document.getElementById('cpanel_manage_server_account_expire_period').value;
	
	var obj_num = document.getElementById('cpanel_manage_server_obj_num').value;
	
	if ((obj_num < 1) || !isIntValid(obj_num))
	{
		obj_num = 0;
	}
	
	var obj_dt = document.getElementById('cpanel_manage_server_obj_dt').value;
	
	if ((obj_dt < 1) || !isIntValid(obj_dt))
	{
		obj_dt = 0;
	}
	
	var obj_trial_period = document.getElementById('cpanel_manage_server_obj_trial_period').value;
	
	if ((obj_trial_period < 1) || !isIntValid(obj_trial_period))
	{
		obj_trial_period = 0;
	}
	
	var dst = document.getElementById('cpanel_manage_server_dst').checked;
	var dst_start = document.getElementById('cpanel_manage_server_dst_start_mmdd').value + ' ' + document.getElementById('cpanel_manage_server_dst_start_hhmm').value;
	var dst_end = document.getElementById('cpanel_manage_server_dst_end_mmdd').value + ' ' + document.getElementById('cpanel_manage_server_dst_end_hhmm').value;
	
	if ((dst == true) && (dst_start.length == 11) && (dst_end.length == 11))
	{
		dst_start_compare = moment().year() + '-' + dst_start + ':00';
		dst_end_compare = moment().year() + '-' + dst_end + ':00';
		
		if (moment(dst_start_compare) >= moment(dst_end_compare))
		{
			alert(la['INVALID_DST_INTERVAL']);
			return;
		}
	}
	else
	{
		dst = false;
		dst_start = '';
		dst_end = '';
	}

	var history_period = document.getElementById('cpanel_manage_server_history_period').value;
	
	if ((history_period < 30) || !isIntValid(history_period))
	{
		alert(la['LOWEST_HISTORY_PERIOD_IS_30_DAYS']);
		return;
	}
	
	var db_backup_email = document.getElementById('cpanel_manage_server_backup_email').value;
	if((db_backup_email != '') && (!isEmailValid(db_backup_email)))
	{
		alert(la['THIS_EMAIL_IS_NOT_VALID']);
		return;
	}
	
	var notify_obj_expire = document.getElementById('cpanel_manage_server_notify_obj_expire').value;
	var notify_obj_expire_period = document.getElementById('cpanel_manage_server_notify_obj_expire_period').value;
	var notify_account_expire = document.getElementById('cpanel_manage_server_notify_account_expire').value;
	var notify_account_expire_period = document.getElementById('cpanel_manage_server_notify_account_expire_period').value;
	
	if ((account_expire_period < 1) || !isIntValid(account_expire_period))
	{
		account_expire_period = 1;
	}
	
	if ((notify_obj_expire_period < 1) || !isIntValid(notify_obj_expire_period))
	{
		notify_obj_expire_period = 1;
	}
	
	if ((notify_account_expire_period < 1) || !isIntValid(notify_account_expire_period))
	{
		notify_account_expire_period = 1;
	}
	
	var places_markers = document.getElementById('cpanel_manage_server_places_markers').value;
	if ((places_markers < 1) || !isIntValid(places_markers))
	{
		places_markers = 0;
	}
	
	var places_routes = document.getElementById('cpanel_manage_server_places_routes').value;
	if ((places_routes < 1) || !isIntValid(places_routes))
	{
		places_routes = 0;
	}
	
	var places_zones = document.getElementById('cpanel_manage_server_places_zones').value;
	if ((places_zones < 1) || !isIntValid(places_zones))
	{
		places_zones = 0;
	}
	
	var map_osm = document.getElementById('cpanel_manage_server_map_osm').value;
	var map_bing = document.getElementById('cpanel_manage_server_map_bing').value;
	var map_google = document.getElementById('cpanel_manage_server_map_google').value;
	var map_mapbox = document.getElementById('cpanel_manage_server_map_mapbox').value;
	var map_yandex = document.getElementById('cpanel_manage_server_map_yandex').value;
	
	if ((map_osm == 'false') && (map_bing == 'false') && (map_google == 'false') && (map_mapbox== 'false') && (map_yandex == 'false'))
	{
		alert(la['AT_LEAST_ONE_MAP_SHOULD_BE_ENABLED']);
		return;
	}
	
	var map_lat = document.getElementById('cpanel_manage_server_map_lat').value;
	if (isNumber(map_lat))
	{
		if ((map_lat < -90) || (map_lat > 90))
		{
			alert(la['LATITUDE_IS_OUT_OF_RANGE']);
			return;
		}
	}
	else
	{
		map_lat = 0;
	}
	
	var map_lng = document.getElementById('cpanel_manage_server_map_lng').value;
	if (isNumber(map_lng))
	{
		if ((map_lng < -180) || (map_lng > 180))
		{
			alert(la['LONGITUDE_IS_OUT_OF_RANGE']);
			return;
		}
	}
	else
	{
		map_lng = 0;
	}
	
	var server_cleanup_users_ae = document.getElementById('cpanel_manage_server_tools_server_cleanup_users_ae').value;
	var server_cleanup_objects_not_activated_ae = document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_activated_ae').value;
	var server_cleanup_objects_not_used_ae = document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_used_ae').value;
	var server_cleanup_db_junk_ae =	document.getElementById('cpanel_manage_server_tools_server_cleanup_db_junk_ae').value;
	
	var server_cleanup_users_days =	document.getElementById('cpanel_manage_server_tools_server_cleanup_users_days').value;
	if ((server_cleanup_users_days < 1) || !isIntValid(server_cleanup_users_days))
	{
		server_cleanup_users_days = 0;
	}
	
	var server_cleanup_objects_not_activated_days = document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_activated_days').value;
	if ((server_cleanup_objects_not_activated_days < 1) || !isIntValid(server_cleanup_objects_not_activated_days))
	{
		server_cleanup_objects_not_activated_days = 0;
	}
	
	var data = {
		cmd: 'save_server_values',
		name: document.getElementById('cpanel_manage_server_name').value,
		generator: document.getElementById('cpanel_manage_server_generator').value,
		show_about: document.getElementById('cpanel_manage_server_show_about').value,
		languages: languages,
		url_login: document.getElementById('cpanel_manage_server_url_login').value,
		url_help: document.getElementById('cpanel_manage_server_url_help').value,
		url_contact: document.getElementById('cpanel_manage_server_url_contact').value,
		url_shop: document.getElementById('cpanel_manage_server_url_shop').value,
		url_sms_gateway_app: document.getElementById('cpanel_manage_server_url_sms_gateway_app').value,
		geocoder_cache: document.getElementById('cpanel_manage_server_geocoder_cache').value,
		connection_timeout: document.getElementById('cpanel_manage_server_connection_timeout').value,
		history_period: history_period,
		db_backup_email: db_backup_email,
		map_osm: map_osm,
		map_bing: map_bing,
		map_google: map_google,
		map_google_traffic: document.getElementById('cpanel_manage_server_map_google_traffic').value,
		map_mapbox: map_mapbox,
		map_yandex: map_yandex,
		map_bing_key: document.getElementById('cpanel_manage_server_map_bing_key').value,
		map_google_key: document.getElementById('cpanel_manage_server_map_google_key').value,
		map_mapbox_key: document.getElementById('cpanel_manage_server_map_mapbox_key').value,
		map_layer: document.getElementById('cpanel_manage_server_map_layer').value,
		map_zoom: document.getElementById('cpanel_manage_server_map_zoom').value,
		map_lat: map_lat,
		map_lng: map_lng,
		page_after_login: document.getElementById('cpanel_manage_server_page_after_login').value,
		allow_registration: document.getElementById('cpanel_manage_server_allow_registration').value,
		account_expire: account_expire,
		account_expire_period: account_expire_period,
		language: document.getElementById('cpanel_manage_server_language').value,
		unit_of_distance: document.getElementById('cpanel_manage_server_distance_unit').value,
		unit_of_capacity: document.getElementById('cpanel_manage_server_capacity_unit').value,
		unit_of_temperature: document.getElementById('cpanel_manage_server_temperature_unit').value,
		currency: document.getElementById('cpanel_manage_server_currency').value,
		timezone: document.getElementById('cpanel_manage_server_timezone').value,
		dst: dst,
		dst_start: dst_start,
		dst_end: dst_end,
		obj_add: document.getElementById('cpanel_manage_server_obj_add').value,
		obj_num: obj_num,
		obj_dt: obj_dt,
		obj_trial_period: obj_trial_period,
		obj_edit: document.getElementById('cpanel_manage_server_obj_edit').value,
		obj_history_clear: document.getElementById('cpanel_manage_server_obj_history_clear').value,
		history: document.getElementById('cpanel_manage_server_history').value,
		reports: document.getElementById('cpanel_manage_server_reports').value,
		rilogbook: document.getElementById('cpanel_manage_server_rilogbook').value,
		object_control: document.getElementById('cpanel_manage_server_object_control').value,
		image_gallery: document.getElementById('cpanel_manage_server_image_gallery').value,
		chat: document.getElementById('cpanel_manage_server_chat').value,
		sms_gateway_server: document.getElementById('cpanel_manage_server_sms_gateway_server').value,
		notify_obj_expire: notify_obj_expire,
		notify_obj_expire_period: notify_obj_expire_period,
		notify_account_expire: notify_account_expire,
		notify_account_expire_period: notify_account_expire_period,
		payment_type: document.getElementById('cpanel_manage_server_payment_type').value,
		payment_url: document.getElementById('cpanel_manage_server_payment_url').value,
		payment_paypal_account: document.getElementById('cpanel_manage_server_payment_paypal_account').value,
		payment_paypal_name: document.getElementById('cpanel_manage_server_payment_paypal_name').value,
		payment_paypal_cur: document.getElementById('cpanel_manage_server_payment_paypal_cur').value,
		payment_paypal_amount: document.getElementById('cpanel_manage_server_payment_paypal_amount').value,
		payment_paypal_custom: document.getElementById('cpanel_manage_server_payment_paypal_custom').value,
		email: document.getElementById('cpanel_manage_server_email').value,
		email_no_reply: document.getElementById('cpanel_manage_server_email_no_reply').value,
		email_signature: document.getElementById('cpanel_manage_server_email_signature').value,
		email_smtp: document.getElementById('cpanel_manage_server_email_smtp').value,
		email_smtp_host: document.getElementById('cpanel_manage_server_email_smtp_host').value,
		email_smtp_port: document.getElementById('cpanel_manage_server_email_smtp_port').value,
		email_smtp_auth: document.getElementById('cpanel_manage_server_email_smtp_auth').value,
		email_smtp_secure: document.getElementById('cpanel_manage_server_email_smtp_secure').value,
		email_smtp_username: document.getElementById('cpanel_manage_server_email_smtp_username').value,
		email_smtp_password: document.getElementById('cpanel_manage_server_email_smtp_password').value,
		sms_gateway: document.getElementById('cpanel_manage_server_sms_gateway').value,
		sms_gateway_type: document.getElementById('cpanel_manage_server_sms_gateway_type').value,
		sms_gateway_number_filter: document.getElementById('cpanel_manage_server_sms_gateway_number_filter').value,
		sms_gateway_url: document.getElementById('cpanel_manage_server_sms_gateway_url').value,
		sms_gateway_identifier: document.getElementById('cpanel_manage_server_sms_gateway_identifier').value,
		server_cleanup_users_ae: server_cleanup_users_ae,
		server_cleanup_objects_not_activated_ae: server_cleanup_objects_not_activated_ae,
		server_cleanup_objects_not_used_ae: server_cleanup_objects_not_used_ae,
		server_cleanup_db_junk_ae: server_cleanup_db_junk_ae,
		server_cleanup_users_days: server_cleanup_users_days,
		server_cleanup_objects_not_activated_days: server_cleanup_objects_not_activated_days,
		reports_schedule: document.getElementById('cpanel_manage_server_reports_schedule').value,
		places_markers: places_markers,
		places_routes: places_routes,
		places_zones: places_zones
	};

	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				alert(la['CHANGES_SAVED_SUCCESSFULLY']);
				loadServerValues();
			}
			else
			{
				alert(result);
			}
		}
	});
}

function serverValuesCheck()
{
	var dst = document.getElementById('cpanel_manage_server_dst').checked;
	
	if (dst)
	{
                document.getElementById('cpanel_manage_server_dst_start_mmdd').disabled = false;
		document.getElementById('cpanel_manage_server_dst_start_hhmm').disabled = false;
		document.getElementById('cpanel_manage_server_dst_end_mmdd').disabled = false;
		document.getElementById('cpanel_manage_server_dst_end_hhmm').disabled = false;
        }
	else
	{
		document.getElementById('cpanel_manage_server_dst_start_mmdd').disabled = true;
		document.getElementById('cpanel_manage_server_dst_start_hhmm').disabled = true;
		document.getElementById('cpanel_manage_server_dst_end_mmdd').disabled = true;
		document.getElementById('cpanel_manage_server_dst_end_hhmm').disabled = true;
	}
	
	if (document.getElementById('cpanel_manage_server_allow_registration').value == 'true')
	{
		document.getElementById('cpanel_manage_server_obj_add').disabled = false;
		document.getElementById('cpanel_manage_server_obj_num').disabled = false;
		document.getElementById('cpanel_manage_server_obj_dt').disabled = false;
		document.getElementById('cpanel_manage_server_obj_trial_period').disabled = false;
		
		switch (document.getElementById('cpanel_manage_server_obj_add').value)
		{
			case "false":
				document.getElementById('cpanel_manage_server_obj_num').disabled = true;
				document.getElementById('cpanel_manage_server_obj_dt').disabled = true;
				document.getElementById('cpanel_manage_server_obj_trial_period').disabled = true;
				break;
			case "trial":
				document.getElementById('cpanel_manage_server_obj_num').disabled = true;
				document.getElementById('cpanel_manage_server_obj_dt').disabled = true;
				document.getElementById('cpanel_manage_server_obj_trial_period').disabled = false;
				break;
			case "limited":
				document.getElementById('cpanel_manage_server_obj_num').disabled = false;
				document.getElementById('cpanel_manage_server_obj_dt').disabled = false;
				document.getElementById('cpanel_manage_server_obj_trial_period').disabled = true;
				break;
			case "unlimited":
				document.getElementById('cpanel_manage_server_obj_num').disabled = true;
				document.getElementById('cpanel_manage_server_obj_dt').disabled = false;
				document.getElementById('cpanel_manage_server_obj_trial_period').disabled = true;
				break;
		}
		
		document.getElementById('cpanel_manage_server_account_expire').disabled = false;
		document.getElementById('cpanel_manage_server_account_expire_period').disabled = false;
	}
	else
	{
		document.getElementById('cpanel_manage_server_obj_add').disabled = true;
		document.getElementById('cpanel_manage_server_obj_num').disabled = true;
		document.getElementById('cpanel_manage_server_obj_dt').disabled = true;
		document.getElementById('cpanel_manage_server_obj_trial_period').disabled = true;
		
		document.getElementById('cpanel_manage_server_account_expire').disabled = true;
		document.getElementById('cpanel_manage_server_account_expire_period').disabled = true;
	}
	
	if (document.getElementById('cpanel_manage_server_account_expire').value == 'true')
	{
                document.getElementById('cpanel_manage_server_account_expire_period').disabled = false;
        }
	else
	{
		document.getElementById('cpanel_manage_server_account_expire_period').disabled = true;
	}
	
	if (document.getElementById('cpanel_manage_server_notify_obj_expire').value == 'true')
	{
                document.getElementById('cpanel_manage_server_notify_obj_expire_period').disabled = false;
        }
	else
	{
		document.getElementById('cpanel_manage_server_notify_obj_expire_period').disabled = true;
	}
	
	if (document.getElementById('cpanel_manage_server_notify_account_expire').value == 'true')
	{
                document.getElementById('cpanel_manage_server_notify_account_expire_period').disabled = false;
        }
	else
	{
		document.getElementById('cpanel_manage_server_notify_account_expire_period').disabled = true;
	}
	
	if (document.getElementById('cpanel_manage_server_payment_type').value == '')
	{
		document.getElementById('cpanel_manage_server_payment_url').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_account').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_name').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_cur').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_amount').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_custom').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_ipn_url').disabled = true;
	}
	else if (document.getElementById('cpanel_manage_server_payment_type').value == 'url')
	{
		document.getElementById('cpanel_manage_server_payment_url').disabled = false;
		document.getElementById('cpanel_manage_server_payment_paypal_account').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_name').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_cur').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_amount').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_custom').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_ipn_url').disabled = true;
	}
	else if (document.getElementById('cpanel_manage_server_payment_type').value == 'paypal')
	{
		document.getElementById('cpanel_manage_server_payment_url').disabled = true;
		document.getElementById('cpanel_manage_server_payment_paypal_account').disabled = false;
		document.getElementById('cpanel_manage_server_payment_paypal_name').disabled = false;
		document.getElementById('cpanel_manage_server_payment_paypal_cur').disabled = false;
		document.getElementById('cpanel_manage_server_payment_paypal_amount').disabled = false;
		document.getElementById('cpanel_manage_server_payment_paypal_custom').disabled = false;
		document.getElementById('cpanel_manage_server_payment_paypal_ipn_url').disabled = false;
	}

	if (document.getElementById('cpanel_manage_server_email_smtp').value == 'true')
	{
		document.getElementById('cpanel_manage_server_email_smtp_host').disabled = false;
		document.getElementById('cpanel_manage_server_email_smtp_port').disabled = false;
		document.getElementById('cpanel_manage_server_email_smtp_auth').disabled = false;
		document.getElementById('cpanel_manage_server_email_smtp_secure').disabled = false;
		document.getElementById('cpanel_manage_server_email_smtp_username').disabled = false;
		document.getElementById('cpanel_manage_server_email_smtp_password').disabled = false;

	}
	else
	{
		document.getElementById('cpanel_manage_server_email_smtp_host').disabled = true;
		document.getElementById('cpanel_manage_server_email_smtp_port').disabled = true;
		document.getElementById('cpanel_manage_server_email_smtp_auth').disabled = true;
		document.getElementById('cpanel_manage_server_email_smtp_secure').disabled = true;
		document.getElementById('cpanel_manage_server_email_smtp_username').disabled = true;
		document.getElementById('cpanel_manage_server_email_smtp_password').disabled = true;
	}
	
	if (document.getElementById('cpanel_manage_server_sms_gateway_type').value == 'app')
	{
		document.getElementById('cpanel_manage_server_sms_app').style.display = '';
		document.getElementById('cpanel_manage_server_sms_http').style.display = 'none';
	}
	else
	{
		document.getElementById('cpanel_manage_server_sms_app').style.display = 'none';
		document.getElementById('cpanel_manage_server_sms_http').style.display = '';
	}
}

function uploadLogo()
{
	// a bit dirty sollution, maybe will make better in the feature :)
	document.getElementById('load_file').addEventListener('change', uploadLogoFile, false);
	document.getElementById('load_file').click();
}

function uploadLogoFile(evt)
{
	var files = evt.target.files;
	var reader = new FileReader();
	reader.onloadend = function(event)
	{
		var result = event.target.result;
		
		if (!files[0].type.match('image/png'))
		{
			alert(la['FILE_TYPE_MUST_BE_PNG']);
			return;
		}
		
		var image = new Image();
		image.src = result;
		
		image.onload = function () {
			if ((image.width != 300) || (image.height != 75))
			{
				alert(la['IMAGE_WIGTH_OR_HEIGHT_DOES_NOT_MEET_REQUIREMENTS']);
				return;
			}
			
			$.ajax({
				url: "func/fn_upload.php?file=logo",
				type: "POST",
				data: result,
				processData: false,
				contentType: false,
				success: function (res) {
					alert(la['IMAGE_UPLOADED_SUCCESSFULLY']);
					var logo = document.getElementById('cpanel_manage_server_logo');
					logo.src = logo.src + "?t=" + new Date().getTime();
				}
			});
		}
		
		document.getElementById('load_file').value = '';
	}
	reader.readAsDataURL(files[0]);
	
	this.removeEventListener('change', uploadLogoFile, false);
}

function initSelectList(list)
{
	switch (list)
	{
		case "object_device_list":
			var select_add = document.getElementById('dialog_object_add_device');
			var select_edit = document.getElementById('dialog_object_edit_device');
			select_add.options.length = 0; // clear out existing items
			select_edit.options.length = 0; // clear out existing items	
			for (var key in gsValues['device_list'])
			{
				var obj = gsValues['device_list'][key];
				select_add.options.add(new Option(obj.name, obj.name));
				select_edit.options.add(new Option(obj.name, obj.name));
			}
						
			break;
		case "privileges_list_super_admin":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
			select.options.add(new Option(la['MANAGER'], 'manager'));
			select.options.add(new Option(la['ADMINISTRATOR'], 'admin'));
			select.options.add(new Option(la['SUPER_ADMINISTRATOR'], 'super_admin'));
		break;
		case "privileges_list_admin":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
			select.options.add(new Option(la['MANAGER'], 'manager'));
			select.options.add(new Option(la['ADMINISTRATOR'], 'admin'));
		break;
		case "privileges_list_manager":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
			select.options.add(new Option(la['MANAGER'], 'manager'));
		break;
		case "privileges_list_user":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
		break;
		case "manager_list":
			if ((cpValues['privileges'] == 'super_admin') || (cpValues['privileges'] == 'admin'))
			{		
				var data = {
					cmd: 'load_manager_list'
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.php",
					data: data,
					dataType: 'json',
					cache: false,
					success: function(result)
					{
						var select = document.getElementById('cpanel_manager_list');
						if (select)
						{
							select.options.length = 0; // clear out existing items
							select.options.add(new Option(la['ADMINISTRATOR'], 0));
							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
						}
						
						document.getElementById('cpanel_manager_list').value = cpValues['manager_id'];
						
						var select = document.getElementById('dialog_user_edit_account_manager_id');
						if (select)
						{
							select.options.length = 0; // clear out existing items	
							select.options.add(new Option(la['NO_MANAGER'], 0));
							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
						}
						
						var select = document.getElementById('dialog_object_add_manager_id');
						if (select)
						{
							select.options.length = 0; // clear out existing items	
							select.options.add(new Option(la['NO_MANAGER'], 0));
							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
						}
						
						var select = document.getElementById('dialog_object_edit_manager_id');
						if (select)
						{
							select.options.length = 0; // clear out existing items	
							select.options.add(new Option(la['NO_MANAGER'], 0));
							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
						}
					}
				});
			}
		break;
	}
}

function switchCPTab(name){
	document.getElementById("top_panel_button_user_list").className = "user-list-btn";
	document.getElementById("top_panel_button_object_list").className = "object-list-btn";
	
	if (document.getElementById("top_panel_button_unused_object_list") != undefined)
	{
		document.getElementById("top_panel_button_unused_object_list").className = "unused-object-list-btn";
	}
	
	if (document.getElementById("top_panel_button_manage_server") != undefined)
	{
		document.getElementById("top_panel_button_manage_server").className = "manage-server-btn";
	}
	
	switch (name)
	{
		case "user_list":
			document.getElementById("top_panel_button_user_list").className = "user-list-btn active";

			document.getElementById('cpanel_user_list').style.display = '';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			break;
		case "object_list":
			document.getElementById("top_panel_button_object_list").className = "object-list-btn active";
		
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = '';

			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			break;
		case "unused_object_list":
			document.getElementById("top_panel_button_unused_object_list").className = "unused-object-list-btn active";
	
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = '';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			break;
		case "manage_server":
			document.getElementById("top_panel_button_manage_server").className = "manage-server-btn active";
						
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = '';
			}
			
			break;
	}
}

function switchCPManager(manager_id)
{
	cpValues['manager_id'] = manager_id;
	
	$('#dialog_user_object_add_objects').tokenize().options.datas = "func/fn_cpanel.php?cmd=load_object_search_list&manager_id=" + cpValues['manager_id'];
	$('#dialog_object_add_users').tokenize().options.datas = "func/fn_cpanel.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'];
	$('#dialog_object_edit_users').tokenize().options.datas = "func/fn_cpanel.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'];
	
	$('#cpanel_user_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_user_list&manager_id=' + cpValues['manager_id']});
	$('#cpanel_user_list_grid').trigger("reloadGrid");	
	
	$('#cpanel_object_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_object_list&manager_id=' + cpValues['manager_id']});
	$('#cpanel_object_list_grid').trigger("reloadGrid");
	
	serverTools('stats');
}

function logDeleteAll()
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_ALL_LOGS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_all_logs'
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.tools.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('logs');
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function logDelete(file)
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
	if(answer)
	{
		var data = {
			cmd: 'delete_log',
			file: file
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.tools.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('logs');
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function logOpen(file)
{
	window.open('logs/'+file,'_blank');
}

function serverTools(cmd)
{
	switch (cmd)
	{
		case "custom_maps":
			var data = {
				cmd: 'load_custom_map_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_custom_map_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var map_id = result[i].map_id;
						var name = result[i].name;
						var active = result[i].active;
						var type = result[i].type;
						var url = result[i].url;
						
						if (active == 'true')
						{
							active= '<img src="theme/images/green_bullet.png" />';
						}
						else
						{
							active= '<img src="theme/images/red_cross.png" />';
						}
						
						var modify = '<a href="#" onclick="customMapProperties(\''+map_id+'\');" title="'+la['EDIT']+'"><img src="theme/images/pen_edit.png" /></a>';
						modify += '<a href="#" onclick="customMapDelete(\''+map_id+'\');" title="'+la['DELETE']+'"><img src="theme/images/trash.png" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, active: active, type: type, url: url, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			break;
		
		case "templates":
			var data = {
				cmd: 'load_template_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_template_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var name = result[i].name;
						
						var name_ = la['TEMPLATE_' + name.toUpperCase()];
						
						var modify = '<a href="#" onclick="templateProperties(\''+name+'\');" title="'+la['EDIT']+'"><img src="theme/images/pen_edit.png" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name_, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			
			break;
		case "logs":
			var data = {
				cmd: 'load_log_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_log_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var name = result[i].name;
						var modified = result[i].modified;
						var size = result[i].size;
						var modify = '<a href="#" onclick="logOpen(\''+name+'\');" title="'+la['OPEN']+'"><img src="theme/images/file.png" /></a>';
						modify += '<a href="#" onclick="logDelete(\''+name+'\');" title="'+la['DELETE']+'"><img src="theme/images/trash.png" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, modified: modified, size_mb: size, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			
			break;
		case "stats":
			clearTimeout(timer_loadStats);
			
			var data = {
				cmd: 'stats',
				manager_id: cpValues['manager_id']
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				dataType: 'json',
				cache: false,
				error: function(statusCode, errorThrown) {
					// shedule next stats reload
					timer_loadStats = setTimeout("serverTools('stats');", 30000);
				},
				success: function(result)
				{
					document.getElementById('user_list_stats').innerHTML = '('+result['total_users']+')';
					document.getElementById('object_list_stats').innerHTML = '('+result['total_objects'] + '/' + result['total_objects_online']+')';
					
					if (document.getElementById('unused_object_list_stats') != undefined)
					{
						document.getElementById('unused_object_list_stats').innerHTML = '('+result['total_unused_objects']+')'; 
                                        }
					
					document.getElementById('cpanel_manage_server_sms_gateway_total_in_queue').innerHTML = result['sms_gateway_total_in_queue'];
					
					// shedule next stats reload
					timer_loadStats = setTimeout("serverTools('stats');", 30000);
				}
			});
			break;
		case "server_cleanup_users":
			var server_cleanup_users_days =	document.getElementById('cpanel_manage_server_tools_server_cleanup_users_days').value;
			if ((server_cleanup_users_days < 1) || !isIntValid(server_cleanup_users_days))
			{
				server_cleanup_users_days = 0;
			}
	
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
			if(answer)
			{
				var data = {
					cmd: 'server_cleanup_users',
					days: server_cleanup_users_days
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.tools.php",
					data: data,
					success: function(result)
					{
						if (result != '')
						{
							alert(la['TOTAL_ITEMS_DELETED'] + ' ' + result);
						}						
					}
				});
			}
			break;
		case "server_cleanup_objects_not_activated":
			var server_cleanup_objects_not_activated_days = document.getElementById('cpanel_manage_server_tools_server_cleanup_objects_not_activated_days').value;
			if ((server_cleanup_objects_not_activated_days < 1) || !isIntValid(server_cleanup_objects_not_activated_days))
			{
				server_cleanup_objects_not_activated_days = 0;
			}
	
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
			if(answer)
			{
				var data = {
					cmd: 'server_cleanup_objects_not_activated',
					days: server_cleanup_objects_not_activated_days
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.tools.php",
					data: data,
					success: function(result)
					{
						if (result != '')
						{
							alert(la['TOTAL_ITEMS_DELETED'] + ' ' + result);
						}
					}
				});
			}
			break;
		case "server_cleanup_objects_not_used":
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
			if(answer)
			{
				var data = {
					cmd: 'server_cleanup_objects_not_used'
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.tools.php",
					data: data,
					success: function(result)
					{
						if (result != '')
						{
							alert(la['TOTAL_ITEMS_DELETED'] + ' ' + result);
						}
					}
				});
			}
			break;
		case "server_cleanup_db_junk":
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
			if(answer)
			{
				var data = {
					cmd: 'server_cleanup_db_junk'
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.tools.php",
					data: data,
					success: function(result)
					{
						if (result != '')
						{
							alert(la['TOTAL_ITEMS_DELETED'] + ' ' + result);
						}
					}
				});
			}
			break;
	}
}

function objectAdd(cmd)
{
	switch (cmd)
	{
		case "open":
			// set object add properties availability
			if (cpValues['privileges'] == 'manager')
			{
				document.getElementById('dialog_object_add_manager_id').disabled = true;
			}
					
			document.getElementById('dialog_object_add_name').value = '';
			document.getElementById('dialog_object_add_imei').value = '';
			document.getElementById('dialog_object_add_model').value = '';
			document.getElementById('dialog_object_add_vin').value = '';
			document.getElementById('dialog_object_add_plate_number').value = '';
			document.getElementById('dialog_object_add_device').value = '';
			document.getElementById('dialog_object_add_sim_number').value = '';
			document.getElementById('dialog_object_add_manager_id').value = 0;
			document.getElementById('dialog_object_add_active').checked = true;
			document.getElementById('dialog_object_add_active_dt').value = moment().add('years', 1).format("YYYY-MM-DD");
			$('#dialog_object_add_users').tokenize().clear();
			$("#dialog_object_add").dialog("open");
			break;
		case "add":
			var name = document.getElementById('dialog_object_add_name').value;
			var imei = document.getElementById('dialog_object_add_imei').value;
			var model = document.getElementById('dialog_object_add_model').value;
			var vin = document.getElementById('dialog_object_add_vin').value;
			var plate_number = document.getElementById('dialog_object_add_plate_number').value;
			var device = document.getElementById('dialog_object_add_device').value;
			var sim_number = document.getElementById('dialog_object_add_sim_number').value;
			var manager_id = document.getElementById('dialog_object_add_manager_id').value;
			var active = document.getElementById('dialog_object_add_active').checked;
			var active_dt = document.getElementById('dialog_object_add_active_dt').value;
			
			var user_ids = $('#dialog_object_add_users').tokenize().toArray();
			
			user_ids = JSON.stringify(user_ids);
			
			if (name == "")
			{
				alert(la['NAME_CANT_BE_EMPTY']);
				return;
			}
			
			if(!isIMEIValid(imei))
			{
				alert(la['THIS_IMEI_IS_NOT_VALID']);
				return;
			}
			
			var data = {
				cmd: 'add_object',
				name: name,
				imei: imei,
				model: model,
				vin: vin,
				plate_number: plate_number,
				device: device,
				sim_number: sim_number,
				manager_id: manager_id,
				active: active,
				active_dt: active_dt,
				user_ids: user_ids
			};
			
		   $.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initSelectList('manager_list');
						serverTools('stats');
						
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_object_list_grid').trigger("reloadGrid");
						$('#cpanel_unused_object_list_grid').trigger("reloadGrid");
						$("#dialog_object_add").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_object_add").dialog("close");
			break;
	}
}

function objectEdit(cmd)
{
	switch (cmd)
	{
		default:			
			var data = {
				cmd: 'load_object_values',
				imei: cmd
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{
					// set object edit properties availability
					if (cpValues['privileges'] == 'manager')
					{
						document.getElementById('dialog_object_edit_manager_id').disabled = true;
					}
					
					// set loaded properties
					document.getElementById('dialog_object_edit_name').value = result['name'];
					document.getElementById('dialog_object_edit_imei').value = result['imei'];
					document.getElementById('dialog_object_edit_model').value = result['model'];
					document.getElementById('dialog_object_edit_vin').value = result['vin'];
					document.getElementById('dialog_object_edit_plate_number').value = result['plate_number'];
					document.getElementById('dialog_object_edit_device').value = result['device'];
					document.getElementById('dialog_object_edit_sim_number').value = result['sim_number'];
					document.getElementById('dialog_object_edit_manager_id').value = result['manager_id'];
					document.getElementById('dialog_object_edit_active').checked = strToBoolean(result['active']);
					document.getElementById('dialog_object_edit_active_dt').value = result['active_dt'];
					
					$('#dialog_object_edit_users').tokenize().clear();
					
					$('#dialog_object_edit_users').tokenize().options.newElements = true;
					var users = result['users'];
					for(var i=0;i<users.length;i++)
					{
						var value = users[i].value;
						var text = users[i].text;
						$('#dialog_object_edit_users').tokenize().tokenAdd(value, text);
					}
					$('#dialog_object_edit_users').tokenize().options.newElements = false;
				}
			});
			
			$("#dialog_object_edit").dialog("open");
			break;
		case "save":
			var name = document.getElementById('dialog_object_edit_name').value;
			var imei = document.getElementById('dialog_object_edit_imei').value;
			var model = document.getElementById('dialog_object_edit_model').value;
			var vin = document.getElementById('dialog_object_edit_vin').value;
			var plate_number = document.getElementById('dialog_object_edit_plate_number').value;
			var device = document.getElementById('dialog_object_edit_device').value;
			var sim_number = document.getElementById('dialog_object_edit_sim_number').value;
			var manager_id = document.getElementById('dialog_object_edit_manager_id').value;
			var active = document.getElementById('dialog_object_edit_active').checked;
			var active_dt = document.getElementById('dialog_object_edit_active_dt').value;
			
			var user_ids = $('#dialog_object_edit_users').tokenize().toArray();
			
			user_ids = JSON.stringify(user_ids);
			
			if (name == "")
			{
				alert(la['NAME_CANT_BE_EMPTY']);
				return;
			}
			
			var data = {
				cmd: 'edit_object',
				name: name,
				imei: imei,
				model: model,
				vin: vin,
				plate_number: plate_number,
				device: device,
				sim_number: sim_number,
				manager_id: manager_id,
				active: active,
				active_dt: active_dt,
				user_ids: user_ids
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					if(result == 'OK')
					{
						initSelectList('manager_list');
						
						$("#dialog_object_edit").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_object_edit").dialog("close");
			break;
	}
}

function objectClearHistory(imei)
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_CLEAR_HISTORY_EVENTS']);
	if(answer)
	{
		var data = {
			cmd: 'clear_object_history',
			imei: imei
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					$('#cpanel_object_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);	
				}
			}
		});
	}
}

function objectDelete(imei){
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_OBJECT_FROM_SYSTEM']);
	if(answer)
	{
		var data = {
			cmd: 'delete_object',
			imei: imei
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					initSelectList('manager_list');
					
					$('#cpanel_user_list_grid').trigger("reloadGrid");
					$('#cpanel_object_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);	
				}
			}
		});
	}
}

function objectDeleteSelected()
{
	var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		alert(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_selected_objects',
			imeis: objects
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					initSelectList('manager_list');
						
					$('#cpanel_user_list_grid').trigger("reloadGrid");
					$('#cpanel_object_list_grid').trigger("reloadGrid");		
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function unusedObjectDelete(imei){
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_UNUSED_OBJECT']);
	if(answer)
	{
		var data = {
			cmd: 'delete_unused_object',
			imei: imei
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					$('#cpanel_unused_object_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);	
				}
			}
		});
	}
}

function unusedObjectDeleteSelected()
{
	var objects = $('#cpanel_unused_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		alert(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_selected_unused_objects',
			imeis: objects
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					$('#cpanel_unused_object_list_grid').trigger("reloadGrid");		
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function userObjectAdd(cmd){
	switch (cmd)
	{
		case "open":
			$('#dialog_user_object_add_objects').tokenize().clear();
			$("#dialog_user_object_add").dialog("open");
			break;
		case "add":
			var imeis = $('#dialog_user_object_add_objects').tokenize().toArray();
			
			imeis = JSON.stringify(imeis);
			
			var data = {
				cmd: 'add_user_objects',
				user_id: cpValues['user_edit_id'],
				imeis: imeis
			};
			
		   $.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
						$("#dialog_user_object_add").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_user_object_add").dialog("close");
			break;
	}
}

function userObjectEdit(imei){	
	var data = {
		cmd: 'edit_user_object',
		imei: imei,
		active: document.getElementById('dialog_user_edit_object_list_grid_active_' + imei).checked,
		active_dt: document.getElementById('dialog_user_edit_object_list_grid_active_dt_'+ imei ).value
	};
		
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
				//$('#cpanel_object_list_grid').trigger("reloadGrid");
			}
			else
			{
				alert(result);
			}
		}
	});
}

function userObjectDelete(imei){
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_OBJECT_FROM_USER_ACCOUNT']);
	if(answer)
	{
		var data = {
			cmd: 'delete_user_object',
			user_id: cpValues['user_edit_id'],
			imei: imei
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
					//$('#cpanel_user_list_grid').trigger("reloadGrid");
					//$('#cpanel_object_list_grid').trigger("reloadGrid");
				}
				else
				{
					alert(result);	
				}
			}
		});
	}
}

function userObjectDeleteSelected()
{
        var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		alert(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_selected_user_objects',
			user_id: cpValues['user_edit_id'],
			imeis: objects
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					initSelectList('manager_list');
						
					$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function userSubaccountEdit(id)
{
	var active = document.getElementById('dialog_user_edit_subaccount_list_grid_active_' + id).checked;
	var username = document.getElementById('dialog_user_edit_subaccount_list_grid_username_' + id).value;
	var email = document.getElementById('dialog_user_edit_subaccount_list_grid_email_'+ id ).value;
	var password = document.getElementById('dialog_user_edit_subaccount_list_grid_password_'+ id ).value;
	
	// username check
	if (username == '')
	{
		alert(la['USERNAME_CANT_BE_EMPTY']);
		return;
	}
	
	// email check
	if(!isEmailValid(email))
	{
		alert(la['THIS_EMAIL_IS_NOT_VALID']);
		return;
	}
	
	// password change
	if (password.length > 0)
	{
		if (password.length >= 6)
		{
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_CHANGE_USER_PASSWORD']);
			if(!answer)
			{
				return;
			}
		}
		else
		{
			alert(la['PASSWORD_LENGHT_AT_LEAST']);
			return;
		}
	}
	
	var data = {
		cmd: 'edit_user_subaccount',
		id: id,
		active: active,
		username: username,
		email: email,
		password: password
	};
		
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");
				//$('#cpanel_user_list_grid').trigger("reloadGrid");
			}
			else
			{
				alert(result);
			}
		}
	});
}

function userSubaccountDelete(id){
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
	if(answer)
	{
		var data = {
			cmd: 'delete_user_subaccount',
			id: id
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");
					//$('#cpanel_user_list_grid').trigger("reloadGrid");
				}
				else
				{
					alert(result);	
				}
			}
		});
	}
}

function userSubaccountDeleteSelected()
{
        var subaccounts = $('#dialog_user_edit_subaccount_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (subaccounts == '')
	{
		alert(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_selected_user_subaccounts',
			ids: subaccounts
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					initSelectList('manager_list');
						
					$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function userObjectActivePeriodGetAvgDate()
{
	var data = {
		cmd: 'get_object_active_period_avg_date',
		id: cpValues['user_edit_id']
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		success: function(result)
		{
			document.getElementById('dialog_user_edit_object_active_period_active_dt').value = result;
		}
	});
}

function userObjectActivePeriodSetSelected()
{
	var active = document.getElementById('dialog_user_edit_object_active_period_active').checked;
	var active_dt = document.getElementById('dialog_user_edit_object_active_period_active_dt').value;
	
	if (active_dt == '')
	{
		alert(la['DATE_CANT_BE_EMPTY'])
		return;
	}
	
	var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		alert(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_SET_ACTIVE_PERIOD_FOR_SELECTED_OBJECTS']);
	if(answer)
	{
		var data = {
			cmd: 'set_object_active_period_selected',
			imeis: objects,
			active: active,
			active_dt: active_dt
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);
				}
			}
		});	
	}
}

function userAdd(cmd)
{
	switch (cmd)
	{
		case "open":
			document.getElementById('dialog_user_add_email').value = '';
			$("#dialog_user_add").dialog("open");
			break;
		case "register":
			var email = document.getElementById('dialog_user_add_email').value;
			var send = document.getElementById('dialog_user_add_send').checked;
			
			if(!isEmailValid(email))
			{
				alert(la['THIS_EMAIL_IS_NOT_VALID']);
				return;
			}
			
			var data = {
				cmd: 'register_user',
				email: email,
				send: send,
				manager_id: cpValues['manager_id']
			};
			
		   $.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						serverTools('stats');
						initSelectList('manager_list');
						
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$("#dialog_user_add").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_user_add").dialog("close");
			break;
	}
}

function userEditLogin()
{
	userLogin(cpValues['user_edit_id']);
}

function userEdit(cmd)
{
	switch (cmd)
	{
		default:
			cpValues['user_edit_id'] = cmd;
			
			var data = {
				cmd: 'load_user_values',
				user_id: cpValues['user_edit_id']
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{					
					// set values
					document.getElementById('dialog_user_edit_account_username').value = result['username'];
					document.getElementById('dialog_user_edit_account_email').value = result['email'];
					document.getElementById('dialog_user_edit_account_password').value = '';					
					document.getElementById('dialog_user_edit_account_active').checked = strToBoolean(result['active']);
					document.getElementById('dialog_user_edit_account_expire').checked = strToBoolean(result['account_expire']);
					if (result['account_expire_dt'] == '')
					{
						result['account_expire_dt'] = moment().format("YYYY-MM-DD");
					}
					document.getElementById('dialog_user_edit_account_expire_dt').value = result['account_expire_dt'];
					
					var privileges = result['privileges'];
					
					if (cpValues['privileges'] == 'super_admin')
					{
						initSelectList("privileges_list_super_admin");
					}
					else if (cpValues['privileges'] == 'admin')
					{
						initSelectList("privileges_list_admin");
					}
					else
					{
						if (privileges['type'] == 'manager')
						{
							initSelectList("privileges_list_manager");
						}
						else
						{
							initSelectList("privileges_list_user");
						}
						
					}
					
					document.getElementById('dialog_user_edit_account_privileges').value = privileges['type'];
					document.getElementById('dialog_user_edit_account_manager_id').value = result['manager_id'];
					
					document.getElementById('dialog_user_edit_account_obj_add').value = result['obj_add'];
					document.getElementById('dialog_user_edit_account_obj_num').value = result['obj_num'];
					document.getElementById('dialog_user_edit_account_obj_dt').value = result['obj_dt'];
					document.getElementById('dialog_user_edit_account_manager_obj_num').value = result['manager_obj_num'];
					document.getElementById('dialog_user_edit_account_obj_edit').value = result['obj_edit'];
					document.getElementById('dialog_user_edit_account_obj_history_clear').value = result['obj_history_clear'];
					
					document.getElementById('dialog_user_edit_account_history').value = privileges['history'];
					document.getElementById('dialog_user_edit_account_reports').value = privileges['reports'];
					document.getElementById('dialog_user_edit_account_rilogbook').value = privileges['rilogbook'];
					document.getElementById('dialog_user_edit_account_object_control').value = privileges['object_control'];
					document.getElementById('dialog_user_edit_account_image_gallery').value = privileges['image_gallery'];
					document.getElementById('dialog_user_edit_account_chat').value = privileges['chat'];

					document.getElementById('dialog_user_edit_places_markers').value = result['places_markers'];
					document.getElementById('dialog_user_edit_places_routes').value = result['places_routes'];
					document.getElementById('dialog_user_edit_places_zones').value = result['places_zones'];
					document.getElementById('dialog_user_edit_api_active').value = result['api'];
					document.getElementById('dialog_user_edit_api_key').value = result['api_key'];
					
					var info = result['info'];
					
					document.getElementById('dialog_user_edit_account_contact_surname').value = info['name'];
					document.getElementById('dialog_user_edit_account_contact_company').value = info['company'];
					document.getElementById('dialog_user_edit_account_contact_address').value = info['address'];
					document.getElementById('dialog_user_edit_account_contact_post_code').value = info['post_code'];
					document.getElementById('dialog_user_edit_account_contact_city').value = info['city'];
					document.getElementById('dialog_user_edit_account_contact_country').value = info['country'];
					document.getElementById('dialog_user_edit_account_contact_phone1').value = info['phone1'];
					document.getElementById('dialog_user_edit_account_contact_phone2').value = info['phone2'];
					document.getElementById('dialog_user_edit_account_contact_email').value = info['email'];
					
					document.getElementById('dialog_user_edit_account_comment').value = result['comment'];
					
					// set values for later check while saving
					cpValues['user_edit_privileges'] = privileges['type'];
					
					// set object edit properties availability
					userEditCheck();
				}
			});
			
			$('#dialog_user_edit_object_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_user_object_list&id=' + cpValues['user_edit_id']});
			$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
			
			$('#dialog_user_edit_subaccount_list_grid').setGridParam({url:'func/fn_cpanel.php?cmd=load_user_subaccount_list&id=' + cpValues['user_edit_id']});
			$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");
			
			$("#dialog_user_edit").dialog("open");
			break;
		case "save":
			var username = document.getElementById('dialog_user_edit_account_username').value;
			var email = document.getElementById('dialog_user_edit_account_email').value;
			var password = document.getElementById('dialog_user_edit_account_password').value;
			var active = document.getElementById('dialog_user_edit_account_active').checked;
			var account_expire = document.getElementById('dialog_user_edit_account_expire').checked;
			var account_expire_dt = document.getElementById('dialog_user_edit_account_expire_dt').value;
			if (account_expire == false)
			{
                                account_expire_dt = '';
                        }
			var privileges_ = document.getElementById('dialog_user_edit_account_privileges').value;
			var manager_id = document.getElementById('dialog_user_edit_account_manager_id').value;
			var manager_obj_num = document.getElementById('dialog_user_edit_account_manager_obj_num').value;
			var obj_add = document.getElementById('dialog_user_edit_account_obj_add').value;
			var obj_num = document.getElementById('dialog_user_edit_account_obj_num').value;	
			var obj_dt = document.getElementById('dialog_user_edit_account_obj_dt').value;
			
			var history = strToBoolean(document.getElementById('dialog_user_edit_account_history').value);
			var reports = strToBoolean(document.getElementById('dialog_user_edit_account_reports').value);
			var rilogbook = strToBoolean(document.getElementById('dialog_user_edit_account_rilogbook').value);
			var object_control = strToBoolean(document.getElementById('dialog_user_edit_account_object_control').value);
			var image_gallery = strToBoolean(document.getElementById('dialog_user_edit_account_image_gallery').value);
			var chat = strToBoolean(document.getElementById('dialog_user_edit_account_chat').value);
			var sms_gateway_server = document.getElementById('dialog_user_edit_account_sms_gateway_server').value;
			
			var privileges = {
				type: privileges_,
				history: history,
				reports: reports,
				rilogbook: rilogbook,
				object_control: object_control,
				image_gallery: image_gallery,
				chat: chat
			};
			
			privileges = JSON.stringify(privileges);
			
			var obj_history_clear = document.getElementById('dialog_user_edit_account_obj_history_clear').value;
			var obj_edit = document.getElementById('dialog_user_edit_account_obj_edit').value;
			var places_markers = document.getElementById('dialog_user_edit_places_markers').value;
			var places_routes = document.getElementById('dialog_user_edit_places_routes').value;
			var places_zones = document.getElementById('dialog_user_edit_places_zones').value;
			var api = document.getElementById('dialog_user_edit_api_active').value;
			var api_key = document.getElementById('dialog_user_edit_api_key').value;
			
			var contact_name = document.getElementById('dialog_user_edit_account_contact_surname').value;
			var contact_company = document.getElementById('dialog_user_edit_account_contact_company').value;
			var contact_address = document.getElementById('dialog_user_edit_account_contact_address').value;
			var contact_post_code = document.getElementById('dialog_user_edit_account_contact_post_code').value;
			var contact_city = document.getElementById('dialog_user_edit_account_contact_city').value;
			var contact_country = document.getElementById('dialog_user_edit_account_contact_country').value;
			var contact_phone1 = document.getElementById('dialog_user_edit_account_contact_phone1').value;
			var contact_phone2 = document.getElementById('dialog_user_edit_account_contact_phone2').value;
			var contact_email = document.getElementById('dialog_user_edit_account_contact_email').value;
			
			var info = {
				name: contact_name,
				company: contact_company,
				address: contact_address,
				post_code: contact_post_code,
				city: contact_city,
				country: contact_country,
				phone1: contact_phone1,
				phone2: contact_phone2,
				email: contact_email
			};
			
			info = JSON.stringify(info);
			
			var comment = document.getElementById('dialog_user_edit_account_comment').value;
			
			// username check
			if (username == '')
			{
				alert(la['USERNAME_CANT_BE_EMPTY']);
				break;
			}
			
			// email check
			if(!isEmailValid(email))
			{
				alert(la['THIS_EMAIL_IS_NOT_VALID']);
				break;
			}
			
			// privileges check
			if ((cpValues['privileges'] == 'super_admin') || (cpValues['privileges'] == 'admin'))
			{
				// super admin privileges change alert
				if ((cpValues['user_edit_privileges'] == 'super_admin') && (privileges_ != 'super_admin'))
				{
					var answer = confirm (la['THIS_USER_HAS_SUPER_ADMIN_PRIVILEGES']);
					if(!answer)
					{
						document.getElementById("dialog_user_edit_account_privileges").value = 'super_admin';
						return;
					}
				}
				
				// admin privileges change alert
				if ((cpValues['user_edit_privileges'] == 'admin') && (privileges_ != 'admin'))
				{
					var answer = confirm (la['THIS_USER_HAS_ADMIN_PRIVILEGES']);
					if(!answer)
					{
						document.getElementById("dialog_user_edit_account_privileges").value = 'admin';
						return;
					}
				}
				
				// manager privileges change alert
				if ((cpValues['user_edit_privileges'] == 'manager') && (privileges_ != 'manager'))
				{
					var answer = confirm (la['THIS_USER_HAS_MANAGER_PRIVILEGES']);
					if(!answer)
					{
						document.getElementById("dialog_user_edit_account_privileges").value = 'manager';
						return;
					}
				}
			}
			
			// account obj num check
			if ((document.getElementById('dialog_user_edit_account_obj_num').disabled == false) && (obj_num == ''))
			{
				alert(la['SET_ACCOUNT_OBJECT_LIMIT'])
				return;
			}
			
			// account obj dt check
			if ((document.getElementById('dialog_user_edit_account_obj_dt').disabled == false) && (obj_dt == ''))
			{
				alert(la['SET_ACCOUNT_OBJECT_DATE_LIMIT'])
				return;
			}
			
			// manager obj num check
			if ((document.getElementById('dialog_user_edit_account_manager_obj_num').disabled == false) && (manager_obj_num == ''))
			{
				alert(la['SET_MANAGER_OBJECT_LIMIT'])
				return;
			}
			
			// password change
			if (password.length > 0)
			{
				if (password.length >= 6)
				{
					var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_CHANGE_USER_PASSWORD']);
					if(!answer)
					{
						break;
					}
				}
				else
				{
					alert(la['PASSWORD_LENGHT_AT_LEAST']);
					break;
				}
			}

			var data = {
				cmd: 'edit_user',
				id: cpValues['user_edit_id'],
				username: username,
				email: email,
				password: password,
				active: active,
				account_expire: account_expire,
				account_expire_dt: account_expire_dt,
				privileges: privileges,
				manager_id: manager_id,
				obj_add: obj_add,
				obj_num: obj_num,
				obj_dt: obj_dt,
				manager_obj_num: manager_obj_num,
				obj_edit: obj_edit,
				obj_history_clear: obj_history_clear,				
				sms_gateway_server: sms_gateway_server,
				places_markers: places_markers,
				places_routes: places_routes,
				places_zones: places_zones,
				api: api,
				api_key: api_key,
				info: info,
				comment: comment
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					if(result == 'OK')
					{
						initSelectList('manager_list');
						
						if ((cpValues['user_edit_privileges'] == 'manager') && (privileges != 'manager') && (cpValues['manager_id'] != 0))
						{
							switchCPManager(0);
						}
						
						$("#dialog_user_edit").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
		break;
	}
}

function userEditCheck()
{
	var selected_privileges = document.getElementById('dialog_user_edit_account_privileges').value;
	var selected_manager_id = document.getElementById('dialog_user_edit_account_manager_id').value;
	
	// prevent self user deactivation, expire account, level change
	if ((cpValues['user_id'] == cpValues['user_edit_id']))
	{
		document.getElementById('dialog_user_edit_account_active').disabled = true;
		document.getElementById('dialog_user_edit_account_expire').disabled = true;
		document.getElementById('dialog_user_edit_account_expire_dt').disabled = true;
		document.getElementById('dialog_user_edit_account_expire').checked = false;
		document.getElementById('dialog_user_edit_account_expire_dt').value = '';
		document.getElementById('dialog_user_edit_account_privileges').disabled = true;
	}
	else
	{
		document.getElementById('dialog_user_edit_account_active').disabled = false;
		document.getElementById('dialog_user_edit_account_expire').disabled = false;
		document.getElementById('dialog_user_edit_account_expire_dt').disabled = false;
		document.getElementById('dialog_user_edit_account_privileges').disabled = false;			
	}
	
	// if super admin or admin
	if ((cpValues['privileges'] == 'super_admin') || (cpValues['privileges'] == 'admin'))
	{
		switch (selected_privileges)
		{
			case "viewer":
				document.getElementById('dialog_user_edit_account_obj_add').disabled = false;
				
				document.getElementById('dialog_user_edit_account_manager_id').disabled = false;
				document.getElementById('dialog_user_edit_account_manager_obj_num').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_obj_num').value = '';
				break;
			case "user":
				document.getElementById('dialog_user_edit_account_obj_add').disabled = false;
				
				document.getElementById('dialog_user_edit_account_manager_id').disabled = false;
				document.getElementById('dialog_user_edit_account_manager_obj_num').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_obj_num').value = '';
				break;
			case "manager":
				document.getElementById('dialog_user_edit_account_obj_add').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_add').value = false;
				
				document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_id').value = 0;
				document.getElementById('dialog_user_edit_account_manager_obj_num').disabled = false;
				break;
			case "admin":
				document.getElementById('dialog_user_edit_account_obj_add').disabled = false;
				
				document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_id').value = 0;
				document.getElementById('dialog_user_edit_account_manager_obj_num').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_obj_num').value = '';
				break;
			case "super_admin":
				document.getElementById('dialog_user_edit_account_obj_add').disabled = false;
				
				document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_id').value = 0;
				document.getElementById('dialog_user_edit_account_manager_obj_num').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_obj_num').value = '';
				break;
		}
	}
	
	// if manager
	if (cpValues['privileges'] == 'manager')
	{
		document.getElementById('dialog_user_edit_account_obj_add').disabled = true;
		document.getElementById('dialog_user_edit_account_obj_add').value = false;
		
		document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
		document.getElementById('dialog_user_edit_account_manager_obj_num').disabled = true;
		document.getElementById('dialog_user_edit_account_manager_obj_num').value = '';
	}
	
	// if user has manager
	if (selected_manager_id != 0)
	{
                document.getElementById('dialog_user_edit_account_obj_add').disabled = true;
		document.getElementById('dialog_user_edit_account_obj_add').value = false;
        }

	// objects account
	switch (document.getElementById('dialog_user_edit_account_obj_add').value)
	{
		case "false":
			document.getElementById('dialog_user_edit_account_obj_num').disabled = true;
			document.getElementById('dialog_user_edit_account_obj_dt').disabled = true;
			document.getElementById('dialog_user_edit_account_obj_num').value = '';
			document.getElementById('dialog_user_edit_account_obj_dt').value = '';
			break;
		case "trial":
			document.getElementById('dialog_user_edit_account_obj_num').disabled = true;
			document.getElementById('dialog_user_edit_account_obj_dt').disabled = true;
			document.getElementById('dialog_user_edit_account_obj_num').value = '';
			document.getElementById('dialog_user_edit_account_obj_dt').value = '';
			break;
		case "limited":
			document.getElementById('dialog_user_edit_account_obj_num').disabled = false;
			document.getElementById('dialog_user_edit_account_obj_dt').disabled = false;
			break;
		case "unlimited":
			document.getElementById('dialog_user_edit_account_obj_num').disabled = true;
			document.getElementById('dialog_user_edit_account_obj_dt').disabled = false;
			document.getElementById('dialog_user_edit_account_obj_num').value = '';
			break;
	}
}

function userDelete(id)
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
	if(answer)
	{
		var data = {
			cmd: 'delete_user',
			id: id
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					initSelectList('manager_list');
						
					$('#cpanel_user_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function userDeleteSelected()
{
	var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (users == '')
	{
		alert(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_selected_users',
			ids: users
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('stats');
					initSelectList('manager_list');
						
					$('#cpanel_user_list_grid').trigger("reloadGrid");	
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function userLogin(id)
{
	var data = {
		cmd: 'login_user',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		success: function(result)
		{
			if(result == 'OK')
			{
				location.href = 'tracking.php';
			}
			else
			{
				alert(result);	
			}
		}
	});
}

function geocoderClearCache()
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_CLEAR_GEOCODER_CACHE']);
	if(answer)
	{
		var data = {
			cmd: 'clear_geocoder_cache'
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					alert(la['GEOCODER_CACHE_CLEARED']);
				}
				else
				{
					alert(result);
				}
			}
		});		
	}
}

function sendEmailSendToSwitch()
{
	var send_to = document.getElementById('send_email_send_to').value;
	
	switch (send_to)
	{
		case "all":
			$('#send_email_username').tokenize().clear();
			document.getElementById('send_email_username_row').style.display = "none";
			break;
		
		case "selected":
			$('#send_email_username').tokenize().clear();
			document.getElementById('send_email_username_row').style.display = "";
			
			var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
			
			$('#send_email_username').tokenize().options.newElements = true;
			for(var i=0;i<users.length;i++)
			{
				var value = users[i];
				var text = $('#cpanel_user_list_grid').jqGrid('getCell', value, 'username');
				$('#send_email_username').tokenize().tokenAdd(value, text);
			}
			$('#send_email_username').tokenize().options.newElements = false;
						
			break;
	}
}

function sendEmail(cmd)
{
	switch (cmd)
	{
		case "open":
			document.getElementById('send_email_send_to').value = 'all';
			document.getElementById('send_email_subject').value = '';
			document.getElementById('send_email_message').value = '';
			document.getElementById('send_email_status').innerHTML = '';
			
			sendEmailSendToSwitch();
			
			$("#dialog_send_email").dialog("open");
			
			break;
		case "cancel":
			$("#dialog_send_email").dialog("close");
			break;
		case "send":
			var send_to = document.getElementById('send_email_send_to').value;
			var user_ids = $('#send_email_username').tokenize().toArray();
			var subject = document.getElementById('send_email_subject').value;
			var message = document.getElementById('send_email_message').value;
			
			if ((send_to == 'selected') && (user_ids.length == 0) || (subject == '') || (message == ''))
			{
				alert(la['ALL_AVAILABLE_FIELDS_SHOULD_BE_FILLED_OUT']);
				break;
                        }
			
			user_ids = JSON.stringify(user_ids);
			
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_SEND_THIS_MESSAGE']);
			if(answer)
			{
				document.getElementById('send_email_status').innerHTML = la['SENDING_PLEASE_WAIT'];
				
				var data = {
					cmd: 'send_email',
					manager_id: cpValues['manager_id'],
					send_to: send_to,
					user_ids: user_ids,
					subject: subject,
					message: message
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.php",
					data: data,
					success: function(result)
					{
						if(result == 'OK')
						{
							document.getElementById('send_email_status').innerHTML = la['SENDING_FINISHED'];
						}
						else
						{
							document.getElementById('send_email_status').innerHTML = la['CANT_SEND_EMAIL'] + ' ' + la['CONTACT_ADMINISTRATOR'];
							alert(result);
						}
					}
				});
			}
			break;
		case "test":
			var subject = document.getElementById('send_email_subject').value;
			var message = document.getElementById('send_email_message').value;
			
			if ((subject == '') || (message == ''))
			{
				alert(la['ALL_AVAILABLE_FIELDS_SHOULD_BE_FILLED_OUT']);
				break;
                        }
			
			
			var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_SEND_TEST_MESSAGE_TO_YOUR_EMAIL']);
			if(answer)
			{
				document.getElementById('send_email_status').innerHTML = la['SENDING_PLEASE_WAIT'];
				
				var data = {
					cmd: 'send_email_test',
					subject: subject,
					message: message
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.php",
					data: data,
					success: function(result)
					{
						if(result == 'OK')
						{
							document.getElementById('send_email_status').innerHTML = la['SENDING_FINISHED'];
						}
						else
						{
							document.getElementById('send_email_status').innerHTML = la['CANT_SEND_EMAIL'] + ' ' + la['CONTACT_ADMINISTRATOR'];
							alert(result);
						}
					}
				});
			}
			break;
	}
}

function notifyCheck(what)
{
	switch (what)
	{
		case "session_check":
			
			if (gsValues['session_check'] == false)
			{
				break;
			}
			
			clearTimeout(timer_sessionCheck);
			
			var data = {
				cmd: 'session_check'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_connect.php",
				data: data,
				cache: false,
				error: function(statusCode, errorThrown)
				{
					timer_sessionCheck = setTimeout("notifyCheck('session_check');", gsValues['session_check'] * 1000);
				},
				success: function(result)
				{
					if (result == 'false')
					{
						$("#blocking_panel").show();
					}
					else
					{
						timer_sessionCheck = setTimeout("notifyCheck('session_check');", gsValues['session_check'] * 1000);
					}
				}
			});
			break;
	}
}

function SMSGatewayClearQueue()
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_CLEAR_SMS_QUEUE']);
	if(answer)
	{
		var data = {
			cmd: 'clear_sms_queue'
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					document.getElementById('cpanel_manage_server_sms_gateway_total_in_queue').innerHTML = '0';
				}
				else
				{
					alert(result);
				}
			}
		});		
	}
}

function SMSGatewayGenerateIdentifier()
{
	var identifier = cpValues['server_key'] + '_' + moment();
	
	identifier = CryptoJS.MD5(identifier).toString().replace(/\D/g,'');
	identifier = identifier + identifier;
	identifier = identifier.substr(0, 20);
	
	return identifier;
}

function customMapProperties(cmd)
{
	switch (cmd)
	{
		default:
			var id = cmd;
			
			cpValues['edit_custom_map_id'] = id;
			
			var data = {
				cmd: 'load_custom_map',
				map_id: cpValues['edit_custom_map_id']
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{
					document.getElementById('dialog_custom_map_name').value = result['name'];
					document.getElementById('dialog_custom_map_active').checked = strToBoolean(result['active']);
					document.getElementById('dialog_custom_map_type').value = result['type'];
					document.getElementById('dialog_custom_map_url').value = result['url'];
					document.getElementById('dialog_custom_map_layers').value = result['layers'];
				}
			});
			
			$("#dialog_custom_map_properties").dialog("open");
			break;
		
		case "add":
			cpValues['edit_custom_map_id'] = false;
			document.getElementById('dialog_custom_map_name').value = '';
			document.getElementById('dialog_custom_map_active').checked = true;
			document.getElementById('dialog_custom_map_type').value = 'tms';
			document.getElementById('dialog_custom_map_url').value = '';
			document.getElementById('dialog_custom_map_layers').value = '';
			
			$("#dialog_custom_map_properties").dialog("open");	
			break;
			
		case "cancel":
			$("#dialog_custom_map_properties").dialog("close");	
			break;
			
		case "save":
			var name = document.getElementById('dialog_custom_map_name').value;
			var active = document.getElementById('dialog_custom_map_active').checked;
			var type = document.getElementById('dialog_custom_map_type').value;
			var url = document.getElementById('dialog_custom_map_url').value;
			var layers = document.getElementById('dialog_custom_map_layers').value;
			
			if (name == "")
			{
				alert(la['NAME_CANT_BE_EMPTY']);
				break;
			}
			
			if (url == "")
			{
				alert(la['URL_CANT_BE_EMPTY']);
				break;
			}
			
			var data = {
				cmd: 'save_custom_map',
				map_id: cpValues['edit_custom_map_id'],
				name: name,
				active: active,
				type: type,
				url: url,
				layers: layers
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				cache: false,
				success: function(result)
				{
					if(result == 'OK')
					{
						serverTools('custom_maps');
						$("#dialog_custom_map_properties").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
			break;
	}
}

function customMapDeleteAll()
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_ALL_CUSTOM_MAPS']);
	if(answer)
	{
		var data = {
			cmd: 'delete_all_custom_maps'
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.tools.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('custom_maps');
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function customMapDelete(map_id)
{
	var answer = confirm (la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'] + '?');
	if(answer)
	{
		var data = {
			cmd: 'delete_custom_map',
			map_id: map_id
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.tools.php",
			data: data,
			success: function(result)
			{
				if(result == 'OK')
				{
					serverTools('custom_maps');
				}
				else
				{
					alert(result);
				}
			}
		});
	}
}

function templateProperties(cmd)
{
	switch (cmd)
	{
		default:
			var name = cmd;
			
			cpValues['template_edit_name'] = name;
			
			var variables = '';
			
			switch (name)
			{
				case "account_registration":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_SERVER_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_URL_LOGIN']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_EMAIL']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_USERNAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_PASSWORD']+'</div>';					
					break;
				
				case "account_registration_au":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_SERVER_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_URL_AU']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_URL_AU_MOBILE']+'</div>';	
					break;
				
				case "account_recover":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_SERVER_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_URL_LOGIN']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_EMAIL']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_USERNAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_PASSWORD']+'</div>';					
					break;
				
				case "expiring_account":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_SERVER_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_URL_SHOP']+'</div>';				
					break;
				
				case "expiring_objects":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_SERVER_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_URL_SHOP']+'</div>';				
					break;
				
				case "event_email":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_IMEI']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_EVENT']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_LAT']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_LNG']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_ADDRESS']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_SPEED']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_ALT']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_ANGLE']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_DT_POS']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_DT_SER']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_G_MAP']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_TR_MODEL']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_PL_NUM']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_DRIVER']+'</div>';
					break;
				
				case "event_sms":
					variables = 	'<div class="row3">'+la['VAR_TEMPLATE_NAME']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_IMEI']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_EVENT']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_LAT']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_LNG']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_ADDRESS']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_SPEED']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_ALT']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_ANGLE']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_DT_POS']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_DT_SER']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_G_MAP']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_TR_MODEL']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_PL_NUM']+'</div>\
							<div class="row3">'+la['VAR_TEMPLATE_DRIVER']+'</div>';
					break;
			}
			
			document.getElementById('dialog_template_variables').innerHTML = variables;
			
			document.getElementById('dialog_template_name').value = la['TEMPLATE_' + name.toUpperCase()];
			
			templateProperties('load');
			
			$("#dialog_template_properties").dialog("open");
			break;
		
		case "load":
			
			var name = cpValues['template_edit_name'];
			var language = document.getElementById('dialog_template_language').value;
			
			var data = {
				cmd: 'load_template',
				name: name,
				language: language
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{
					document.getElementById('dialog_template_subject').value = result['subject'];
					document.getElementById('dialog_template_message').value = result['message'];
				}
			});
			
			break;
			
		case "cancel":
			$("#dialog_template_properties").dialog("close");	
			break;
			
		case "save":
			var name = cpValues['template_edit_name'];
			var language = document.getElementById('dialog_template_language').value;
			var message = document.getElementById('dialog_template_message').value;
			var subject = document.getElementById('dialog_template_subject').value;
			
			var data = {
				cmd: 'save_template',
				name: name,
				language: language,
				message: message,
				subject: subject
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.tools.php",
				data: data,
				cache: false,
				success: function(result)
				{
					if(result == 'OK')
					{
						$("#dialog_template_properties").dialog("close");
					}
					else
					{
						alert(result);
					}
				}
			});
			break;
	}
}