<div id="dialog_image_gallery" title="<? echo $la['IMAGE_GALLERY'];?>">
	<div class="block float-left img-controls">
		<div class="container">
			<div class="row2">
				<div class="width35"><? echo $la['OBJECT']; ?></div>
				<div class="width65"><select class="width100" id="dialog_image_gallery_object_list"></select></div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['FILTER'];?></div>
				<div class="width65">
					<select class="width100" id="dialog_image_gallery_filter" onchange="switchHistoryReportsDateFilter('img');">
						<option value="0" selected><? echo $la['WHOLE_PERIOD'];?></option>
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
					<input readonly class="inputbox-calendar inputbox width100" id="dialog_image_gallery_date_from" type="text" value=""/>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="dialog_image_gallery_hour_from">
					<? include ("inc/inc_dt.hours.php"); ?>
					</select>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="dialog_image_gallery_minute_from">
					<? include ("inc/inc_dt.minutes.php"); ?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="width35"><? echo $la['TIME_TO']; ?></div>
				<div class="width31">
					<input readonly class="inputbox-calendar inputbox width100" id="dialog_image_gallery_date_to" type="text" value=""/>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="dialog_image_gallery_hour_to">
					<? include ("inc/inc_dt.hours.php"); ?>
					</select>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select class="width100" id="dialog_image_gallery_minute_to">
					<? include ("inc/inc_dt.minutes.php"); ?>
					</select>
				</div>
			</div>
			<div class="row">
				<center>
					<input style="width: 92px; margin-right: 3px;" class="button icon-show icon" type="button" value="<? echo $la['SHOW']; ?>" onclick="imgFilter();"/>
					<input style="width: 92px;" class="button icon-close icon" type="button" value="<? echo $la['DELETE_ALL']; ?>" onclick="imgDeleteAll();"/>
				</center>
			</div>
			
			<table id="image_gallery_list_grid"></table>
			<div id="image_gallery_list_grid_pager"></div>
		</div>
	</div>
	<div class="block float-left img-content">
		<div class="container last">
			<div class="row">
				<div id="image_gallery_img"></div>
			</div>
			<div id="image_gallery_img_data"></div>
		</div>
	</div>
</div>