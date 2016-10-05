<?php

header("Content-Type: application/json;charset=utf-8");
//Start session
session_start();

//Include database connection details
require_once('connection.php');

require 'email.php';
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
$email = strtolower($_GET['email']);
$empresa = strtoupper($_GET['empresa']);
$username = strtolower($_GET['username']);
$pass1 = $_GET['pass1'];
$active = "true";

$query = "SELECT * FROM gs_users WHERE username = '$username';";
$consulta = mysql_query($query);
$json = array();
if ($consulta) {
    if (mysql_num_rows($consulta) > 0) {
        $data = array('success' => 'false', 'value' => "El usuario ya se encuentra registrado. Verifique el nombre de la empresa o el nombre del usuario.");
        array_push($json, $data);
    } else { // EL usuario no esta registrado en el sistema
        $date_red = date("Y-m-d");
        $privilegies = '{"type":"super_admin"}';
        $privilegies = (string)($privilegies);
        $pass_encrypted = md5($pass1);
        $qry = "INSERT INTO gs_users (name, username, password, email, active, dt_reg, privileges) VALUES('$empresa','$username', '$pass_encrypted', '$email','$active','$date_red','$privilegies');";        
        
        $result = mysql_query($qry);
        $json = array();
        if (isset($result)) {
            $data = array('success' => 'true', 'value' => "Usuario se registró con éxito. Ya puede iniciar sesión.");
            $correo = false;
            $asunto = "Bienvenida a ZTrack";
            $msjHTML = "<html> 
                       <head> 
                           <title>Gracias por registrarse a ZTrack</title> 
                           </head> 
                       <body> 
                          <h1>Se ha registrado con éxito a la plataforma ZTrack</h1> 
                        <p> 
                        <b>Su usuario es:  " . $username . " <br>
                           Su contrasena es: " . $pass1 . "<br>
                           Gracias por preferirnos y disfrute de nuestra plataforma.
                        </p> 
                            </body> 
                        </html> "
                    . ""
                    . "";
            array_push($json, $data);            
            $correo = enviarMsj("zmodocolombia@zmodo.com", "Bienvenida", $email, $empresa, $asunto, $msjHTML);            
        } else {
            $data = array('success' => 'false', 'value' => "Usuario NO se registró con éxito. Verifique los datos.");
            array_push($json, $data);
        }
    }
}

$jsonstring = json_encode($json);
echo $jsonstring;




