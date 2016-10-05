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
        $groups = DB::select('select group_id, group_name from gs_user_object_groups where user_id = ' . $user_id . ' order by group_name asc');        
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

    

}
