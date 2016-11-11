<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NovedadController extends \BaseController {

    public function getVehiculos() {
        $data = Input::all();
        $sql = "SELECT gr.gr_id, og.group_name, guo.object_id, guo.imei, gob.name, gob.plate_number, dr.driver_name
                FROM gs_gruposrutas gr 
                JOIN gs_user_object_groups og ON og.group_id = gr.group_id
                JOIN gs_user_objects guo ON guo.group_id = og.group_id
                JOIN gs_objects gob ON gob.imei = guo.imei
                JOIN gs_user_object_drivers dr ON dr.driver_id = guo.driver_id
                WHERE gr.area_id = " . $data["area_id"] .
                " and gr.route_id = " . $data["ruta_id"] .
                " ;";

        $resultados = DB::select($sql);
        return Response::json(array('vehiculos' => $resultados));
    }

    public function getNovedades() {
        $data = Input::all();
        $sql = "select * from novedades where user_id = " . $data["user_id"];
        $novedades = DB::select($sql);
        return Response::json(array('novedades' => $novedades));
    }

    public function postNovedadesavehiculo() {
        $data = Input::all();
        //echo($data["novedades_list"][0]["descripcion"]);
        $sql = "";
        for ($i = 0; $i < count($data["novedades_list"]); $i++) {
            $sql = "insert into registro_novedades(vehiculo_id, despachador_id, novedad_id, fecha_registro) values ("
                    . "" . $data["vehiculo_id"]
                    . "," . $data["despachador_id"]
                    . "," . $data["novedades_list"][$i]["novedad_id"]
                    . ", (select now())"
                    . ");";
            try {
                DB::beginTransaction();
                DB::insert($sql);
                DB::commit();
                if($i == intval(count($data["novedades_list"])-1)){
                   return Response::json(array('success' => true, 'mensaje' => "Las novedades han sido registradas con éxito."));   
                }                
            } catch (Exception $e) {
                DB::rollback();
                return Response::json(array('mensaje' => "No se pueden registrar la(s) novedad(es). Contacte al administrador de sistema. " . $e, 'error' => true));
            }
        }
    }

}
