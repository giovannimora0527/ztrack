<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function encriptar($cadena) {
    $key = '';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
    $encrypted = password_hash($cadena, PASSWORD_DEFAULT);
    return $encrypted; //Devuelve el string encriptado
}

function desencriptar($cadena) {
    $key = '';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
    $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    echo($decrypted);
    return $decrypted;  //Devuelve el string desencriptado
}
