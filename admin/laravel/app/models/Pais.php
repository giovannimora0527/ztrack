<?php

class Pais extends Eloquent {

    public function departamento() {
		return $this->hasMany('Departamento');
    }
        
    
}