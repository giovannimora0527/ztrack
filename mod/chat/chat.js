//#################################################
// VARS
//#################################################

var chatData = new Array();
chatData['imei'] = false;
chatData['first_msg_id'] = false;
chatData['last_msg_id'] = false;
chatData['timezone'] = moment().format('ZZ');

// timers
var timer_chatLoadData;
var timer_chatMsgsDTHide;

//#################################################
// END VARS
//#################################################

var chatMsgsScrollHandler = function(){
	if($(this).scrollTop() == 0){
		if (chatData['first_msg_id'] != false)
		{
			chatLoadMsgs('old');
		}
	}
	
	$('#chat_msgs div').each(function() {
                if ($(this).position().top > 0) {
			var dt = $(this).attr('title');
			if (dt != undefined)
			{
				if (dt.length > 10)
				{
					if (document.getElementById("chat_msgs_dt").style.display == "none")
					{
						document.getElementById("chat_msgs_dt").style.display = "block";
					}
					
					clearTimeout(timer_chatMsgsDTHide);
					timer_chatMsgsDTHide = setTimeout(function() {
						$('#chat_msgs_dt').fadeOut('slow');
					}, 3000);
					
					document.getElementById("chat_msgs_dt").innerHTML = dt.substring(0,10);
					return false;
				}
			}
                }   
        });
}

function load()
{
	$(window).bind('resize', function() {scrollToBottom("chat_msgs");});
	
	$("#chat_msgs").scroll(chatMsgsScrollHandler);
	
        chatSelectObject();
}

function chatClear()
{
	document.getElementById("chat_msgs_dt").style.display = "none";
	document.getElementById("chat_msgs_dt").innerHTML = '';
	document.getElementById("chat_msgs_text").innerHTML = '';
	document.getElementById("chat_msg_status").innerHTML = '';
}

function chatLoadData()
{
	clearTimeout(timer_chatLoadData);
	
	var data = {
		cmd: 'load_chat_data',
		imei: chatData['imei'],
		last_msg_id: chatData['last_msg_id']
	};
	
	$.ajax({
		type: "POST",
		url: "fn_chat.php",
		data: data,
		dataType: 'json',
		error: function(statusCode, errorThrown) {
			timer_chatLoadData = setTimeout("chatLoadData();", gsValues['chat_refresh'] * 1000);
		},
		success: function(result)
		{
			// set message count
			chatData['msg_count'] = result['msg_count'];
			
			// set last message delivery status
			if (result['last_msg_status'] != false)
			{
				chatUpdateMsgDeliveryStatus(result['last_msg_status']);
			}
			
			// check for new messages
			var imei = chatData['imei'];
			if (chatData['msg_count'][imei] > 0)
			{
				chatLoadMsgs('new');
			}
			
			timer_chatLoadData = setTimeout("chatLoadData();", 10 * 1000);
		}
	});
}

function chatSend()
{
        var msg = document.getElementById("chat_msg").value;
        
        if ((chatData['imei'] != false) && (msg != ''))
        {
		msg = stripHTML(msg);
		msg = strLink(msg);
		
        	var data = {
                        cmd: 'send_msg',
                        imei: chatData['imei'],
                        msg: msg
                };
                
                $.ajax({
			type: "POST",
			url: "fn_chat.php",
			data: data,
			cache: false,
			success: function(result)
			{
                                if (result == 'OK')
                                {
					document.getElementById("chat_msg").value = '';
                                        chatLoadMsgs('new');
                                }
				else
				{
					
				}
			},
			error: function(statusCode, errorThrown) {
                                
			}
		});
        }
}

function chatLoadMsgs(type)
{	
	if (type == 'old')
	{
		var msg_limit = 10;
	}
	else
	{
		var msg_limit = 40;
	}
	
        var data = {
                cmd: 'load_msgs',
		type: type,
                imei: chatData['imei'],
		msg_limit: msg_limit,
		first_msg_id: chatData['first_msg_id'],
		last_msg_id: chatData['last_msg_id']
        };
        
        $.ajax({
                type: "POST",
                url: "fn_chat.php",
                data: data,
		dataType: 'json',
                cache: false,
                success: function(result)
                {
			if (type == 'select')
			{
				chatLoadData();
			}
			
			if (result == '')
			{
				//if (type == 'old')
				//{
				//	//chatData['first_msg_id'] = false;
				//}
				return;
			}
			
			if (type == 'old')
			{
				document.getElementById("chat_msgs").scrollTop = 1;
			}
			
			var msgs_html = '';
			
			for (var id in result)
			{
				id = parseInt(id);
				
				var dt = result[id].dt;
				dt = moment.utc(dt).zone(chatData['timezone']).format('YYYY-MM-DD HH:mm:ss');
				var side = result[id].s;
				var msg = result[id].m;
				var status = result[id].st;
				
				if (type != 'old')
				{
					msgs_html += chatFormatMsg(id, dt, side, msg);
				}
				else
				{
					msgs_html += chatFormatMsg(id, dt, side, msg);
				}
				
				if ((chatData['first_msg_id'] > id) || (chatData['first_msg_id'] == false)) {
					chatData['first_msg_id'] = id;
				}
				
				if ((chatData['last_msg_id'] < id) || (chatData['last_msg_id'] == false)) {
					chatData['last_msg_id'] = id;
				}
			}
			
			if (type != 'old')
			{
				document.getElementById("chat_msgs_text").innerHTML = document.getElementById("chat_msgs_text").innerHTML + msgs_html;
				scrollToBottom("chat_msgs");
				
				var imei = chatData['imei'];
				var id = chatData['last_msg_id'];
				
				// set last message delivery status
				var side = result[id].s;
				var status = result[id].st;
				if (side == 'C') {
					chatUpdateMsgDeliveryStatus(status);
				}
				else
				{
					chatUpdateMsgDeliveryStatus(0);
				}
			}
			else
			{
				document.getElementById("chat_msgs_text").innerHTML = msgs_html + document.getElementById("chat_msgs_text").innerHTML;
			}	
                },
                error: function(statusCode, errorThrown) {
                   	if (type == 'select')
			{
				chatLoadData();
			}     
                }
        });
}

function chatFormatMsg(id, dt, side, msg)
{
	if (side == 'S')
	{
		var msg_class = 'chat-msg-server';
		var msg_dt_class = 'chat-msg-dt-server';
	}
	else
	{
		var msg_class = 'chat-msg-client';
		var msg_dt_class = 'chat-msg-dt-client';
	}
	
	if (dt.substring(0,10) == moment().format("YYYY-MM-DD"))
	{
		dt = dt.substring(11,19);
	}
	
	var time = dt;

	//var time = dt.substring(11,16);
	
	var msg_div = '<div class="chat-msg-container"><div title="'+dt+'" class="'+msg_class+'">'+msg+'<div class="'+msg_dt_class+'">'+time+'</div></div></div>';
	
	return msg_div;
}

function chatUpdateMsgDeliveryStatus(status)
{
	var need_scroll = false;
	if (status == 0)
	{
		document.getElementById("chat_msg_status").innerHTML = '';
	}
	else if (status == 1)
	{
		if(document.getElementById("chat_msg_status").innerHTML == '')
		{
			need_scroll = true;
		}
		document.getElementById("chat_msg_status").innerHTML = 'delivered';
	}
	else if (status == 2)
	{
		if(document.getElementById("chat_msg_status").innerHTML == '')
		{
			need_scroll = true;
		}
		document.getElementById("chat_msg_status").innerHTML = 'seen';
	}
	
	if (need_scroll) {
		scrollToBottom("chat_msgs");
	}
}

function chatSelectObject()
{
	chatClear();
	chatLoadMsgs('select');
}

function scrollToBottom(id)
{
    var obj = document.getElementById(id);
    obj.scrollTop = obj.scrollHeight;
}

function strLink(str) {
    //URLs starting with http://, https://, or ftp://
    var replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    var replacedText = str.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with www. (without // before it, or it'd re-link the ones done above)
    var replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    var replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links
    var replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
    var replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText
}

function stripHTML(str) {
    str=str.replace(/(<\?[a-z]*(\s[^>]*)?\?(>|$)|<!\[[a-z]*\[|\]\]>|<!DOCTYPE[^>]*?(>|$)|<!--[\s\S]*?(-->|$)|<[a-z?!\/]([a-z0-9_:.])*(\s[^>]*)?(>|$))/gi, '');
    return str;
}