<div id="page_events" style="display: none;">
	<form role="form">
		<div class="input-group form-group btn-group">
			<input id="page_event_search" class="form-control" type="search" placeholder="<? echo $la['SEARCH']; ?>..." onkeyup="eventLoadList();"/>
			<span id="page_event_search_clear" class="input-group-addon">
				<span class="glyphicon glyphicon-remove"></span>
			</span>
		</div>
		<div id="page_event_list" class="list-group"></div>
	</form>
	<a href="#" class="btn btn-default btn-blue show-menu pull-left back-btn" onclick="switchPage('menu');;">
		<i class="glyphicon glyphicon-menu-left"></i>
		<? echo $la['BACK']; ?>
	</a>
</div>