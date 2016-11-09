<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NovedadController extends \BaseController {

    public function getVehiculos() {
        $data = Input::all();
        $sql = "SELECT ub.object_id, ub.imei, ob.name, ob.plate_number,    
                dt.estado, e.descripcion, 
                CASE 
                  WHEN dr.driver_name IS NULL THEN 'Sin Conductor'
                  ELSE dr.driver_name 
                  END AS driver_name  
                FROM gs_gruposrutas gr
                JOIN gs_user_objects ub ON ub.group_id = gr.group_id
                JOIN gs_objects ob ON ob.imei = ub.imei
                JOIN gs_user_object_drivers dr ON dr.driver_id = ub.driver_id
                JOIN gs_despacho_temporal dt ON dt.object_id = ub.object_id
                JOIN estados e ON e.estado_id = dt.estado
                WHERE gr.route_id = " . $data["ruta_id"] .    "  
                and gr.user_id = " . $data["user_id"] .    " 
                ;";        
        $resultados = DB::select($sql);
        return Response::json(array('vehiculos' => $resultados));
    }

}
