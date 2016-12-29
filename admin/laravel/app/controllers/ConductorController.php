<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConductorController extends \BaseController {

    public function getConductores() {
        $user_id = Input::get("user_id");
        $sql = "select * from gs_user_object_drivers where user_id = " . $user_id . " and estado_id = 1";
        $conductores = DB::select($sql);
        return Response::json(array('conductores' => $conductores));
    }

    public function getConductoresasignados() {
        $user_id = Input::get("user_id");
        $sql = "select * from gs_user_object_drivers where user_id = " . $user_id . " and estado_id = 2";
        $conductores = DB::select($sql);
        return Response::json(array('conductores' => $conductores));
    }

    public function postSaveconductor() {
        $data = Input::all();
        $sql = "select driver_id from gs_user_object_drivers where driver_idn = '" . $data["identificacion"] . "';";
        $result = DB::select($sql);
        if (count($result) > 0) {
            return Response::json(array('mensaje' => 'El conductor ya se encuentra en la base de datos con el número del DNI. Intente de nuevo', 'success' => false));
        }

        $sql = "insert into gs_user_object_drivers (user_id, driver_name, driver_assign_id, driver_idn, driver_address, driver_phone, driver_email, driver_desc, estado_id) values("
                . "'" . $data["user_id"]
                . "','" . strtoupper($data["nombres"])
                . "', '" . $data["codigo"]
                . "', '" . $data["identificacion"]
                . "', '" . strtoupper($data["direccion"])
                . "', '" . $data["telefono"]
                . "', '" . $data["email"]
                . "', '" . $data["descripcion"]
                . "', 1"
                . ");";
        try {
            DB::beginTransaction();
            DB::insert($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }

    public function postUpdateconductor() {
        $data = Input::all();
        $sql = "update gs_user_object_drivers set "
                . " driver_idn = '" . $data["driver_idn"]
                . "', driver_name = '" . strtoupper($data["driver_name"]);
        
        if(isset($data["driver_assign_id"])){
           $sql .= "', driver_assign_id = '" . $data["driver_assign_id"];
        }
         $sql  .= "', driver_address = '" . strtoupper($data["driver_address"])
                . "', driver_phone = '" . $data["driver_phone"]
                . "', driver_email = '" . $data["driver_email"]
                . "', driver_desc = '" . $data["driver_desc"]
                . "' where driver_id = " . $data["driver_id"];         
        
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "Los datos del conductor identificado con c.c N° " . $data["driver_idn"] . " se ha actualizado correctamente"));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }

    public function postDeleteconductor() {
        $conductor_id = Input::get("conductor_id");
        //Buscar si el conductor se encuentra asignado a un vehiculo. Si es asi no se puede eliminar
        $sql = "select object_id from gs_user_objects where driver_id = " . $conductor_id;
        $result = DB::select($sql);
        if (count($result) > 0) {
            return Response::json(array('success' => false, 'mensaje' => "El registro se encuentra asociado a un vehículo. Primero elimine la asociación con el vehículo y después continue con la operación."));
        }

        $sql = "delete from gs_user_object_drivers where driver_id = " . $conductor_id;
        try {
            DB::beginTransaction();
            DB::delete($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha eliminado con éxito."));
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede eliminar el registro. " . $e, 'error' => true));
        }
    }

    public function postSearchconductor() {
        $data = Input::all();
        $min = Input::get("min");
        $max = Input::get("max");
        $more_results = false;
        $sqlcount = "select count(d.driver_id) conteo
                from gs_user_object_drivers d 
                left join conductor_estado ce ON ce.id = d.estado_id
                left join gs_user_objects gu ON gu.driver_id = d.driver_id
                left join gs_gruposrutas gr ON gr.group_id = gu.group_id
                left join gs_user_routes r ON r.route_id = gr.route_id
                left join gs_user_object_groups g ON g.group_id = gu.group_id
                where d.user_id = '" . $data["user_id"] . "' ";
        
        $sql = "select d.driver_id, d.driver_name, d.driver_address, d.driver_assign_id, d.driver_idn, d.driver_phone, d.driver_email, d.driver_desc,
                d.estado_id, ce.descripcion, g.group_name, r.route_name
                from gs_user_object_drivers d 
                left join conductor_estado ce ON ce.id = d.estado_id
                left join gs_user_objects gu ON gu.driver_id = d.driver_id
                left join gs_gruposrutas gr ON gr.group_id = gu.group_id
                left join gs_user_routes r ON r.route_id = gr.route_id
                left join gs_user_object_groups g ON g.group_id = gu.group_id
                where d.user_id = '" . $data["user_id"] . "' ";
        if (isset($data["ruta"])) {
            $sql .= "and r.route_id = " . $data["ruta"] . " ";
            $sqlcount .= "and r.route_id = " . $data["ruta"] . " ";
        }
        if (isset($data["nombreconductor"])) {
            $sql .= "and d.driver_name LIKE '%" . strtoupper($data["nombreconductor"]) . "%' ";
            $sqlcount .= "and d.driver_name LIKE '%" . strtoupper($data["nombreconductor"]) . "%' ";
        }
        if (isset($data["documento"])) {
            $sql .= "and d.driver_idn = '" . $data["documento"] . "' ";
            $sqlcount .= "and d.driver_idn = '" . $data["documento"] . "' ";
        }
        if (isset($data["telefono"])) {
            $sql .= "and r.driver_phone = '" . $data["telefono"] . "' ";
            $sqlcount .= "and r.driver_phone = '" . $data["telefono"] . "' ";
        }
        $sql .= " order by d.driver_name asc";
        $count = DB::select($sqlcount);
        if ($count[0]->conteo > $max) {
           $sql .= " LIMIT " . $min . "," . $max . ";";
           $more_results = true;
        }
         
        $result = DB::select($sql);
        return Response::json(array('conductores' => $result, 'min' => intval($min), 'max' => $max - 1, 'count' => $count[0]->conteo,
                            'moreresults' => $more_results, 'success' => true));
    }

}
