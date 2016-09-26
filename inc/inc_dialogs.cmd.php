<div id="dialog_cmd" title="<? echo $la['OBJECT_CONTROL'];?>">
	<div id="cmd_tabs">
		<ul>           
			<li><a href="#cmd_control_tab"><? echo $la['CONTROL'];?></a></li>
			<li><a href="#cmd_templates_tab"><? echo $la['TEMPLATES'];?></a></li>
		</ul>
		
		<div id="cmd_control_tab">
			<div class="row3">
				<div class="block width100">
					<div class="container last">
						<div class="row2">
							<div class="width20"><? echo $la['OBJECT'];?></div>
							<div class="width29"><select class="width100" id="cmd_object_list" onchange="cmdTemplateList();"></select></div>
						        <div class="width1"></div>
							<div class="width20"><? echo $la['GATEWAY'];?></div>
							<div class="width30">
								<select id="cmd_gateway" style="width: 70px;"/>
									<option value="gprs">GPRS</option>
									<option value="sms">SMS</option>
								</select>
							</div>
						</div>
						<div class="row2">
							<div class="width20"><? echo $la['TEMPLATE'];?></div>
							<div class="width29"><select class="width100" id="cmd_template_list" onchange="cmdTemplateSwitch();"></select></div>
						        <div class="width1"></div>
							<div class="width20"><? echo $la['TYPE'];?></div>
							<div class="width30">
								<select id="cmd_type" style="width: 70px;"/>
									<option value="ascii">ASCII</option>
									<option value="hex">HEX</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="block width100">
					<div class="container last">
						<div class="row2">
							<div class="width20"><? echo $la['COMMAND'];?></div>
							<div class="width65">
								<input id="cmd_cmd" class="inputbox" type="text" value="" maxlength="256">
							</div>
							<div class="width1"></div>
							<div class="width14">
								<input class="button width100" type="button" onclick="cmdSend();" value="<? echo $la['SEND']; ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<table id="cmd_status_list_grid"></table>
			<div id="cmd_status_list_grid_pager"></div>
		</div>
		
		<div id="cmd_templates_tab">
			<table id="cmd_template_list_grid"></table>
			<div id="cmd_template_list_grid_pager"></div>
		</div>
	</div>
</div>

<div id="dialog_cmd_template_properties" title="<? echo $la['COMMAND_PROPERTIES'];?>">
	<div class="row3">
		<div class="title-block"><? echo $la['TEMPLATE']; ?></div>
		<div class="row2">
			<div class="width35"><? echo $la['NAME']; ?></div>
			<div class="width65"><input id="dialog_cmd_template_name" class="inputbox" type="text" value="" maxlength="20"></div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['HIDE_UNUSED_PROTOCOLS']; ?></div>
			<div class="width65">
				<input id="dialog_cmd_template_hide_unsed_protocols" type="checkbox" class="checkbox" onchange="cmdTemplateProtocolList();"/>
			</div>
		</div>
		<div class="row2">
			<div class="width35">
				<? echo $la['PROTOCOL']; ?>
			</div>
			<div class="width65">
				<select class="width100" id="dialog_cmd_template_protocol"></select>
			</div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['GATEWAY'];?></div>
			<div class="width65">
				<select id="dialog_cmd_template_gateway" style="width: 70px;"/>
					<option value="gprs">GPRS</option>
					<option value="sms">SMS</option>
				</select>
			</div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['TYPE'];?></div>
			<div class="width65">
				<select id="dialog_cmd_template_type" style="width: 70px;"/>
					<option value="ascii">ASCII</option>
					<option value="hex">HEX</option>
				</select>
			</div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['COMMAND'];?></div>
			<div class="width65">
				<input id="dialog_cmd_template_cmd" class="inputbox" type="text" value="" maxlength="256">
			</div>
		</div>
	</div>
	
	<div class="row3">
		<div class="block width100">
			<div class="container last">
				<div class="title-block"><? echo $la['VARIABLES']; ?></div>
				<div class="row2">
					<div class="row3"><? echo $la['VAR_TEMPLATE_IMEI']; ?></div>
				</div>
			</div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="cmdTemplateProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="cmdTemplateProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>