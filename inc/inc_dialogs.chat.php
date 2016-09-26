<div id="dialog_chat" title="<? echo $la['CHAT'];?>">
	<div class="block float-left">
		<div class="container">
			<div class="row">
				<div class="width70">					
					<input id="chat_object_list_search" class="inputbox-search" type="text" value="" placeholder="<? echo $la['SEARCH']; ?>" maxlength="25">
				</div>
				<div class="float-right">
					<a href="#" onclick="chatReloadData();">
						<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
							<img src="theme/images/refresh2.png" border="0"/>
						</div>
					</a>
					<a href="#" onclick="chatDeleteAllMsgs();">
						<div class="panel-button"  title="<? echo $la['DELETE_ALL_SELECTED_OBJECT_MESSAGES']; ?>">
							<img src="theme/images/trash.png" border="0"/>
						</div>
					</a>
				</div>
			</div>
			<table id="chat_object_list_grid"></table>
		</div>
	</div>
	
	<div class="chat-msgs-block">
		<div id="chat_msgs_dt"></div>
		<div id="chat_msgs">
			<div id="chat_msgs_text"></div>
			<div class="chat-msg-status" id="chat_msg_status"></div>
		</div>
	</div>

	<div class="chat-msg-block" >
		<div class="text-input">
			<input id="chat_msg" class="inputbox" type="text" value="" maxlength="500" onkeydown="if (event.keyCode == 13) chatSend();" placeholder="<? echo $la['TYPE_A_MESSAGE']; ?>" disabled>
		</div>
		<div class="send-btn">
			<input id="chat_msg_send" type="button" class="button icon-create icon send-message" value="<? echo $la['SEND']; ?>" onclick="chatSend();" disabled>
		</div>
	</div>
</div>