<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Conductor extends Eloquent {
    
    
    protected $connection = 'gs';    
    protected $table = 'gs_user_object_drivers';      
    protected $fillable = array('driver_id','user_id', 'driver_name', 'driver_assign_id', 'driver_idn', 'driver_address', 'driver_phone',
                                 'driver_email', 'driver_desc', 'estado_id');
}
