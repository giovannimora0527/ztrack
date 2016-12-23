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
                    . "where driver_id = " . $data["conductor_id"];
            DB::update($sql_update);
            //se guarda un registro de gs_objects - La fecha active_dt queda con dos años a futuro para su vencimiento
            $fecha = date('Y-m-j');
            $nuevafecha = strtotime('+2 year', strtotime($fecha));
            $nuevafecha = date('Y-m-j', $nuevafecha);
            $insert = "insert into gs_objects (imei, protocol, active, active_dt, name, icon, map_icon, tail_color, tail_points, plate_number, "
                    . "odometer_type, odometer, time_adj, dt_chat) values("
                    . "'" . $data["imei"]
                    . "', 'android' "
                    . ", 'true'"
                    . ", '" . $nuevafecha
                    . "', '" . $data["nombre"]
                    . "', 'img/markers/objects/31.png'"
                    . ", 'arrow'"
                    . ", '#00FF44'"
                    . ", 7"
                    . ", '"
                    . "', 'gps'"
                    . ", 0"
                    . ", -5"
                    . ", '0000-00-00 00:00:00' "
                    . ");";
            DB::insert($insert);
            //Se crea la tabla gs:object_data_imei
            $sql = "CREATE TABLE gs_object_data_" . $data["imei"]
                . "(
                `dt_server` datetime NOT NULL,
                `dt_tracker` datetime NOT NULL,
                `lat` double DEFAULT NULL,
                `lng` double DEFAULT NULL,
                `altitude` double DEFAULT NULL,
                `angle` double DEFAULT NULL,
                `speed` double DEFAULT NULL,
                `params` varchar(2048) COLLATE utf8_bin NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"; 
            DB::insert($sql);            
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }

    public function postActualizarvehiculo() {
       $data = Input::all();
       $sql = "";  
       $actualizado = false;
       
       //SI se cambia el conductor del vehiculo
       if($data["hasFilterDriver"] == 1){
         //Valido si el conductor no se encuentra asignado a un vehiculo previamente
          $query = "select * from gs_user_objects where imei = ".$data["imei"]; 
          $results = DB::select($query);
          $conductor_id = $results[0]->driver_id;
          //obtengo el conductor y lo paso a estado disponible
          $sql = "update gs_user_object_drivers set "
                  . "estado_id = 1"
                  . " where driver_id = " . $conductor_id;
          DB::update($sql);
         //cambio el registro en la tabla user_objects  
         $sql = "update gs_user_objects guo set "
                 . "guo.driver_id = '"  .$data["driver_id"]
                 
                 . "' where guo.imei = '" .$data["imei"] . "';";         
         DB::update($sql);
         //actualizo el estado del nuevo conductor asignado
         $sql = "update gs_user_object_drivers set "
                  . "estado_id = 2"
                  . " where driver_id = " . $data["driver_id"];
          DB::update($sql);
         $actualizado = true;
       }
       $sql = "update gs_objects gob set "
             . "gob.plate_number = '"  .$data["plate_number"] 
             . "', gob.model = '"  .$data["model"]                               
             . "', gob.name = '"  .$data["name"];
       if(isset($data["sim_number"])){
         $sql .= "', gob.sim_number = '"  .$data["sim_number"];  
       }
               
       $sql .= "' where gob.imei = '" .$data["imei"] . "';";        
       DB::update($sql);       
       if($actualizado == true){
         return Response::json(array('success' => true, 'mensaje' => "El registro se ha actualizado correctamente"));  
       }
       else{
           return Response::json(array('success' => false, 'mensaje' => "El registro se ha guardado correctamente"));
       }       
       
    }

    public function postDeletevehiculo() {
        
    }

    public function postFiltrarvehiculo() {
        $data = Input::all();
        $hasFiltros = Input::get("hasFiltros");
        $more_results = false;
        $sql = "select guo.object_id, guo.imei, gob.name, gob.model, gob.sim_number, gob.plate_number, d.driver_name, d.driver_address, 
                       d.driver_phone, GROUP_CONCAT(r.route_name SEPARATOR ', ')  rutas
                from gs_user_objects guo
                join gs_objects gob ON gob.imei = guo.imei
                join gs_user_object_drivers d ON d.driver_id = guo.driver_id
                left join gs_gruposrutas gr ON gr.group_id = guo.group_id
                left join gs_user_routes r ON r.route_id = gr.route_id
                where guo.user_id = " . $data["user_id"];

        $sql_count = "select count(guo.object_id) conteo
                from gs_user_objects guo
                join gs_objects gob ON gob.imei = guo.imei
                join gs_user_object_drivers d ON d.driver_id = guo.driver_id
                left join gs_gruposrutas gr ON gr.group_id = guo.group_id
                left join gs_user_routes r ON r.route_id = gr.route_id
                where guo.user_id = " . $data["user_id"];
        $filtros = "";

        if ($hasFiltros) {
            $count = 0;
            if ($data["placa"] != "") {
                $filtros.= " and gob.plate_number LIKE '%" . $data["placa"] . "%'";
            }
            if ($data["numvehiculo"] != "") {
                $filtros.= " and gob.name LIKE '" . $data["numvehiculo"] . "%'";
            }
            if ($data["conductor"] != "") {
                $filtros.= " and d.driver_id = " . $data["conductor"];
            }
            if ($data["ruta"] != "") {
                $filtros.= " and gr.route_id = " . $data["ruta"];
            }
        }
        $sql = $sql . $filtros;
        $sql_count = $sql_count . $filtros;

        $sql .= " group by guo.imei "
                . "LIMIT " . $data["min"] . "," . $data["max"]
                . ";";
        $results_count = DB::select($sql_count);

        if ($results_count[0]->conteo > $data["max"]) {
            $more_results = true;
        } else {
            $more_results = false;
        }
        try {
            DB::beginTransaction();
            $resultados = DB::select($sql);
            DB::commit();
            if (count($resultados) > 0) {
                return Response::json(array('success' => true, 'mensaje' => 'Datos cargados con éxito', 'vehiculos' => $resultados, 'min' => intval($data["min"]), 'max' => intval($data["max"]) - 1, 'count' => $results_count[0]->conteo,
                            'moreresults' => $more_results));
            } else {
                return Response::json(array('success' => false, 'mensaje' => "No hay resultados disponibles."));
            }
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No hay resultados disponibles. " . $e, 'error' => true));
        }
    }
    
    public function getInfovehiculo(){
        $data = Input::all();
        $sql = "select ob.object_id, ob.imei, obj.name, obj.plate_number, dr.driver_name, dr.driver_phone, dt.hora_llegada "
                . "from gs_user_objects ob "
                . "join gs_objects obj on obj.imei = ob.imei "
                . "join gs_user_object_drivers dr ON dr.driver_id = ob.driver_id "
                . "join gs_despacho_temporal dt ON dt.object_id = ob.object_id "
                . "where ob.object_id = " . $data["vehiculo_id"] . " "
                . "and ob.user_id = " . $data["user_id"] ;
       
        $vehiculo = DB::select($sql);
        return Response::json(array('success' => true, 'vehiculo' => $vehiculo[0]));
        
    }

}
