<?php

class Departamento extends Eloquent {	

	protected $hidden = array('pais_id');

	public function ciudades(){
    
        return $this->hasMany('Ciudad');
    }

    public function paises(){

    	return $this->belongsTo('Pais');
    }
  
}
