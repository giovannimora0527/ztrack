<?php

header("Content-Type: application/json;charset=utf-8");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('connection.php');
require 'email.php';

$email = strtolower($_POST['email']);
$qry = "SELECT id, username FROM gs_users WHERE email='$email';";

$result = mysql_query($qry);
$id = 0;

if ($result) {
    if (mysql_num_rows($result) > 0) {
        $correo = false;
        $member = mysql_fetch_assoc($result);
        $id = $member['id'];
        $nombre = $member['username'];
        $password = generarContrasena();

        $asunto = "Cambio de Contraseña";
        $msjHTML = "<html> 
                       <head> 
                           <title>Prueba de correo</title> 
                           </head> 
                       <body> 
                          <h1>Se ha registrado la nueva contrasena con éxito a la plataforma ZTrack</h1> 
                        <p> 
                        <b>Su usuario es:  " . $nombre . " <br>
                           Su nueva contrasena es: " . $password . "<br>
                           Gracias por preferirnos.
                        </p> 
                            </body> 
                        </html> "
                . ""
                . "";
        $correo = enviarMsj("zmodocolombia@zmodo.com", "Prueba", $email, $nombre, $asunto, $msjHTML);
        $json = array();
        if ($correo) {            
            //Codigo para registrar la nueva contrasena            
            $query = "UPDATE gs_users SET password = '" . md5($password) . "' WHERE id = '" . $id . "';";        
            $result = mysql_query($query);
            if (isset($result)) {
                $data = array('success' => 'true', 'value' => "Cambio de contrasena se hizo con exito. Ya puede iniciar sesión.");
                array_push($json, $data);
            } else {
                $data = array('success' => 'false', 'value' => "NO se registró con éxito la contrasena. Verifique los datos.");
                array_push($json, $data);
            }
        } else {
            $data = array('success' => 'false', 'value' => "NO se pudo enviar el correo. Contacte al administrador del sistema.");
            array_push($json, $data);
        }
        $jsonstring = json_encode($json);
        echo $jsonstring;
    }
} else {
    return null;
}

function generarContrasena() {
    $length = 20;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}




