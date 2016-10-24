<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ReportesController extends \BaseController {

    public function getCountvehiculos() {
        $data = Input::all();
        $sql = "select count(*) numero from gs_user_objects where user_id = " . $data["user_id"];
        $result = DB::select($sql);
        return Response::json(array('resultado' => $result[0]));
    }

    public function getCountconductores() {
        $data = Input::all();
        $sql = "select count(*) numero from gs_user_object_drivers where user_id = " . $data["user_id"];
        $result = DB::select($sql);
        return Response::json(array('resultado' => $result[0]));
    }

    public function getVehiculosenruta() {
        $data = Input::all();
        $sql = "select user_id from gs_info_despachador where empresa_id = " . $data["user_id"];
        $result = DB::select($sql);
        $count = 0;
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                $query = "select count(*) numero from despachos where user_id = " . $result[$i]->user_id . " and estado_id = 3;";
                $resultado = DB::select($query);
                $count += $resultado[0]->numero;
            }
        }
        return Response::json(array('resultado' => $count));
    }

    public function getVehiculosenparqueadero() {
        $data = Input::all();
        $sql = "select user_id from gs_info_despachador where empresa_id = " . $data["user_id"];
        $result = DB::select($sql);
        $count = 0;
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                $query = "select count(*) numero from gs_despacho_temporal where user_id = " . $result[$i]->user_id . " and (estado = 2 or estado = 4) "
                        . "and hora_llegada > (select CURDATE()) AND hora_llegada <= (SELECT NOW())"
                        . ";";
                $resultado = DB::select($query);
                $count += $resultado[0]->numero;
            }
        }
        return Response::json(array('resultado' => $count));
    }

    public function getCantidadrutas() {
        $data = Input::all();
        $sql = "select count(user_id) numero from gs_user_routes where user_id = " . $data["user_id"];
        $result = DB::select($sql);
        return Response::json(array('resultado' => $result[0]->numero));
    }

    public function getCantidaddespachadores() {
        $data = Input::all();
        $sql = "select count(user_id) numero from gs_info_despachador where empresa_id = " . $data["user_id"];
        $result = DB::select($sql);
        return Response::json(array('resultado' => $result[0]->numero));
    }

    public function getTiempopromedio() {
        $data = Input::all();
        $sql = "select user_id from gs_info_despachador where empresa_id = " . $data["user_id"] .";";       
        $result = DB::select($sql);
        $array_result = array();
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                $query = "select CAST((SUM((TIMEDIFF(d.hora_llegada,d.hora_salida)))/COUNT(d.hora_salida)) AS TIME) AS promedio
                          from despachos d
                          where user_id = " . $result[$i]->user_id . ";";                
                $resultados = DB::select($query);
                array_push($array_result, $resultados);
            }            
        }  
        return Response::json(array('resultado' => $array_result));
    }

}
