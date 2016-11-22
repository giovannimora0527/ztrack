<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConductorController extends \BaseController {
   
    public function getConductores(){
      //validar primero que el conductor no se encuentre asginado previamente a un vehiculo
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
        try {
            DB::beginTransaction();
            DB::insert($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }
    
    
    
    public function postUpdateconductor(){
        $data = Input::all();
        $sql = "update gs_user_object_drivers set "
                . "driver_name = '" . strtoupper($data["driver_name"])
                . "', driver_address = '" . strtoupper($data["driver_address"])
                . "', driver_phone = '" . $data["driver_phone"]
                . "', driver_email = '" . $data["driver_email"]
                . "', driver_desc = '" . $data["driver_desc"]
                . "' where driver_id = " .$data["driver_id"];         
       
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "Los datos del conductor identificado con c.c N° " . $data["driver_idn"] . " se ha actualizado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }
    
    public function postDeleteconductor(){
      $conductor_id = Input::get("conductor_id");
      //Buscar si el conductor se encuentra asignado a un vehiculo. Si es asi no se puede eliminar
      $sql = "select object_id from gs_user_objects where driver_id = " . $conductor_id;
      $result = DB::select($sql);
      if(count($result)>0){
          return Response::json(array('success' => false, 'mensaje' => "El registro se encuentra asociado a un vehículo. Primero elimine la asociación con el vehículo y después continue con la operación."));        
      }
      
      $sql = "delete from gs_user_object_drivers where driver_id = " .$conductor_id;
      try {
            DB::beginTransaction();
            DB::delete($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha eliminado con éxito."));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede eliminar el registro. " . $e, 'error' => true));
        }
        
    }
    
    
}