<div id="page_history" style="display: none;">
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['OBJECT']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_history_object_list" class="form-control"></select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['FILTER']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_history_filter" class="form-control" onChange="switchHistoryReportsDateFilter();">
					<option value="0" selected>&nbsp;</option>
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
	</div>
	
	<div class="row vertical-align">
		<div class="col-xs-6">
			<? echo $la['TIME_FROM']; ?>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<div class="input-group date">
				<input type="text" class="form-control" data-field="datetime" id="page_history_date_from" readonly="readonly"/>
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row vertical-align">
		<div class="col-xs-6">
			<? echo $la['TIME_TO']; ?>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<div class="input-group date">
				<input type="text" class="form-control" data-field="datetime" id="page_history_date_to" readonly="readonly"/>
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['STOP_DURATION']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_history_stop_duration" class="form-control">
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
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['STOPS']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_history_stops" class="form-control" onChange="historyRouteToggleStops();">
					<option value="false"><? echo $la['NO']; ?></option>
					<option value="true" selected><? echo $la['YES']; ?></option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<? echo $la['EVENTS']; ?>
			</div>
			<div class="col-xs-6">
				<select id="page_history_events" class="form-control" onChange="historyRouteToggleEvents();">
					<option value="false"><? echo $la['NO']; ?></option>
					<option value="true" selected><? echo $la['YES']; ?></option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="row vertical-align">
			<div class="col-xs-6">
				<button type="button" class="btn btn-default dropdown-toggle" aria-haspopup="true" aria-expanded="false" onClick="historyLoadRoute();">
					<? echo $la['SHOW']; ?>
				</button>
			</div>
			<div class="col-xs-6">
				<button type="button" class="btn btn-default dropdown-toggle" aria-haspopup="true" aria-expanded="false" onClick="historyHideRoute();">
					<? echo $la['HIDE']; ?>
				</button>
			</div>
		</div>
	</div>
	
	<a href="#" class="btn btn-default btn-blue show-menu pull-left back-btn" onclick="switchPage('menu');">
		<i class="glyphicon glyphicon-menu-left"></i>
		<? echo $la['BACK']; ?>
	</a>
</div>