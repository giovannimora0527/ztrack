<?php

class SessionController extends \BaseController {

    /**
     * Start a session to a new user if credendials are right.
     *
     * @return Response
     */
    public function postLogin() {
        try {
            $username = Input::get('name');
            $password = Input::get('password');
             
            if (Auth::attempt(array('name' => $username, 'password' => $password))) {                
                $user = Auth::user();
                $response = array("user" => $username, "token" => Session::token());
                return Response::json(array('data' => array('mensaje' => 'Sesión iniciada correctamente', 'info' => $response)));
            } else {                
                return Response::json(array('data' => array('mensaje' => 'Usuario y/o contrasena incorrectos')), 400);
            }
        } catch (\Exception $e) {
            return Response::json(array('data' => array('mensaje' => $e->getMessage())), 500);
        }
    }

    public function postLogout() {

        Auth::logout();
        return Response::json(array('data' => array('mensaje' => 'Sesion finalizada correctamente')), 200);
    }
    
    public function getFind(){
        echo("entroooooooooooooo");
    }

}
