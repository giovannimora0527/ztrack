<?php

class UserController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('serviceAuth', array('only' =>
            array('postCreate', 'postPassword', 'getUsers')));
    }

    public function getUsers() {
        return Response::json(array('users' => User::all()));
    }

    public function postValidateuser() {
        $name = Input::get('name');
        $password = Input::get('password');

        $usuario = User::where('name', '=', $name)->get();
        if (count($usuario) > 0) {
            if (strcmp($usuario[0]->password, $password) === 0) {
                return Response::json(array('success' => true, 'msj' => 'Bienvenido'));
            } else {
                return Response::json(array('success' => false, 'msj' => 'Usuario y/o Contraseña Incorrectos'));
            }
        } else {
            return Response::json(array('error' => true, 'msj' => 'Usuario y/0 Contraseña Incorrectos'));
        }
    }

    public function deleteDestroy() {
        $id = Input::get('user_id');
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return "Se elimino el usuario " . $user->name;
        }
        return "No es posible eliminar pues no existe";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function postCreate() {
        $usuario = new User;
        $input = Input::all();
        $usuario->name = $input['username'];
        $usuario->password = Hash::make($input['password1']);
        $usuario->type = $input['rol'];
        $usuario->type = $usuario->type['descripcion'];
        $usuario->nombre = $input['nombre'];
        $usuario->apellido = $input['apellido'];
        $usuario->email = $input['email'];        

        try { // VALIDACION DE LOS CAMPOS NO OBLIGATORIOS
            if ($input['direccion'] != null) {
                $usuario->direccion = $input['direccion'];
            }
            if ($input['telefono'] != null) {
                $usuario->telefono = $input['telefono'];
            }
            if ($input['fechaNacimiento'] != null) {
                $usuario->fnacimiento = $input['fechaNacimiento'];
            }
        } catch (Exception $e) {
           
        }
        $usuario->genero = $input['genero'];
        $usuario->genero = $usuario->genero['value'];
        $usuario->noticias = $input['noticias'];

        try {
            $resultado = User::where('name', '=', $usuario->name)->get();
            if (!count($resultado) > 0) {
                DB::beginTransaction();
                $usuario->save();
                DB::commit();
                return Response::json(array('success' => true, 'mensaje' => "El usuario se ha registrado correctamente"));
            } else {
                return Response::json(array('error' => true, 'mensaje' => "El username se encuentra registrado en nuestra BD"), 401);
            }
        } catch (Exception $e) {
            return Response::json(array('error' => "No se puede guardar el usuario. " + $e, 'error' => true), 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getUser() {
        $data = Input::all();
        $sql = "select name, username, account_expire_dt, 
                    CASE 
                        WHEN profile_id = 1 THEN 'Administrador'
                        ELSE 'Despachador'
                        END AS profile, 
                    CASE 
                        WHEN email = '' || null THEN 'N/A'
                        ELSE email
                        END AS email "
                . " from gs_users where id = " . $data["user_id"];
        $user = DB::select($sql);
        return Response::json(array('user' => $user[0]));
    }

    public function postPassword() {
        $input = Input::json()->all();
        $input_array = (array) $input;
        return User::updatePassword($input_array);
    }

    public function postLogin() {

        $input = Input::json()->all();
        $input_array = (array) $input;

        $respuesta = array();
        $respuesta['message'] = "Error 401, no esta autenticado ";
        if (Auth::attempt(array('name' => $input_array['name'], 'password' => $input_array['password']), false)) {

            $user = User::find(Auth::user()->id);
            $user->token = Session::token();
            Session::put('user', $user);

            $respuesta['user'] = $user;
            $respuesta['message'] = 'all-fine';
        }

        return Response::json($respuesta);
    }

    public function postLogout() {
        $input = Input::get('token');
        Auth::logout();
        Session::forget('user');
        return Session::all();
    }

    public function postChangepassword() {
        $pass1 = Input::get('pass1');
        $user = User::where('name', '=', 'admin')->get();
        if (count($user) > 0) {
            $user->password = Hash::make($pass1);
            $nombre = " 'admin' ";
            $password = " '" . $user->password . "'";
            $consulta = DB::select('UPDATE users SET password =' . $password . ' WHERE name =' . $nombre);
            return Response::json(array('success' => true, 'mensaje' => "Se cambio la contraseña con éxito" + $consulta));
        } else {
            return Response::json(array('error' => true, 'mensaje' => "Hubo un error " + $consulta));
        }        
    }

    public function getUsertypes() {
        return Response::json(array('usertypes' => Usertype::all()));
    }

    public function getConfigprofile(){
        
    }
    
    public function postUpdateinfouser(){
        $data = Input::all();
        $sql = "update gs_users set "        
                . "email = '" .$data["email"]
                . "', username = '" .$data["username"]
                . "' where id = " . $data["user_id"];        
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La informacion ha sido actualizada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede actualizar la información. Contacte al administrador de sistema. " . $e, 'error' => true, 'success' => false));
        }
    }
    
    public function postUpdatepassword(){
        $data = Input::all();
        $sql = "update gs_users set "        
                . "password = md5('" .$data["password"]                
                . "') where id = " . $data["user_id"]; 
        
        try {
            DB::beginTransaction();
            DB::update($sql);
            DB::commit();
            return Response::json(array('success' => true, 'mensaje' => "La contraseña ha sido actualizada con éxito."));
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('mensaje' => "No se puede actualizar la contraseña. Contacte al administrador de sistema. " . $e, 'error' => true, 'success' => false));
        }
    }
    
}
