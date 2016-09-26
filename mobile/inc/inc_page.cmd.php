<div id="page_cmd" style="display: none;">
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['OBJECT']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_cmd_object_list" onchange="cmdTemplateList();" class="form-control"></select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['TEMPLATE']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_cmd_template_list" onchange="cmdTemplateSwitch();" class="form-control"></select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['GATEWAY']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_cmd_gateway" class="form-control">
					<option value="gprs">GPRS</option>
					<option value="sms">SMS</option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['TYPE']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_cmd_type" class="form-control">
					<option value="ascii">ASCII</option>
					<option value="hex">HEX</option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['COMMAND']; ?>
			</div>
			<div class="col-xs-6">
				<input id="page_cmd_cmd" class="form-control" type="text" value="" maxlength="256">
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-12">
				<button type="button" class="btn btn-default dropdown-toggle" aria-haspopup="true" aria-expanded="false" onclick="cmdSend();">
					<? echo $la['SEND']; ?>
				</button>
			</div>
		</div>
	</div>
	
	<a href="#" class="btn btn-default btn-blue show-menu pull-left back-btn" onclick="switchPage('menu');">
		<i class="glyphicon glyphicon-menu-left"></i>
		<? echo $la['BACK']; ?>
	</a>
</div>