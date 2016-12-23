<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GruposController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('serviceAuth', array('only' =>
            array('postCreate', 'postUpdate', 'getDestroy')));
    }

    public function getGrupos() { 
        $user_id = Input::get('user_id');
        $groups = DB::select('select group_id, group_name, group_desc from gs_user_object_groups where user_id = ' . $user_id . ' AND group_id <> 0 order by group_name asc');        
        return Response::json(array('grupos' => $groups));
    }
    
    public function getAllinforupos() { 
        $user_id = Input::get('user_id');        
        $groups = Grupo::where('user_id', '=', $user_id)->get();
        return Response::json(array('grupos' => $groups));
    }
    
    public function getCargarareas(){
       $user_id = Input::get('user_id'); 
       $areas = DB::select('select group_id id, group_name name from gs_user_places_groups where user_id = ' . $user_id);
        return Response::json(array('areas' => $areas));
    }
    
        
    public function postSavegrupo(){
        $data = Input::all();        
        $sql = "select * from gs_user_object_groups where group_name = '" . strtoupper($data["nombre"]) . "';";
        $result = DB::select($sql);
        if(count($result) > 0){
           return Response::json(array('success' => false, 'mensaje' => "El grupo ya se encuentra registrado. Cambie a continuación el nombre.")); 
        }
        $sql = "insert into gs_user_object_groups(user_id, group_name, group_desc) values("
                . "" . $data["user_id"]               
                . ", '" . strtoupper($data["nombre"]) 
                . "', '" . $data["descripcion"]
                . "'"                
                . ");";
        try {
            DB::beginTransaction();
            DB::insert($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha guardado correctamente"));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('error' => "No se puede guardar el registro. " . $e, 'error' => true));
        }
    }
    
    public function postSearchgrupo(){
        $data = Input::all();
        $sql = "select * from gs_user_object_groups where user_id = " . $data["user_id"];
        if(isset($data["nombregrupo"])){
           $sql .= " and group_name LIKE '%" . strtoupper($data["nombregrupo"]) . "%' "; 
        }   
        $sql .= " order by group_name asc;";
        try {            
            $results = DB::select($sql);            
            return Response::json(array('resultados' => $results));
        } catch (Exception $e) {            
            return Response::json(array('error' => "No se puede realizar la consulta. Contacte al admin del sistema. " . $e, 'error' => true));
        }        
    }
    
    public function postActualizargrupo(){
        $data = Input::all();
        $sql = "update gs_user_object_groups set "
                . " group_name = '" .$data["group_name"]
                . "', group_desc = '" .$data["group_desc"]
                . "' where group_id = " .$data["group_id"];
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "El registro se ha actualizado correctamente"));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('error' => "No se puede actualizar el registro. " . $e, 'error' => true));
        }
        
    }

    

}
