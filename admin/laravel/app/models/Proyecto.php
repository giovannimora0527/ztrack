<?php

use Illuminate\Auth\UserInterface;

class Proyecto extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proyecto';      
    protected $fillable = array('id','nombre', 'user_id', 'type', 'duracion', 'fechaini','fechafin', 'active', 'created_date');
    
    
}

