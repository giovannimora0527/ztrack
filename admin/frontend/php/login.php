<?php
header("Content-Type: application/json;charset=utf-8");
//Start session
session_start();

//Include database connection details
require_once('connection.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Function to sanitize values received from the form. Prevents SQL injection
function clean($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
}

//Sanitize the POST values
$username = clean($_POST['nombre']);
$password = clean($_POST['password']);


//Input Validations
if ($username == '') {
    $errmsg_arr[] = 'Nombre de usuario vacio';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Campo password vacio';
    $errflag = true;
}

//If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: ../login.html");
    exit();
}


$qry = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysql_query($qry);

$json = array();
if ($result) {
    if (mysql_num_rows($result) > 0) {
        //Login Satisfactorio
        session_regenerate_id();
        $member = mysql_fetch_assoc($result);
        $_SESSION['SESS_MEMBER_ID'] = $member['id'];
        $_SESSION['SESS_FIRST_NAME'] = $member['empresa'];
        session_write_close();        
        $data = array('success' => true, 'value' => 'Bienvenido');
        array_push($json, $data);
    } else {
        //Login falló
        $errmsg_arr[] = 'user name and password not found';
        $errflag = true;
        if ($errflag) {
            $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
            session_write_close();
            $data = array('success' => 'false', 'value' => "Fallo inicio de Sesión");
            array_push($json, $data);
        }
    }
  $jsonstring = json_encode($json);
  echo $jsonstring;  
} else {    
    return null;
}
