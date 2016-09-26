<?php

class DepartamentoController extends \BaseController {

    public function __construct() {

        $this->beforeFilter('serviceAuth', array('only' =>
            array('postCreate', 'postUpdate', 'getDestroy')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function getDepartamentos() {
        $consulta = DB::select('SELECT * FROM mg_departamentos')->get();
        return Response::json(array('departamentos' => $consulta));
    }

    public function getCiudades() {
        $departmentId = Input::get('departamento_id');
        $department = NULL;

        if (!is_null($departmentId)) {
            $department = Departamento::with('Ciudad')->find($departmentId);
        }

        return Response::json(array('ciudades' => $department->ciudades));
    }

}
