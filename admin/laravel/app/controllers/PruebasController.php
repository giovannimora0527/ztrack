<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PruebasController extends \BaseController {

    public function getDatabases() {
        $data = Input::all();
        $user_id = $data["user_id"];
        for($i=1; $i <= 10; $i++){
            $insert = "insert into gs_user_objects (user_id, imei, group_id, driver_id, trailer_id) values("
                    . "" . $user_id
                    . ",'" . $i 
                    . "', 1"
                    . ", 1"
                    . ", 0"
                    . ");";           
            DB::insert($insert);
            $insert = "insert into gs_objects (imei, protocol, active, active_dt, name, icon, map_icon, tail_color, tail_points, plate_number, "
                    . "odometer_type, odometer, time_adj, dt_chat) values("                   
                    . "'" . $i 
                    . "', 'android' "
                    . ", 'true'"
                    . ", '2016-10-20'"
                    . ", '00" . $i
                    . "', 'img/markers/objects/31.png'"
                    . ", 'arrow'"
                    . ", '#00FF44'"
                    . ", 7"
                    . ", 'ABC-0" . $i
                    . "', 'gps'"
                    . ", 0"
                    . ", -5"
                    . ", '0000-00-00 00:00:00' "
                    . ");";               
            DB::insert($insert);
            
            
            $sql = "CREATE TABLE gs_object_data_11111111111112" .$i  
                . "(
                `dt_server` datetime NOT NULL,
                `dt_tracker` datetime NOT NULL,
                `lat` double DEFAULT NULL,
                `lng` double DEFAULT NULL,
                `altitude` double DEFAULT NULL,
                `angle` double DEFAULT NULL,
                `speed` double DEFAULT NULL,
                `params` varchar(2048) COLLATE utf8_bin NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"; 
            DB::insert($sql);
        }
        
               
       
    }

}
