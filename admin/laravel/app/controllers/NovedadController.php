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
                if ($i == intval(count($data["novedades_list"]) - 1)) {
                    return Response::json(array('success' => true, 'mensaje' => "Las novedades han sido registradas con éxito."));
                }
            } catch (Exception $e) {
                DB::rollback();
                return Response::json(array('mensaje' => "No se pueden registrar la(s) novedad(es). Contacte al administrador de sistema. " . $e, 'error' => true));
            }
        }
    }

    public function getNovedadesavehiculoxfiltro() {
        $data = Input::all();
        $hasfecha = false;
        $hasvehiculo = false;
        if ($data["fecha"] != "") {
            $hasfecha = true;
        }
        if (isset($data["vehiculoid"])) {
            $hasvehiculo = true;
        }
        $countfilter = 0;
        //Filtrar pendiente por el usuario que creo las novedades
        $sql = "SELECT d.nombre, d.apellido, n.descripcion, rn.id, rn.vehiculo_id, gob.name, rn.fecha_registro, 
                 CASE 
                        WHEN rn.estado = 0 THEN 'Pendiente'
                        ELSE 'Solucionado'
                        END AS estado,
                 CASE 
                        WHEN rn.fecha_solucion = '0000-00-00 00:00:00' THEN 'N/R'
                        ELSE rn.fecha_solucion
                        END AS fecha_solucion
                FROM registro_novedades rn
                JOIN gs_info_despachador d ON d.user_id = rn.despachador_id
                JOIN novedades n ON rn.novedad_id = n.novedad_id  
                JOIN gs_user_objects guo ON rn.vehiculo_id = guo.object_id
                JOIN gs_objects gob ON guo.imei = gob.imei
                ";
        if ($hasvehiculo) {
            if ($countfilter == 0) {
                $sql .= " WHERE rn.vehiculo_id = " . $data["vehiculoid"];
                $countfilter++;
            } else {
                $sql .= " AND rn.vehiculo_id = " . $data["vehiculoid"];
                $countfilter++;
            }
        }
        if ($hasfecha) {
            if ($countfilter == 0) {
                $sql .= " WHERE rn.fecha_registro <= '" . $data["fecha"] . " 23:59:59'";
                $countfilter++;
            } else {
                $sql .= " AND rn.fecha_registro <= '" . $data["fecha"] . " 23:59:59'";
                $countfilter++;
            }
        }
        if ($data["active_tab"] == 2) {
            $sql .= " and rn.estado = 0 ";
        }
        $sql .= " and rn.despachador_id = " . $data["user_id"];

        $novedades = DB::select($sql);
        return Response::json(array('novedades' => $novedades));
    }

    public function postSolucionarnovedad() {
        $data = Input::all();
//        registro_novedades
        $sql = "update registro_novedades set "
                . "estado = 1"
                . ", descripcion = '" . strtoupper($data["descripcion"])
                . "', fecha_solucion = (SELECT NOW())"
                . "' where id = " . $data["id"]
        ;
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La novedad ha sido solucionada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede registrar la solución. Contacte al administrador de sistema. " . $e, 'error' => true));
        }
    }

    public function postUpdatenovedad() {
        $data = Input::all();
        $sql = "update registro_novedades set "
                . "novedad_id = " . $data["novedad_id"]
                . ", fecha_registro = '" . $data["fecha"] . " " . $data["hora"] . "'"
                . " where id = " . $data["id"];        
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La novedad ha sido actualizada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede actualizar la novedad. Contacte al administrador de sistema. " . $e, 'error' => true, 'success' => false));
        }
    }
    
    public function getVehiculosnovedades(){
       $data = Input::all();
       $sql = "select distinct guo.object_id, gob.name  "
               . "from registro_novedades rn "
               . "join gs_user_objects guo ON guo.object_id = rn.vehiculo_id "
               . "join gs_objects gob ON gob.imei = guo.imei "
               . "where rn.despachador_id = " .$data["user_id"] 
               . ";"
               ;
       $results =  DB::select($sql);
       return Response::json(array('success' => true, 'vehiculosnovedad' => $results));
    }
    
    
    public function postNovedadnueva(){
        $data = Input::all();
        $sql = "insert into novedades (descripcion, user_id) values ("
                . "'" . strtoupper($data["descripcion"])
                . "', " . $data["user_id"]
                . ");";        
        try {
            DB::beginTransaction();
            DB::insert($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La novedad ha sido ingresada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede ingresar la novedad. Contacte al administrador de sistema. " . $e, 'error' => true, 'success' => false));
        }
    }
    
    public function getNovedadesadmin(){
         $data = Input::all();
         $sql = "select * from novedades where user_id = " . $data["user_id"];
         $result = DB::select($sql);
         return Response::json(array('novedadesregistradas' => $result));
    }
    
    public function postUpdatenovedadadmin(){
        $data = Input::all();
        $sql = "update novedades set "
                . "descripcion = '" . strtoupper($data["descripcion"])
                . "' where novedad_id = " .$data["novedad_id"];
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La novedad ha sido actualizada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede actualizar la novedad. Contacte al administrador de sistema. " . $e, 'error' => true, 'success' => false));
        }
    }
    
    public function postDeletenovedad(){
        $data = Input::all();
        $sql = "delete from novedades where novedad_id = " . $data["novedad_id"];
        try {
            DB::beginTransaction();
            DB::delete($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La novedad ha sido eliminada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede eliminar la novedad. Contacte al administrador de sistema. " . $e, 'error' => true, 'success' => false));
        }
    }
    

}
