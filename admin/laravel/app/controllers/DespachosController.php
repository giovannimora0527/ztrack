<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DespachosController extends \BaseController {

    public function getGruposxrutaid() {
        $data = Input::all();
        $sql = "select g.group_id, g.group_name 
                from gs_gruposrutas gr
                join gs_user_object_groups g on g.group_id = gr.group_id
                where gr.user_id = " . $data["user_id"] . " and 
                gr.area_id = " . $data["area_id"] . ";";
        try {
            DB::beginTransaction();
            $groups = DB::select($sql);
            DB::commit();
            return Response::json(array('groups' => $groups));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se puede traer la informacion. " . $e, 'error' => true));
        }
    }

    public function getVehiculoxgroupid() {
        $data = Input::all();
        $sql = "SELECT guo.object_id, guo.imei 
                FROM gs_user_objects guo
                JOIN gs_objects gob ON gob.imei = guo.imei
                WHERE guo.user_id = " . $data["user_id"] . " 
                AND guo.group_id = " . $data["group_id"] . ";";
        try {
            DB::beginTransaction();
            $vehiculos = DB::select($sql);
            DB::commit();
            return Response::json(array('vehiculos' => $vehiculos));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se puede traer la informacion. " . $e, 'error' => true));
        }
    }

    function getDespachadoresbyuserid() {
        $user_id = Input::get("user_id");
        $sql = "select id, nombre, apellido from gs_info_despachador where empresa_id = " . $user_id;
        try {
            DB::beginTransaction();
            $despachadores = DB::select($sql);
            DB::commit();
            return Response::json(array('despachadores' => $despachadores));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se puede cargar la información. " . $e, 'error' => true));
        }
    }

    public function getPuntoscontrol() {
        $data = Input::all();
        $sql = "select uz.zone_id, uz.zone_name from gs_user_zones uz where uz.user_id =  " . $data["user_id"]
                . " and uz.group_id = " . $data["area_id"]
                . ";";
        $puntoscontrol = DB::select($sql);
        return Response::json(array('puntoscontrol' => $puntoscontrol));
    }

    public function postSavepuntoscontrolaruta() {
        $data = Input::all();
        $sql = "select * from gs_rutazonas where user_id = " . $data["user_id"];
        $esta = false;
        $result = DB::select($sql);
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                //if (($result[$i]->zone_id == $data["ptocontrol_id"] {
                 if ($result[$i]->zone_id == $data["ptocontrol_id"] && $result[$i]->route_id == $data["route_id"]){
                    $esta = true;
                }
            }
        }
        if (!$esta) {
            date_default_timezone_set('America/Bogota');
            $time = date('Y-m-d H:i:s', time());
            $sqlinsert = "insert into gs_rutazonas(user_id, zone_id, route_id, fecha) values("
                    . "" . $data["user_id"]
                    . ", " . $data["ptocontrol_id"]
                    . ", " . $data["route_id"]
                    . ", '" . $time
                    . "');";
            try {
                DB::beginTransaction();
                DB::insert($sqlinsert);
                DB::commit();
                return Response::json(array('success' => true, 'mensaje' => "El punto de control se agrego con éxito a la ruta."));
            } catch (Exception $e) {
                DB::rollback();
                return Response::json(array('mensaje' => "No se pudo guardar el registro. Intente de nuevo o contacte al administrador del sistema. " . $e, 'error' => true));
            }
        } else {
            //return Response::json(array('success' => false, 'error' => true, 'mensaje' => "No se pudo guardar el registro. El registro se encuentra en la base de datos."));
            return Response::json(array('success' => false, 'error' => true, 'mensaje' => "No se pudo guardar el punto de control. Ya se encuentra asignado a esta ruta."));
        }
    }

    public function getCargarpuntoscontrolaruta() {
        $data = Input::all();
        $sqlselect = "SELECT rz.rz_id, rz.fecha, uz.zone_id, uz.zone_name, ur.route_id, ur.route_name,
                      CASE 
                        WHEN rz.tiempo_zona = '00:00:00' THEN 'Sin Asignar'
                        ELSE rz.tiempo_zona 
                        END AS tiempo_zona
                      FROM gs_rutazonas rz                      
                      JOIN gs_user_zones uz ON uz.zone_id = rz.zone_id
                      JOIN gs_user_routes ur ON ur.route_id = rz.route_id
                      WHERE rz.user_id = " . $data["user_id"] .
                " AND rz.route_id = " . $data["route_id"] .
                "";        
        $ptosdecontrol = DB::select($sqlselect);
        return Response::json(array('ptosdecontrol' => $ptosdecontrol));
    }

    public function postGuardartiempopc() {
        $data = Input::all();
        $sql = "select * from gs_rutazonas where rz_id = " . $data["pc"] . ";";        
        $result = DB::select($sql);
        $tiempos = strtotime($data["tiempo"]);
        $time = date("H:i:s", $tiempos);
        $sql_tiempo = "";
        if (count($result) > 0) {
            $sql_tiempo = "update gs_rutazonas set "
                    . "tiempo_zona = '" . $time
                    . "' where rz_id = " . $data["pc"];
        } else {
            return Response::json(array('mensaje' => "No se pudo guardar el registro. Intente de nuevo o contacte al administrador del sistema. ", 'error' => true));
        }

        try {
            DB::beginTransaction();
            DB::update($sql_tiempo);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El tiempo de recorrio al punto de control se agrego con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se pudo guardar el registro. Intente de nuevo o contacte al administrador del sistema. " . $e, 'error' => true));
        }
    }

    public function getRutazonainfo() {
        $data = Input::all();
        $sql = "select uz.zone_name, ur.route_name,
                CASE 
                  WHEN rz.tiempo_zona IS NULL THEN 'Sin Asignar'
                  ELSE rz.tiempo_zona 
                  END AS tiempopc
                from gs_rutazonas  rz
                join gs_user_zones uz ON uz.zone_id = rz.zone_id
                join gs_user_routes ur ON ur.route_id = rz.route_id
                where rz.rz_id =  " . $data["rz_id"];
        $rutazonainfo = DB::select($sql);
        return Response::json(array('rutazona' => $rutazonainfo[0]));
    }

}
