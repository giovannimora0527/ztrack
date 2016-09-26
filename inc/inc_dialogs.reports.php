<div id="dialog_reports" title="<? echo $la['REPORTS']; ?>">
	<div class="controls-block width100">
		<div class="block width50">
			<input class="button icon-new icon" type="button" onclick="historyReportsNew();" value="<? echo $la['NEW']; ?>" />
			<input class="button icon-save icon" type="button" onclick="historyReportsSave();" value="<? echo $la['SAVE']; ?>" />
		</div>
		<div class="block width50">
			<input class="button icon-create icon float-right" type="button" onclick="historyReportsGenerate();" value="<? echo $la['GENERATE']; ?>" />
		</div>
	</div>
	
	<div class="row3">	
		<div class="title-block"><? echo $la['REPORT'];?></div>
		<div class="report-block block width50">
			<div class="container">
				<div class="row2">
					<div class="width50"><? echo $la['NAME'];?></div>
					<div class="width50"><input id="dialog_reports_name" class="inputbox" type="text" value="" maxlength="30"></div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['TYPE']; ?></div>
					<div class="width50">
						<select class="width100" id="dialog_reports_type" onchange="historyReportsSwitchType();">
							<option value="general" selected><? echo $la['GENERAL_INFO']; ?></option>
							<option value="general_merged"><? echo $la['GENERAL_INFO_MERGED']; ?></option>
							<option value="object_info"><? echo $la['OBJECT_INFO']; ?></option>
							<option value="drives_stops"><? echo $la['DRIVES_AND_STOPS']; ?></option>
							<option value="travel_sheet"><? echo $la['TRAVEL_SHEET']; ?></option>
							<option value="events"><? echo $la['EVENTS']; ?></option>
							<option value="overspeed"><? echo $la['OVERSPEEDS']; ?></option>
							<option value="underspeed"><? echo $la['UNDERSPEEDS']; ?></option>
							<option value="zone_in_out"><? echo $la['ZONE_IN_OUT']; ?></option>
							<option value="service"><? echo $la['SERVICE']; ?></option>
							<option value="rag"><? echo $la['DRIVER_BEHAVIOR_RAG']; ?></option>
							<option value="fuelfillings"><? echo $la['FUEL_FILLINGS']; ?></option>
							<option value="fuelthefts"><? echo $la['FUEL_THEFTS']; ?></option>
							<option value="logic_sensor_info"><? echo $la['LOGIC_SENSOR_INFO']; ?></option>
							<option value="acc_graph"><? echo $la['IGNITION_GRAPH']; ?></option>
							<option value="fuellevel_graph"><? echo $la['FUEL_LEVEL_GRAPH']; ?></option>
							<option value="temperature_graph"><? echo $la['TEMPERATURE_GRAPH']; ?></option>
							<option value="sensor_graph"><? echo $la['SENSOR_GRAPH']; ?></option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['FORMAT']; ?></div>
					<div class="width50">
						<select id="dialog_reports_format" style="width:80px;"/>
							<option value="html">HTML</option>
							<option value="pdf">PDF</option>
							<option value="xls">XLS</option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['SHOW_ADDRESSES']; ?></div>
					<div class="width50"><input id="dialog_reports_show_addresses" type="checkbox" class="checkbox" disabled/></div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['ZONES_INSTEAD_OF_ADDRESSES']; ?></div>
					<div class="width50"><input id="dialog_reports_zones_addresses" type="checkbox" class="checkbox" disabled/></div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['STOPS']; ?></div>
					<div class="width50">
						<select id="dialog_reports_stop_duration" style="width:80px;"/>
							<option value="1">> 1 min</option>
							<option value="2">> 2 min</option>
							<option value="5">> 5 min</option>
							<option value="10">> 10 min</option>
							<option value="20">> 20 min</option>
							<option value="30">> 30 min</option>
							<option value="60">> 1 h</option>
							<option value="120">> 2 h</option>
							<option value="300">> 5 h</option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['SPEED_LIMIT']; ?> (<? echo $la["UNIT_SPEED"]; ?>)</div>
					<div class="width50"><input id="dialog_reports_speed_limit" onkeypress="return isNumberKey(event);" class="inputbox" type="text" maxlength="3"/></div>
				</div>
			</div>
		</div>

		<div class="report-block block width50">
			<div class="container last">
				<div id="reports_tabs">
					<ul>           
						<li id="reports_tabs"><a href="#dialog_reports_objects_tab"><? echo $la['OBJECTS']; ?></a></li>
						<li id="reports_tabs"><a href="#dialog_reports_zones_tab"><? echo $la['ZONES']; ?></a></li>
						<li id="reports_tabs"><a href="#dialog_reports_data_items_tab"><? echo $la['DATA_ITEMS']; ?></a></li>
						<li id="reports_tabs"><a href="#dialog_reports_sensors_tab"><? echo $la['SENSORS']; ?></a></li>
					</ul>              
					<div id="dialog_reports_objects_tab">
						<select class="width100" id="dialog_reports_object_list" style="height:159px;" multiple="multiple" onchange="historyReportsSelectObject();"></select>
					</div>
					<div id="dialog_reports_zones_tab">
						<select class="width100" id="dialog_reports_zone_list" style="height:159px;" multiple="multiple" disabled></select>
					</div>
					<div id="dialog_reports_data_items_tab">
						<select class="width100" id="dialog_reports_data_item_list" style="height:159px;" multiple="multiple"></select>
					</div>
					<div id="dialog_reports_sensors_tab">
						<select class="width100" id="dialog_reports_sensor_list" style="height:159px;" multiple="multiple" disabled></select>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row3">
		<div class="schedule-block block width50">
			<div class="container">
				<div class="title-block"><? echo $la['SCHEDULE'];?></div>
				<div class="row2">
					<div class="width50"><? echo $la['DAILY'];?></div>
					<div class="width50"><input id="dialog_reports_schedule_period_daily" type="checkbox" <? if ($gsValues['REPORTS_SCHEDULE'] == 'false') { ?> disabled=disabled <? } ?>/></div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['WEEKLY'];?></div>
					<div class="width50"><input id="dialog_reports_schedule_period_weekly" type="checkbox" <? if ($gsValues['REPORTS_SCHEDULE'] == 'false') { ?> disabled=disabled <? } ?>/></div>
				</div>
				<div class="row2">
					<div class="width50"><? echo $la['SEND_TO_EMAIL'];?></div>
					<div class="width50"><input id="dialog_reports_schedule_email_address" class="inputbox" type="text" value="" maxlength="500" placeholder="<? echo $la['EMAIL_ADDRESS']; ?>" <? if ($gsValues['REPORTS_SCHEDULE'] == 'false') { ?> disabled=disabled <? } ?>/></div>
				</div>
			</div>
		</div>
		<div class="time-period block width50">
			<div class="container last">
				<div class="title-block"><? echo $la['TIME_PERIOD'];?></div>
				<div class="row2">
					<div class="width45"><? echo $la['FILTER'];?></div>
					<div class="width55">
						<select class="width100" id="dialog_reports_filter" onchange="switchHistoryReportsDateFilter('reports');">
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
					<div class="width45"><? echo $la['TIME_FROM']; ?></div>
					<div class="width27">
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_reports_date_from" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width12">
						<select class="width100" id="dialog_reports_hour_from">
						<? include ("inc/inc_dt.hours.php"); ?>
						</select>
					</div>
					<div class="width2"></div>
					<div class="width12">
						<select class="width100" id="dialog_reports_minute_from">
						<? include ("inc/inc_dt.minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width45"><? echo $la['TIME_TO']; ?></div>
					<div class="width27">
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_reports_date_to" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width12">
						<select class="width100" id="dialog_reports_hour_to">
						<? include ("inc/inc_dt.hours.php"); ?>
						</select>
					</div>
					<div class="width2"></div>
					<div class="width12">
						<select class="width100" id="dialog_reports_minute_to">
						<? include ("inc/inc_dt.minutes.php"); ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<table id="report_templates_list_grid"></table>
	<div id="report_templates_list_grid_pager"></div>
    
</div>
