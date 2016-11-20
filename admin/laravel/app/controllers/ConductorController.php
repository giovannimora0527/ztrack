<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConductorController extends \BaseController {
   
    public function getConductores(){
       $user_id = Input::get("user_id"); 
       $conductores = Conductor::where('user_id', '=', $user_id)->get();
       return Response::json(array('conductores' => $conductores));       
    }
    
    public function postSaveconductor(){
        $data = Input::all();
        $sql = "select driver_id from gs_user_object_drivers where driver_idn = '" . $data["identificacion"] . "';";        
        $result = DB::select($sql);        
        if(count($result) > 0){
           return Response::json(array('mensaje' => 'El conductor ya se encuentra en la base de datos con el número del DNI. Intente de nuevo', 'success' => false));         
        }
        
        $sql = "insert into gs_user_object_drivers (driver_name, user_id, driver_idn, driver_address, driver_phone, driver_email, driver_desc) values("
                . "'" . strtoupper($data["nombres"])
                . "', '" . $data["user_id"]
                . "', '" . $data["identificacion"]
                . "', '" . strtoupper($data["direccion"])
                . "', '" . $data["telefono"]
                . "', '" . $data["email"]
                . "', '" . $data["descripcion"]
                . "');";  
        //echo($sql);
        try {
            DB::beginTransaction();
            DB::insert($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }
    
    
    
}