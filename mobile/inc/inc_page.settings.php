<div id="page_settings" style="display: none;">
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['MAP_STARTUP_POSITION']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_settings_map_startup_possition" class="form-control">
					<option value="default"><? echo $la['DEFAULT'];?></option>
					<option value="last"><? echo $la['REMEMBER_LAST'];?></option>
					<option value="fit"><? echo $la['FIT_OBJECTS'];?></option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['LANGUAGE']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_settings_language" class="form-control">
					<? echo getLanguageList(); ?>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['UNIT_OF_DISTANCE']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_settings_distance_unit" class="form-control">
					<option value="km"><? echo $la['KILOMETER'];?></option>
					<option value="mi"><? echo $la['MILE'];?></option>
					<option value="nm"><? echo $la['NAUTICAL_MILE'];?></option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['UNIT_OF_CAPACITY']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_settings_capacity_unit" class="form-control">
					<option value="l"><? echo $la['LITER'];?></option>
					<option value="g"><? echo $la['GALLON'];?></option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['UNIT_OF_TEMPERATURE']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_settings_temperature_unit" class="form-control">
					<option value="c"><? echo $la['CELSIUS'];?></option>
					<option value="f"><? echo $la['FAHRENHEIT'];?></option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['TIMEZONE']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_settings_timezone" class="form-control">
					<? include ("../inc/inc_timezones.php"); ?>
				</select>
			</div>
		</div>
	</div>
	
	<div class="clearfix">
	<a href="#" class="btn btn-default dropdown-toggle pull-left save-btn" onclick="settingsSave();">
		<i class="glyphicon glyphicon-floppy-disk"></i>
		<? echo $la['SAVE']; ?>
	</a>
	</div>
	
	<a href="#" class="btn btn-default btn-blue show-menu pull-left back-btn" onclick="switchPage('menu');">
		<i class="glyphicon glyphicon-menu-left"></i>
		<? echo $la['BACK']; ?>
	</a>
	
</div>