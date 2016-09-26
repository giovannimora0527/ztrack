<?php

use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    public $timestamps = false;   
    protected $fillable = array('name', 'password', 'type', 'nombre', 'apellido','email','direccion','telefono','genero','fnacimiento','noticias', 'photo');
    
    
    public function getRememberToken() {
        return null;
    }

    public function setRememberToken($value) {
        //not supported yet
    }

    public function getRememberTokenName() {
        return null;
    }

    public function getAuthPassword() {
        return $this->password;
    }

    public function getAuthIdentifier() {
        return $this->getKey();
    }

    /**
     * Overrides the method to ignore the remember token
     */
    public function setAttribute($key, $value) {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute) {
            parent::setAttribute($key, $value);
        }
    }
    
    
    

}
