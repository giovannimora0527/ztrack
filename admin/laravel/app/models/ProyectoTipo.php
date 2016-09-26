<?php

class ProyectoTipo extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proyecto_tipo';
    public $timestamps = false;   
    protected $fillable = array('id','descripcion');
    
}
