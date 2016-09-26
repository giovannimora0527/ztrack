<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProyectoController extends BaseController {

    public function getTipos() {
        return Response::json(array('tipoproyecto' => ProyectoTipo::all()));
    }

    public function getProyectos() {  
      $proyectos = array();  
      $proyectos = Proyecto::where('user_id', '=', Auth::user()->id)->get();      
      if(count($proyectos)>0){
         return Response::json(array('proyectos' => $proyectos));
      }
      else{
         return  Response::json(array('data' => [])); 
      }
    }

}
