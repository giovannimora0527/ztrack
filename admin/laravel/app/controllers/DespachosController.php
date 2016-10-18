<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DespachosController extends \BaseController {
    
    
    public function getGruposxrutaid(){
        $data = Input::all();
        $sql = "select g.group_id, g.group_name 
                from gs_gruposrutas gr
                join gs_user_object_groups g on g.group_id = gr.group_id
                where gr.user_id = " . $data["user_id"] ." and 
                gr.area_id = " .$data["area_id"] .";";
        try {
            DB::beginTransaction();
            $groups = DB::select($sql);
            DB::commit();
            return Response::json(array('groups' => $groups));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se puede traer la informacion. " . $e, 'error' => true));
        } 
    }
    
    public function getVehiculoxgroupid (){
        $data = Input::all();        
        $sql = "SELECT guo.object_id, guo.imei 
                FROM gs_user_objects guo
                JOIN gs_objects gob ON gob.imei = guo.imei
                WHERE guo.user_id = " . $data["user_id"] ." 
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
    
    function getDespachadoresbyuserid(){
       $user_id = Input::get("user_id");
       $sql = "select id, nombre, apellido from gs_info_despachador where empresa_id = " .$user_id;
       try {
            DB::beginTransaction();
            $despachadores = DB::select($sql);
            DB::commit();
            return Response::json(array('despachadores' => $despachadores));
        } catch (Exception $e) {
            return Response::json(array('mensaje' => "No se puede cargar la información. " . $e, 'error' => true));
        } 
       
    }
    
    
    
    
    
}
