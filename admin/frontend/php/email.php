<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('PHPMailer/class.phpmailer.php');
require 'PHPMailer/PHPMailerAutoload.php';

function enviarMsj($from, $title, $addressToSend, $nombreusuario, $asunto, $msjHTML) {
    $correo = new PHPMailer();
    $correo->IsSMTP();
//Activaremos la autentificación SMTP el cual se utiliza en la mayoría de casos
    $correo->SMTPAuth = true;
//Especificamos la seguridad de la conexion, puede ser SSL, TLS o lo dejamos en blanco si no sabemos
    $correo->SMTPSecure = 'TLS';
//Especificamos el host del servidor SMTP
    $correo->Host = "s14-chicago.accountservergroup.com";
//Especficiamos el puerto del servidor SMTP
    $correo->Port = 587;
//El usuario del servidor SMTP
    $correo->Username = "ztrackregistro@zmodocolombia.com";
//Contraseña del usuario
    $correo->Password = "zcolombia";
// Timeout para el servidor de correos. Por defecto es valor es '10'
    $correo->Timeout = 30;
// Codificación UTF8. Obligado utilizarlo en aplicaciones en Español
    $correo->CharSet = 'UTF-8';
// Timeout para el servidor de correos. Por defecto es valor es '10'
    $correo->Timeout = 30;

    //Usamos el SetFrom para decirle al script quien envia el correo
    $correo->SetFrom($from, $title);
    //Usamos el AddAddress para agregar un destinatario
    $correo->AddAddress($addressToSend, $nombreusuario);
    $correo->Subject = $asunto;
    $correo->MsgHTML($msjHTML);

    //Enviamos el correo       
    if (!$correo->Send()) {       
        return false;
    } else {       
        return true;
    }
}


