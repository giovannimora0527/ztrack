<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DespachadorController extends \BaseController {

    public function getDespachador() {
        $data = Input::all();
        $sql = "select id, username from gs_users where username = '" . strtolower($data["username"]) . "';";
        $results = DB::select($sql);
        if (count($results) > 0) {
            return Response::json(array('error' => true, 'mensaje' => "El nombre del usuario ya se encuentra registrado en nuestro sistema. Intente con otro nombre de usuario."));
        } else {
            $select = "select name from gs_users where id =  '" . $data["user_id"] . "';";
            $empresa = DB::select($select);
            $empresa_id = $data["user_id"];
            $name = $empresa[0]->name;
            $username = strtolower($data["username"]);
            $pass = $data["password"];
            $pass_encrypted = md5($pass);
            $privilegies = '{"type":"viewer"}';
            $privilegies = (string) ($privilegies);
            $date_red = date("Y-m-d");
            $profile_id = "3";

            $query = "insert into gs_users (active, privileges, manager_id, name, username, password, dt_reg, profile_id, empresa_id) values ('true', '"
                    . $privilegies
                    . "', '" . $data["user_id"]
                    . "', '" . $name
                    . "', '" . $data["username"]
                    . "', '" . $pass_encrypted
                    . "', '" . date("Y-m-d")
                    . "', '" . $profile_id
                    . "', '" . $data["user_id"]
                    . "');"
            ;
            try {
                DB::beginTransaction();
                DB::insert($query);
                DB::commit();
                $select = "select id, empresa_id from gs_users where username = '" . $data["username"] . "';";
                $inforesult = DB::select($select);
                $insertinfo = "insert into gs_info_despachador (user_id, nombre, apellido, direccion, telefono, empresa_id) values ("
                        . "'" . $inforesult[0]->id
                        . "', '" . strtoupper($data["nombre"])
                        . "', '" . strtoupper($data["apellidos"])
                        . "', '" . strtoupper($data["direccion"])
                        . "', '" . $data["telefono"]
                        . "', '" . $inforesult[0]->empresa_id
                        . "');";
                DB::beginTransaction();
                DB::insert($insertinfo);
                DB::commit();
                return Response::json(array('success' => true, 'mensaje' => "El despachador se agrego con éxito."));
            } catch (Exception $e) {
                DB::rollback();
                return Response::json(array('mensaje' => "No se pudo guardar el registro. Intente de nuevo o contacte al administrador del sistema. " . $e, 'error' => true));
            }
        }
    }

    public function getVehiculosparadero() {
        $data = Input::all();
        $sql = "select * from gs_info_despachador where user_id = " . $data["user_id"];
        $data_despachador = DB::select($sql);
        $sql = "select * from gs_despacho_temporal where user_id = " . $data["user_id"];
        $result = DB::select($sql);
        $turno = count($result);
        $turno++;
        date_default_timezone_set('America/Bogota');
        $time = date('Y-m-d H:i:s', time());
        try {
            $validacion_sql = "select * from gs_despacho_temporal where object_id = " . $data["vehiculo_id"];
            $validate = DB::select($validacion_sql);
            if (count($validate) == 0) {
                $insert = "insert into gs_despacho_temporal(object_id, user_id, empresa_id, ruta_id, grupo_id, turno, hora_llegada, estado) values(" . $data["vehiculo_id"]
                        . ", " . $data_despachador[0]->user_id
                        . ", " . $data_despachador[0]->empresa_id
                        . ", " . $data["route_id"]
                        . ", " . $data["group_id"]
                        . ", " . $turno
                        . ", '" . $time
                        . "', 2"
                        . ");";
                DB::beginTransaction();
                DB::insert($insert);
                DB::commit();
                return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
            }
            return Response::json(array('success' => false, 'mensaje' => "El vehiculo ya se encuentra en paradero. Intente de Nuevo."));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }

    public function getVehiculosdisponibles() {
        $data = Input::all();
        $sql = "select dt.object_id, gso.name vehiculo from gs_despacho_temporal dt "
                . "join gs_user_objects ob on dt.object_id = ob.object_id "
                . "join gs_objects gso on ob.imei = gso.imei "
                . "where dt.user_id = " . $data["user_id"]
                . " and dt.grupo_id = " . $data["group_id"]
                . " and estado = 2;";
        $vehiculos = DB::select($sql);
        return Response::json(array('vehiculosparadero' => $vehiculos));
    }

    public function getAllvehiculos() {
        $data = Input::all();
        $sql = "select dt.object_id, gso.name vehiculo, gso.plate_number, dt.turno, dt.hora_llegada, d.driver_name 
                from gs_despacho_temporal dt                 
                join gs_user_objects ob on dt.object_id = ob.object_id 
                join gs_objects gso on ob.imei = gso.imei
                join gs_user_object_drivers d ON d.driver_id = ob.driver_id
                where dt.user_id =  ". $data["user_id"] ." and (estado = 2 or estado = 4)
                order by dt.turno asc;";
        $vehiculos = DB::select($sql);
        return Response::json(array('vehiculosparadero' => $vehiculos));
    }

    public function getInfovehiculo() {
        $data = Input::all();
        $sql = "SELECT gso.object_id, ob.name, ob.imei, ob.plate_number placa, d.driver_name conductor, 
                d.driver_phone telefono, dt.hora_llegada, dt.turno, dt.ruta_id, SUBSTRING_INDEX(ur.route_points,',', 2) coordenadas,  ug.group_id
                FROM gs_user_objects gso
                JOIN gs_objects ob ON gso.imei = ob.imei
                JOIN gs_user_object_drivers d ON gso.driver_id = d.driver_id
                JOIN gs_despacho_temporal dt ON dt.object_id = gso.object_id
                JOIN gs_user_routes ur ON ur.route_id = dt.ruta_id
                JOIN gs_user_object_groups ug ON ug.group_id = dt.grupo_id
                WHERE gso.object_id = " . $data["vehiculo_id"]
                . ";"
        ;
        $info_vehiculo = DB::select($sql);
        return Response::json(array('vehiculo' => $info_vehiculo[0]));
    }
    //Funcion que permite despachar vehiculos y registrarlos en la tabla despachos
    public function getDespachovehiculo() {
        $data = Input::all();
        $update_sql = "update gs_despacho_temporal set "
                . "estado = 3 "
                . "where object_id = " . $data["object_id"] . ";";
        DB::update($update_sql);
        //Realizo la consulta para consultar el numero_recorrido
        $sql_consult = "select max(numero_recorrido) numero_recorrido from despachos where vehiculo_id = " . $data["object_id"];
        $result_consulta = DB::select($sql_consult);
        $num_vuelta = 0;
        if (count($result_consulta) > 0) {            
            $num_vuelta = ($result_consulta[0]->numero_recorrido + 1);
        } else {
           $num_vuelta = $num_vuelta+1;
        }
       
        //Capturo la hora de salida del evento clic despachar
        date_default_timezone_set('America/Bogota');
        $time = date('Y-m-d H:i:s', time());
        $coordenadas = $data["coordenadas"];
        $myArray = explode(',', $coordenadas);
        
        $sql_despachador_info = "select * from gs_info_despachador where user_id = " .  $data["user_id"] . ";";
        $info = DB::select($sql_despachador_info);

        $sql_insert = "insert into despachos(vehiculo_id, latitud, longitud, ruta_id, imei, hora_salida, estado_id, user_id, "
                . "numero_recorrido) values("
                . $data["object_id"]
                . ", '" . $myArray[0]
                . "', '" . $myArray[1]
                . "', " . $data["ruta_id"]
                . ", '" . $data["imei"]
                . "', '" . $time
                . "', 3"
                . ", " . $info["id"] // Se cambia el id -> para el nuevo registro se usa el id del despachador 
                . ", " . $num_vuelta
                . ");";
        try {
            DB::beginTransaction();
            DB::insert($sql_insert);            
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El vehículo ha sido despachado con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede despachar el vehiculo. Contacte al administrador de sistema. " . $e, 'error' => true));
        }
    }

    public function getVehiculosdespachados() {
        $data = Input::all();
        $sql_despachador_info = "select * from gs_info_despachador where user_id = " .  $data["user_id"] . ";";
        $info = DB::select($sql_despachador_info);        
        $sql = "select d.despacho_id, d.imei, SUBSTRING(d.hora_salida,11,9) hora_salida, d.user_id, d.numero_recorrido vuelta, 
                gob.name, e.descripcion estado, d.ruta_id, ur.route_name, uo.object_id, ug.group_id, ug.group_name
                from despachos d
                join gs_objects gob ON gob.imei = d.imei
                join estados e ON e.estado_id = d.estado_id
                join gs_user_routes ur ON ur.route_id = d.ruta_id
                join gs_user_objects uo ON d.imei = uo.imei
                join gs_user_object_groups ug ON ug.group_id = uo.group_id
                where d.estado_id = 3 and d.hora_salida>(select CURDATE()) AND d.hora_salida <= (SELECT NOW()) 
                and d.user_id = " . $info["id"] 
                . " ORDER BY d.hora_salida asc;";  // Error esta que no estoy mandando el ID del despachador.... Sugerencia: el dministrador debe ver los despachos
        $vehiculosdespachados = DB::select($sql);
        return Response::json(array('vehiculosdespachados' => $vehiculosdespachados));
    }

    public function getLlegadavehiculo() {
        $data = Input::all();
        date_default_timezone_set('America/Bogota');
        $time = date('Y-m-d H:i:s', time());
        $sql_update = "update despachos set "
                . "estado_id = 4"
                . ", hora_llegada = '" . $time
                . "' where despacho_id = " . $data["despacho_id"];
        //Actualizo el estado del despacho de 3 => 4 que esta en espera (parqueadero) y le asigna la hora de llegada        
        DB::update($sql_update);
        //Valido el ultimo turno para despachar y poner en cola al vehiculo
        $sql_turno = "SELECT dt.turno FROM `gs_despacho_temporal` dt
                      WHERE dt.turno = ( 
                             SELECT MAX( turno )  FROM gs_despacho_temporal
                       );";
        $turno = DB::select($sql_turno);        
        $turno;        
        try {
            $sql = "update gs_despacho_temporal set "
                    . "turno = " .($turno[0]->turno + 1)
                    . ", hora_llegada = '" . $time                    
                    . "', estado = 4"
                    . " where object_id = " . $data["object_id"]
                    . ""; 
           
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El vehículo se encuentra en paradero."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }
    
    public function getLlegadavehiculos(){
       $data = Input::all();
       $sql = "select dt.object_id, dt.hora_llegada, ob.imei, ob.name vehiculo, ob.plate_number placa, d.driver_name conductor
               from gs_despacho_temporal dt 
               join gs_user_objects guo ON guo.object_id = dt.object_id
               join gs_user_object_drivers d ON d.driver_id = guo.driver_id
               join gs_objects ob ON ob.imei = guo.imei 
               where dt.user_id = " .$data["user_id"] .
               " and dt.estado = 4
               order by dt.hora_llegada asc; ";
     
       $llegadavehiculos = DB::select($sql);
       return Response::json(array('success' => true, 'llegadavehiculos' => $llegadavehiculos));
             
    }
    
    public function getHistorialrecorrido(){
       $data = Input::all(); 
       $sql = "SELECT d.vehiculo_id, d.numero_recorrido, SUBSTRING(d.hora_salida,11,9) hora_salida, "
               . "SUBSTRING(d.hora_llegada,11,9) hora_llegada, d.ruta_id, r.route_name, obd.driver_name, gob.plate_number placa, gob.name vehiculo, "
               . "TIMEDIFF(d.hora_llegada,d.hora_salida) as diferencia "
               . "FROM despachos d "
               . "JOIN gs_user_objects guo ON guo.object_id = d.vehiculo_id "
               . "JOIN gs_objects gob ON gob.imei = guo.imei "
               . "JOIN gs_user_object_drivers obd ON guo.driver_id = obd.driver_id "
               . "JOIN gs_user_routes r ON r.route_id = d.ruta_id "
               . "WHERE d.vehiculo_id = " .$data["object_id"] . " "
               . " and d.hora_salida>(select CURDATE()) AND d.hora_salida <= (SELECT NOW())"
               . ";"
               ;
       $estadistica = DB::select($sql);  
       $cant = count($estadistica);
       return Response::json(array('success' => true, 'estadistica' => $estadistica, 'conductor' => $estadistica[0]->driver_name, 'vueltas' => $estadistica[$cant-1]->numero_recorrido));
       
    }

}
