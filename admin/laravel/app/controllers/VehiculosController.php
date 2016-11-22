<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VehiculosController extends \BaseController {
    
    public function getvehiculos(){
        $data = Input::all();
        //hacer el join con gs_objects para traer la demas informacion
        $sql = "select * from gs_user_objects where user_id = " .$data["user_id"];
        $vehiculos = DB::select($sql);
        return Response::json(array('conductores' => $vehiculos));       
    }
    
    
    public function postSavevehiculo(){
       $data = Input::all();
       //validar que el imei no se encuentre registrado
       //Despues validar que el conductor no este asignado previamente 
    }
    
    public function postUpdatevehiculo(){
        
    }
    
    public function postDeletevehiculo(){
        
    }
}