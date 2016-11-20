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
//Conductores
Route::controller('conductores', 'ConductorController');
//Grupos
Route::controller('grupos', 'GruposController');
//Rutas
Route::controller('rutas', 'RutasController');
//GruposRutas
Route::controller('gruposrutas', 'GrupoZTrackController');

//Despachos
Route::controller('despachos', 'DespachosController');
//Despachador
Route::controller('despachador', 'DespachadorController');
//Reportes
Route::controller('reportes', 'ReportesController');
//Novedades
Route::controller('novedades', 'NovedadController');

//Pruebas
Route::controller('pruebas', 'PruebasController');

Route::group(array('before' => 'authenticationFilter'), function() {

    /* En esta sección van todas las funciones que requieren que el usuario se encuentre autenticado */
});

App::missing(function($exception) {
    return Response::json(array('mensaje' => array('Error' => array('Página no encontrada'))), 404);
});


