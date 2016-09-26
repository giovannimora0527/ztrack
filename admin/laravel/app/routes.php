<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::controller('captcha', 'CaptchaController');
//Countries Implementation
Route::controller('paises', 'PaisController');
//Departamentos
Route::controller('departamentos', 'DepartamentoController');
//Ciudades
Route::controller('ciudades', 'CiudadController');
//Flag controller
Route::controller('flags', 'FlagController');
//User Controller
Route::controller('users', 'UserController');
//ProyectoTipo controller
Route::controller('proyectotipo', 'ProyectoTipoController');
//Proyecto controller
Route::controller('proyecto', 'ProyectoController');
//Session Controller
Route::controller('session', 'SessionController');

Route::group(array('before' => 'authenticationFilter'), function() {

    /* En esta sección van todas las funciones que requieren que el usuario se encuentre autenticado */
});

App::missing(function($exception) {
    return Response::json(array('mensaje' => array('Error' => array('Página no encontrada'))), 404);
});


