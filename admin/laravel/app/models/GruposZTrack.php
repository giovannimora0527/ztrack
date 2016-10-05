<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class GruposZTrack extends Eloquent {
    
    protected $connection = 'gs';    
    protected $table = 'gs_gruposrutas';      
    protected $fillable = array('gr_id','user_id', 'route_id', 'group_id', 'fechaini', 'fechafin');
    

}

