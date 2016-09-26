<?php

class Usertype extends Eloquent {

    protected $table = 'tipo_usuarios';
    public $timestamps = false;   
    protected $fillable = array('id', 'descripcion');
    
}


