<?php

class CiudadController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('serviceAuth', array('only' =>
            array('postCreate', 'postUpdate', 'getDestroy')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postCitys() {
        $ciudades = Ciudad::where('departament_id', '=', Input::get('departamento_id'))->get();
        return Response::json(array('ciudades' => $ciudades));
    }

}
