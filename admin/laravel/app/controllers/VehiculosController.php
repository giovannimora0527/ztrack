<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VehiculosController extends \BaseController {

    public function getVehiculos() {
        $data = Input::all();
        //hacer el join con gs_objects para traer la demas informacion
        $sql = "select * from gs_user_objects where user_id = " . $data["user_id"];
        $vehiculos = DB::select($sql);
        return Response::json(array('vehiculos' => $vehiculos));
    }

    public function postSavevehiculo() {
        $data = Input::all();
        $sql = "select object_id from gs_user_objects where imei = " . $data["imei"];
        $results = DB::select($sql);
        if (count($results) > 0) {
            return Response::json(array('mensaje' => 'El vehiculo ya se encuentra en la base de datos con el número del IMEI ' . $data["imei"] . '. Intente de nuevo', 'success' => false));
        }

        $sql = "insert into gs_user_objects(user_id, imei, group_id, driver_id, trailer_id) values("
                . "" . $data["user_id"]
                . ", " . $data["imei"]
                . ", 0"
                . ", " . $data["conductor_id"]
                . ", 0"
                . ");";

        try {
            DB::beginTransaction();
            DB::insert($sql);
            //Se actualiza el estado del conductor 
            $sql_update = "update gs_user_object_drivers set "
                    . "estado_id = 2 "
                    . "where driver_id = " .$data["conductor_id"];
            DB::update($sql_update);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }       
    }

    public function postUpdatevehiculo() {
        
    }

    public function postDeletevehiculo() {
        
    }

}
