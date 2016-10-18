<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RutasController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('serviceAuth', array('only' =>
            array('postCreate', 'postUpdate', 'getDestroy')));
    }

    public function getRutasbyid() {
        $user_id = Input::get('user_id');
        $area_id = Input::get('area_id');
        $rutas = DB::select('select route_id, route_name from gs_user_routes where user_id = ' . $user_id . ' and group_id = ' . $area_id . ' order by route_name asc');
        return Response::json(array('rutas' => $rutas));
    }

    public function getAllinforutasbyid() {
        $user_id = Input::get('user_id');
        $rutas = Ruta::where('user_id', '=', $user_id)->get();
        return Response::json(array('rutas' => $rutas));
    }

    public function postSaveasignacionrutas() {
        $data = Input::all();
        $user_id = Input::get("user_id");
        $area_id = Input::get("area_id");
        $despachador_id = Input::get("despachador_id");
        date_default_timezone_set('America/Bogota');
        $time = time();
        $fecharegistro = date("Y-m-d H:i:s", $time);
        $insert = false;

        //Insertar el registro de la asignacion del despachador a la ruta(s)
        for ($i = 0; $i < count($data) - 3; $i++) {
            $route_id = ($data[$i]["route_id"]);
            $sql = "insert into gs_despachador_ruta(desp_id, user_id, area_id, route_id, fecha_asignacion) values("
                    . "" . $despachador_id
                    . "," . $user_id
                    . "," . $area_id
                    . "," . $route_id
                    . ",'" . $fecharegistro
                    . "')";
            try {
                DB::beginTransaction();
                DB::insert($sql);
                $insert = true;
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $insert = false;
                return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
            }
        }
        if ($insert) {
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        }
    }

    public function getRutasdespachador() {
        $data = Input::all();
        $sql = "select dr.route_id, ur.route_name from gs_despachador_ruta dr "
                . " join gs_info_despachador i ON dr.desp_id = i.id"
                . " join gs_user_routes ur ON dr.route_id = ur.route_id"
                . " where i.user_id = " . $data["user_id"]
                . ";";
        try {
            DB::beginTransaction();
            $rutas = DB::select($sql);
            DB::commit();
            return Response::json(array('rutas' => $rutas));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se pudo cargar los registro de la BD: " . $e, 'error' => true));
        }
    }

    public function getGruposvehiculosbyrutasid() {
        $data = Input::all();
        //Selecciono el id de la empresa para que el despachador trabajo y poder localizar los gruposrutas registrados por el usuario empresa
        $sql = "select empresa_id from gs_info_despachador where user_id = " . $data["user_id"];
        $result = DB::select($sql);
        $query = "select gr.group_id, guog.group_name from gs_gruposrutas gr "
                . "join gs_user_object_groups guog ON guog.group_id = gr.group_id "
                . "where gr.user_id = " . $result[0]->empresa_id
                . " and gr.route_id = " . $data["route_id"];
        $grupos = DB::select($query);

//        if(count($grupos)>0){
//          for($i=0; $i < count($grupos); $i++){
//             $qry = "select gso.object_id, gob.name, gso.imei "
//                     . "from gs_user_objects gso "
//                     . "join gs_objects gob on gob.imei = gso.imei "
//                     . "where gso.user_id = " .$result[0]->empresa_id
//                     . " and gso.group_id = " .$grupos[$i]->group_id
//                     . ";" ;
//            $vehiculos = DB::select($qry);
//            array_push($array_result,$vehiculos);
//            echo($qry);
//            break;
//          }   
//        } 
        return Response::json(array('grupos' => $grupos));
    }

    public function getVehiculosbygroupid() {
        $data = Input::all();
        //Selecciono el id de la empresa para que el despachador trabajo y poder localizar los vehiculos asociados al grupo
        $sql = "select empresa_id from gs_info_despachador where user_id = " . $data["user_id"];
        $result = DB::select($sql);
        $qry = "select gso.object_id, gob.name, gso.imei "
                . "from gs_user_objects gso "
                . "join gs_objects gob on gob.imei = gso.imei "
                . "where gso.user_id = " . $result[0]->empresa_id
                . " and gso.group_id = " . $data["group_id"]
                . ";";
        $vehiculos = DB::select($qry);
        if (count($vehiculos) > 0) {
            return Response::json(array('vehiculos' => $vehiculos));
        } else {
            return Response::json(array('empty' => true, 'mensaje' => 'No hay vehiculos asociados al grupo. Contacte al administrador.'));
        }
    }
    
    // copiar y pegar en despachador controller

    
}
