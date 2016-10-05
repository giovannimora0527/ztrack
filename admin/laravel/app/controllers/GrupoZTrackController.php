<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GrupoZTrackController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('serviceAuth', array('only' =>
            array('postCreate', 'postUpdate', 'getDestroy')));
    }

    public function getGruposrutasbyid() {
        $user_id = Input::get('user_id');
        $sql = "select gr.gr_id, a.group_name area_name, c.route_name , b.group_name, gr.fechaini, gr.fechafin, gr.area_id, gr.route_id from gs_gruposrutas gr "
                . "join gs_user_places_groups a ON (a.group_id = gr.area_id) "
                . "join gs_user_object_groups b ON (b.group_id = gr.group_id) "
                . "join gs_user_routes c ON (c.route_id = gr.route_id) "
                . "where gr.user_id = " . $user_id
                . " order by gr.fechaini;";
        $groupsroutes = DB::connection('gs')->select($sql);
        return Response::json(array('gruposrutas' => $groupsroutes));
    }

//Metodo que sirve para guardar el registro de asignacion de un grupo a una ruta en un intervalo de fecha
    public function getGruposrutas() {
        $user_id = Input::get("user_id");
        $area_id = Input::get("area_id");
        $data = Input::all();
        $fechaini = substr(str_replace('T', ' ', $data['fechaini']), 0, 10);
        $fechafin = substr(str_replace('T', ' ', $data['fechafin']), 0, 10);
        $grupo_id = $data['group_id'];
        $ruta_id = $data['route_id'];
        $sql = "select * from gs_gruposrutas where user_id = " . $user_id;
        $result = DB::select($sql);
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]->group_id == $data['group_id']) {
                    return Response::json(array('success' => false, 'mensaje' => "El registro se encuentra asignado a una ruta previa. Intente de nuevo."));
                }
            }
        }

        $sql = "insert into gs_gruposrutas (user_id, area_id, route_id, group_id, fechaini, fechafin) values(" . intval($user_id) . "," . intval($area_id) . ","
                . intval($ruta_id) . "," . intval($grupo_id) . ",'" . $fechaini . "','" . $fechafin . "')";
        try {
            DB::beginTransaction();
            DB::insert($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " + $e, 'error' => true));
        }
    }

    public function postEliminar() {
        $grId = Input::get("grId");        
        date_default_timezone_set('America/Bogota');
        $time = time();
        $fecharegistro = date("Y-m-d H:i:s", $time);
        $sql = "select * from gs_gruposrutas where gr_id = " . $grId . ";";
        $result = DB::select($sql);
        if (count($result) > 0) {
            $sql = "insert into gs_history_gr (fechaborrado, user_id, area_id, route_id, group_id, fechaini, fechafin) "
                    . "values ('" . $fecharegistro 
                    ."', " . $result[0]->user_id
                    .", " . $result[0]->area_id
                    .", " . $result[0]->route_id
                    .", " . $result[0]->group_id
                    .", '" . $result[0]->fechaini
                    ."', '" . $result[0]->fechafin
                    . "');";
        }        
        DB::insert($sql); // Guardo el registro en la tabla de historial       

        $sql = "delete from gs_gruposrutas where gr_id = " . $grId . ";";
        try {
            DB::beginTransaction();
            DB::connection('gs')->delete($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha eliminado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede eliminar el registro. " + $e, 'error' => true));
        }
    }

    public function postEditargruporuta() {
        $data = Input::all();
        $hayfechaini = false;
        $sql = "update gs_gruposrutas set ";
        $count = 0;
        if (isset($data["route_id"])) {
            $hayfechaini = true;
            if ($count == 0) {
                $sql .= "route_id = " . $data["route_id"];
                $count++;
            } else {
                $sql .= ", route_id = " . $data["route_id"];
                $count++;
            }
        }
        if (isset($data["fechaini"])) {
            if ($count == 0) {
                $sql .= "fechaini = '" . substr(str_replace('T', ' ', $data['fechaini']), 0, 10) . "'";
                $count++;
            } else {
                $sql .= ", fechaini = '" . substr(str_replace('T', ' ', $data['fechaini']), 0, 10) . "'";
                $count++;
            }
        }
        if (isset($data["fechafin"])) {
            if ($count == 0) {
                $sql .= "fechafin = '" . substr(str_replace('T', ' ', $data['fechafin']), 0, 10) . "'";
                $count++;
            } else {
                $sql .= ", fechafin = '" . substr(str_replace('T', ' ', $data['fechafin']), 0, 10) . "'";
                $count++;
            }
        }
        $sql .= " where gr_id = " . $data["group_id"] . ";";

        try {
            DB::beginTransaction();
            DB::connection('gs')->update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha actualizado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede actualizar el registro. " + $e, 'error' => true));
        }
    }

    public function getGruporutabygrupoid() {
        $grupo_id = Input::get('grId');
        return Response::json(array('gruporuta' => GruposZTrack::where('gr_id', '=', $grupo_id)->get()));
    }

    public function getVehiculos() {
        $user_id = Input::get("user_id");
        $sql = "select gu.object_id vehiculo_id, gu.imei, go.name, go.vin from gs_objects as go, gs_user_objects as gu
               where gu.imei=go.imei and gu.user_id=" . $user_id . " order by go.vin;";
        try {
            DB::beginTransaction();
            $vehiculos = DB::select($sql);
            DB::commit();
            return Response::json(array('vehiculos' => $vehiculos));
        } catch (Exception $e) {
            return Response::json(array('error' => "No tiene vehiculos registrados en el sistema. " + $e, 'error' => true));
        }
    }

    public function postAsignacion() {
        $data = Input::all();
        $sql = "update gs_user_objects set group_id = " . $data["group_id"] . " where object_id = " . $data["vehiculo_id"];
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => "true", 'value' => "Registro guardado con exito."));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se pudo guardar el registro. " + $e, 'error' => true));
        }
    }

    public function getAsignaciones() {
        $data = Input::all();
        $sql = "SELECT gob.object_id, gob.imei, gu.name, gu.vin, gu.plate_number, a.group_name, gd.driver_phone, gd.driver_address,
                CASE 
                 WHEN gd.driver_name IS NULL THEN 'Sin Conductor'
                  ELSE gd.driver_name 
                 END AS driver_name
                FROM gs_user_objects gob 
                JOIN gs_objects gu ON gu.imei = gob.imei
                JOIN gs_user_object_groups a ON a.group_id =  gob.group_id
                LEFT JOIN gs_user_object_drivers gd ON gd.driver_id = gob.driver_id 
                WHERE gob.user_id = " . $data["user_id"]
                . " AND gob.group_id <> 0 
                ORDER BY gu.vin asc;";
        try {
            DB::beginTransaction();
            $asignaciones = DB::select($sql);
            DB::commit();
            if (count($asignaciones) > 0) {
                return Response::json(array('success' => true, 'asignaciones' => $asignaciones));
            } else {
                return Response::json(array('success' => false));
            }
        } catch (Exception $e) {
            return Response::json(array('errormsj' => "No hay resultados disponibles. " + $e, 'error' => true));
        }
    }

    public function postEliminarasignacion() {
        $data = Input::all();
        $sql = "update gs_user_objects set group_id = 0" . " where object_id = " . $data["vehiculo_id"];
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha desagrupado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede desagrupar el registro. " + $e, 'error' => true));
        }
    }

    public function getConductores() {
        $user_id = Input::get("user_id");
        $sql = "select driver_id, driver_name, driver_address, driver_phone, driver_email "
                . "FROM gs_user_object_drivers "
                . "WHERE user_id = " . $user_id
                . " ORDER BY driver_name;";
        try {
            DB::beginTransaction();
            $conductores = DB::select($sql);
            DB::commit();
            return Response::json(array('conductores' => $conductores));
        } catch (Exception $e) {
            return Response::json(array('error' => "No tiene conductores registrados. Agregue conductores para poder asignar. " + $e, 'error' => true));
        }
    }

    public function getAsignacionconductor() {
        $data = Input::all();
        $sql = "select object_id, driver_id 
                from  gs_user_objects 
                where  user_id = 1;";
        $result = DB::select($sql);
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]->driver_id == $data["conductor_id"]) {
                    return Response::json(array('warning' => "true", 'value' => "El conductor ya tiene un vehiculo asignado"));
                }
            }
        }
        //Confirmamos que el vehiculo no tenga una asignacion ya hecha anteriormente, si es asi, obligamos al usuario a desasignarla
        $sql = "select object_id, driver_id from gs_user_objects where object_id = " . $data["vehiculo_id"];
        $result = DB::select($sql);
        if (isset($result)) {
            if ($result[0]->driver_id == $data["conductor_id"]) {
                return Response::json(array('warning' => "true", 'value' => "Debe seleccionar otro vehiculo diferente al que ya esta asignado. Intente de nuevo."));
            }
            if ($result[0]->driver_id == 0) {
                
            }
            if ($result[0]->driver_id == 0) {
                $sql = "update gs_user_objects set driver_id = " . $data["conductor_id"] . " where object_id = " . $data["vehiculo_id"] . "";
                try {
                    DB::beginTransaction();
                    DB::update($sql);
                    DB::commit();
                    return Response::json(array('success' => "true", 'value' => "Registro guardado con exito."));
                } catch (Exception $e) {
                    return Response::json(array('mensaje' => "No se pudo guardar el registro. " + $e, 'error' => true));
                }
            } else {
                return Response::json(array('warning' => "true", 'value' => "El vehiculo ya tiene un conductor asignado. Para continuar debe desasociar el conductor al vehiculo."));
            }
        }
    }

    public function getAsignacionconductores() {
        $user_id = Input::get("user_id");
        $sql = "SELECT gu.object_id, gu.driver_id, gd.driver_name, gd.driver_address, gd.driver_phone, gd.driver_email, b.vin, b.plate_number
               FROM gs_user_objects gu 
               JOIN gs_user_object_drivers gd ON gu.driver_id = gd.driver_id               
               JOIN gs_objects b ON gu.imei = b.imei
               WHERE gu.user_id = " . $user_id
                . " ORDER BY b.vin asc;"
        ;
        $asignaciones = DB::select($sql);
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('asignaciones' => $asignaciones));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se pudo obtener el registro. " + $e, 'error' => true));
        }
    }

    public function getUpdateinformacionconductores() {
        $data = Input::all();
        //Confirmamos que el vehiculo no tenga una asignacion ya hecha anteriormente, si es asi, obligamos al usuario a desasignarla
        $sql = "update gs_user_objects set driver_id = 0 where driver_id = " . $data["driver_id"] . "";
        DB::update($sql);

        $sql = "select object_id, driver_id from gs_user_objects where object_id = " . $data["vehiculo_id"];
        $result = DB::select($sql);
        if (isset($result)) {
            if ($result[0]->driver_id == $data["driver_id"]) {
                return Response::json(array('warning' => "true", 'value' => "Debe seleccionar otro vehiculo diferente al que ya esta asignado. Intente de nuevo."));
            }
            if ($result[0]->driver_id == 0) {
                $sql = "update gs_user_objects set driver_id = " . $data["driver_id"] . " where object_id = " . $data["vehiculo_id"] . "";
                try {
                    DB::beginTransaction();
                    DB::update($sql);
                    DB::commit();
                    return Response::json(array('success' => "true", 'value' => "Registro guardado con exito."));
                } catch (Exception $e) {
                    return Response::json(array('mensaje' => "No se pudo guardar el registro. " + $e, 'error' => true));
                }
            } else {
                return Response::json(array('warning' => "true", 'value' => "El vehiculo ya tiene un conductor asignado. Para continuar debe desasociar el conductor al vehiculo."));
            }
        }
    }

}
