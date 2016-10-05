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
    $str = trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
}

//Sanitize the POST values
$userId = clean($_POST['userId']);

$qry = "SELECT * FROM gs_users WHERE id='$userId';";
$result = mysql_query($qry);
$json = array();

if ($result) {
    if (mysql_num_rows($result) > 0) {
        $empresa = mysql_fetch_assoc($result);
        $data = array('success' => true, 'usuario' => $empresa['username'], 'password' => $empresa['password']);
        array_push($json, $data);
    } else {
        $data = array('success' => 'false', 'value' => "Algo pasó");
        array_push($json, $data);
    }
    $jsonstring = json_encode($json);
    echo($jsonstring);    
} else {
    return null;
}

