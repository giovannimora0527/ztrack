<?php

use Illuminate\Auth\UserInterface;

class Equipo extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gruporisk';      
    protected $fillable = array('id','nombre', 'apellido', 'email', 'proyectoid');
    
    
    
    
}


