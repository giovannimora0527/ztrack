//#################################################
// VARS
//#################################################

// language array/vars
var la = [];

//#################################################
// END VARS
//#################################################

//#################################################
// DIALOGS AND TABS
//#################################################

$(function (){
	$("#tabs_connect").tabs();
});

//#################################################
// END DIALOGS AND TABS
//#################################################

function getUrlVars()
{
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function connectLoad()
{
	loadLanguage();
	
	// set language selectbox
	var cookie = getCookie("gs_language");
	if (cookie!=null && cookie!="")
  	{
		document.getElementById("system_language").value = cookie;
	}
	
	if (getUrlVars()['op'] == "reg")
	{
		document.getElementById('tabs_connect_reg_tab').click();
		document.getElementById("creg_email").focus();
	}
	else
	{
		document.getElementById("username").focus();
	}
}

function connectServer()
{
	var server = document.getElementById("server").value;
	window.open (server,'_self',false);
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
		mobile: 'false'
	};
	    
	jQuery.ajax({
	    type: "POST",
	    url: "func/fn_connect.php",
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
			window.open ('cpanel.php','_self',false);
		}
		else if (result.cmd == 'login_http')
		{
			window.open (result.url,'_self',false);
		}
		else if (result.cmd == 'msg')
		{
			alert(result.msg);
		}
	    }
	});
}

function connectRecover(){
	var email = document.getElementById("crem_email").value;
	var seccode = document.getElementById("crem_seccode").value;
	
	if(!isEmailValid(email))
	{
		alert(la['THIS_EMAIL_IS_NOT_VALID']);
		return;
	}
	
	var data = {
		cmd: 'recover',
		email: email,
		seccode: seccode
	};
	
	jQuery.ajax({
		type: "POST",
		url: "func/fn_connect.php",
		data: data,
		success: function(result)
		{
		    alert(result);
		}
	});
}

function connectRegister(){
	var email = document.getElementById("creg_email").value;
	var seccode = document.getElementById("creg_seccode").value;
	
	if(!isEmailValid(email))
	{
		alert(la['THIS_EMAIL_IS_NOT_VALID']);
		return;
	}
	
	var data = {
		cmd: 'register',
		email: email,
		seccode: seccode
	};
	
	jQuery.ajax({
		type: "POST",
		url: "func/fn_connect.php",
		data: data,
		success: function(result)
		{
		    alert(result);
		}
	});
}

function connectLogout(){
	var data = {
		cmd: 'logout'
	};
	    
	jQuery.ajax({
		type: "POST",
		url: "func/fn_connect.php",
		data: data,
		success: function(result)
		{
                    window.close(sessionStorage.getItem('url'));
		}
	});
}