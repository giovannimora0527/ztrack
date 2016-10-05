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
        $area_id =  Input::get('area_id'); 
        $rutas = DB::select('select route_id, route_name from gs_user_routes where user_id = ' . $user_id . ' and group_id = ' .$area_id . ' order by route_name asc');
        return Response::json(array('rutas' => $rutas));
    }
    
    public function getAllinforutasbyid() { 
        $user_id = Input::get('user_id'); 
        $rutas = Ruta::where('user_id', '=', $user_id)->get();
        return Response::json(array('rutas' => $rutas));
    }
    

}

