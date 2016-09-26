<div id="dialog_about" title="<? echo $la['ABOUT']; ?>">
	<div class="row">
		<center><img src="<? echo $gsValues['URL_LOGO']; ?>" /></center>
	</div>
	<center><? echo $la['COPYRIGHT']; ?></center>
</div>

<div id="dialog_loading" title="">
	<center>
        	<div class="loader">
			<? echo $la['LOADING_PLEASE_WAIT']; ?>
			<span></span>
		</div>
	</center>
</div>

<div id="dialog_show_point" title="<? echo $la['SHOW_POINT'];?>">
	<div class="row3">
		<div class="row2">
			<div class="width30"><? echo $la['LATITUDE']; ?></div>
			<div class="width70"><input id="dialog_show_point_lat" class="inputbox" type="text" value="" maxlength="15"></div>
		</div>
		<div class="row2">
			<div class="width30"><? echo $la['LONGITUDE']; ?></div>
			<div class="width70"><input id="dialog_show_point_lng" class="inputbox" type="text" maxlength="15"></div>
		</div>
	</div>
	
	<center>
	    <input class="button icon-show icon" type="button" onclick="utilsShowPoint();" value="<? echo $la['SHOW']; ?>" />
	    <input class="button icon-close icon" type="button" onclick="$('#dialog_show_point').dialog('close');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_address_search" title="<? echo $la['ADDRESS_SEARCH'];?>">
	<div class="row3">
		<div class="row2">
			<div class="width100">
				<input class="inputbox_search_addr width100" type='text' id='dialog_address_search_addr' onkeydown="if (event.keyCode == 13) utilsSearchAddress();" maxlength="100"/>
			</div>
		</div>
	</div>
		
	<center>
	    <input class="button icon-search icon" type="button" onclick="utilsSearchAddress();" value="<? echo $la['SEARCH']; ?>" />&nbsp;&nbsp;&nbsp;
	    <input class="button icon-close icon" type="button" onclick="$('#dialog_address_search').dialog('close');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_import_export" title="<? echo $la['IMPORT_EXPORT_TOOLS']; ?>">
	<div class="row2">
		<div class="width100">
			<input class="button width100" type="button" value="<? echo $la['LOAD_GSR']; ?>" onclick="historyLoadGSR();"/>
		</div>
	</div>
	<div class="row2">
		<div class="width100">
			<input class="button width100" type="button" value="<? echo $la['EXPORT_GSR']; ?>" onclick="historyExportGSR();"/>
		</div>
	</div>
	<div class="row2">
		<div class="width100">
			<input class="button width100" type="button" value="<? echo $la['EXPORT_KML']; ?>" onclick="historyExportKML();"/>
		</div>
	</div>
	<div class="row2">
		<div class="width100">
			<input class="button width100" type="button" value="<? echo $la['EXPORT_GPX']; ?>" onclick="historyExportGPX();"/>
		</div>
	</div>
	<div class="row2">
		<div class="width100">
			<input class="button width100" type="button" value="<? echo $la['EXPORT_CSV']; ?>" onclick="historyExportCSV();"/>
		</div>
	</div>
</div>

<input id="load_file" type="file" style="display: none;" onchange=""/>