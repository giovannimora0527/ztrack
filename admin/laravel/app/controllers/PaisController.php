<?php

class PaisController extends \BaseController {

    /**
     * Return a list of countries
     *
     * @return Response
     */
    public function getPaises() {
        return Response::json(array('paises' => Pais::all()));
    }

    /**
     * Return a list of departments given a country
     *
     * @return Response
     */
    public function getDepartamentos() {
        $paisId = Input::get('pais_id');
        

        if (!is_null($paisId)) {
            $pais = Pais::find($paisId);
            return Response::json(array('data' => $pais->departamentos), 200);
        } else {
            return Response::json(array('mensaje' => 'No existe el pais'), 400);
        }
    }

}
