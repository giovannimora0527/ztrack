<?php

class Ciudad extends Eloquent {


    public function departamento() {
		return $this->belongsTo('Departamento');
    }    
    
    
}
