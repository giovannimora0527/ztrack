//$( document ).ready(function() {
//	setTimeout(function(){ connectLoad(); }, 100);
//});

var la = [];

function connectLoad()
{
	loadLanguage();
	// set language selectbox
	var cookie = getCookie("gs_language");
	if (cookie!=null && cookie!="")
  	{
		document.getElementById("system_language").value = cookie;
	}
}

function connectServer()
{
	var server = document.getElementById("server").value;
	window.open (server+'/mobile','_self',false);
}

function connectLogin()
{	
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;
	var remember_me = document.getElementById("remember_me").checked;
	
	if ((username == '') || (password == ''))
	{
		return;
	}
	
	var data = {
		cmd: 'login',
		username: username,
		password: password,
		remember_me: remember_me,
		mobile: 'true'
	};
	    
	jQuery.ajax({
	    type: "POST",
	    url: "../func/fn_connect.php",
	    data: data,
	    dataType: 'json',
	    success: function(result)
	    {
		if (result.cmd == 'login_tracking')
		{
			window.open ('tracking.php','_self',false);
		}
		else if (result.cmd == 'login_cpanel')
		{
			window.open ('tracking.php','_self',false);
		}
		else if (result.cmd == 'login_http')
		{
			window.open (result.url,'_self',false);
		}
		else if (result.cmd == 'msg')
		{
			bootbox.alert(result.msg);
		}
	    }
	});
}

function connectLogout(){
	var data = {
		cmd: 'logout'
	};
	    
	jQuery.ajax({
		type: "POST",
		url: "../func/fn_connect.php",
		data: data,
		success: function(result)
		{
			window.open (result+'/mobile','_self',false);
		}
	});
}