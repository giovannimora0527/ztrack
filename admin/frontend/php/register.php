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

function limpiarString($texto)
{
      $textoLimpio = ereg_replace("[^A-Za-z0-9]", "", $texto);								
      return $textoLimpio;
}

//Captura de datos del formulario registro
//Sanitize the POST values
$email = strtolower($_GET['email']);
$empresa = strtoupper($_GET['empresa']);
$userzmod = limpiarString(strtoupper($_GET['empresa']));
$username = strtolower($_GET['username']);
$pass1 = $_GET['pass1'];
$active = "true";
$isregister = false;

// EL usuario esta registrado en el sistema por el username
$query = "SELECT * FROM gs_users WHERE username = '$username';";
$consulta = mysql_query($query);
$json = array();
if ($consulta) {
    if (mysql_num_rows($consulta) > 0) {
        $data = array('success' => 'false', 'value' => "El nombre de usuario ya se encuentra registrado. Verifique el nombre de la empresa o el nombre del usuario.");
        array_push($json, $data);
        $isregister = true;
        $jsonstring = json_encode($json);
        echo $jsonstring;
        return;
    }
}

// EL usuario esta registrado en el sistema por el email
$query = "SELECT * FROM gs_users WHERE email = '$email';";
$consulta = mysql_query($query);
$json = array();
if ($consulta) {
    if (mysql_num_rows($consulta) > 0) {
        $data = array('success' => 'false', 'value' => "El email ya se encuentra registrado. Intente con otro correo nuevamente.");
        array_push($json, $data);
        $isregister = true;
        $jsonstring = json_encode($json);
        echo $jsonstring;
        return;
    }
}

// EL usuario no esta registrado en el sistema
if ($isregister == false) {
    $date_red = date("Y-m-d");
    $nombrezmodocolombia = "zmodo_" . $userzmod;
    $privilegies = '{"type":"super_admin"}';
    $privilegies = (string) ($privilegies);
    $pass2 = "ztrack1234";
    $pass_encrypted = md5($pass1);
    $pass_encrypted2 = md5($pass2);
    $emailcorporativo = "ztrackregistro@zmodocolombia.com";
    $qry = "INSERT INTO gs_users (name, username, password, email, active, dt_reg, privileges, obj_edit, profile_id) VALUES("
            . "'$empresa','$username', '$pass_encrypted', '$email','$active','$date_red','$privilegies','true', 1);";
    
    $result = mysql_query($qry);
    $json = array();
    //Encontrar el id del usuario que se registro para poderlo asociar con la cuenta administrador de zmodocolombia
    $query = "select id from gs_users where username = '" . $username . "';";
    $resultado = mysql_query($query);
    if (isset($resultado) && mysql_num_rows($resultado) > 0) {
        $empresa_data = mysql_fetch_assoc($resultado);
        $empresa_id = $empresa_data["id"];       
    }   
    //Guardando el registro de la empresa para zmodocolombia
    $qry2 = "INSERT INTO gs_users (name, username, password, email, active, dt_reg, privileges, profile_id, empresa_id) VALUES('$empresa','$nombrezmodocolombia', '$pass_encrypted2', '$emailcorporativo','$active','$date_red','$privilegies', '2', '$empresa_id');";    
  
    $result2 = mysql_query($qry2);
    if (isset($result2)) {
        $data = array('success' => 'true', 'value' => "Usuario se registró con éxito. Ya puede iniciar sesión.");
        $asunto = "Una empresa nueva en ZTrack";
        $msjHTML = "<html> 
                       <head> 
                           <title>Una empresa se registro a ZTrack</title> 
                           </head> 
                       <body> 
                          <h1>Se ha registrado con éxito a la plataforma ZTrack</h1> 
                        <p> 
                        <b>Su usuario es:  " . $nombrezmodocolombia . " <br>
                           Su contrasena es: " . $pass2 . "<br>                           
                        </p> 
                            </body> 
                        </html> "
                . ""
                . "";
        enviarMsj("zmodocolombia@zmodo.com", "Bienvenida", $emailcorporativo, $empresa, $asunto, $msjHTML);
    }
    //despues de registrar los dos usuarios envio el msj al usuario de la empresa el msj de confirmacion al correo
    if (isset($result)) {
        $data = array('success' => 'true', 'value' => "Usuario se registró con éxito. Ya puede iniciar sesión.");

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
        enviarMsj("zmodocolombia@zmodo.com", "Bienvenida", $email, $empresa, $asunto, $msjHTML);
    } else {
        $data = array('success' => 'false', 'value' => "Usuario NO se registró con éxito. Verifique los datos.");
        array_push($json, $data);
    }
    
    
    $jsonstring = json_encode($json);
    echo $jsonstring;
}
    