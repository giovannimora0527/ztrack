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

//Captura de datos del formulario registro
//Sanitize the POST values
$username = clean($_POST['nombre']);
$password = clean($_POST['password']);

