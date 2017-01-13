/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('PerfilController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Perfil de Usuario";

    cargarDataPerfil();

    function cargarDataPerfil() {
        var perfil = localStorage['ztrack.perfil'];
        if (parseInt(perfil) === 3) {
            $params = {
                user_id: localStorage['ztrack.despachador_id']
            };
        }
        if (parseInt(perfil) === 1) {
            $params = {
                user_id: localStorage['ztrack.user_id']
            };
        }
        QueriesService.executeRequest('GET', '../laravel/public/usuario/user', null, $params)
                .then(function (result) {
                    $scope.user = result.user;
                });
    }

    $scope.actualizarDatos = function () {
        if ($scope.editinfo === undefined) {
            toastr.warning("Los campos son obligatorios", "Advertencia");
            return;
        }
        if ($scope.editinfo.usuario === undefined || $scope.editinfo.usuario === "") {
            toastr.warning("El campo nombre de usuario es obligatorio", "Advertencia");
            return;
        }
        if ($scope.editinfo.email === undefined || $scope.editinfo.email === "") {
            toastr.warning("El campo email es obligatorio", "Advertencia");
            return;
        }
        var perfil = localStorage['ztrack.perfil'];
        if (parseInt(perfil) === 3) {
            $params = {
                user_id: localStorage['ztrack.despachador_id'],
                username: $scope.editinfo.usuario,
                email: $scope.editinfo.email
            };
        }
        if (parseInt(perfil) === 1) {
            $params = {
                user_id: localStorage['ztrack.user_id'],
                username: $scope.editinfo.usuario,
                email: $scope.editinfo.email
            };
        }
        
        QueriesService.executeRequest('POST', '../laravel/public/usuario/updateinfouser', $params, null)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        $('#editarInfo').modal('hide');
                        $scope.limpiarCampos();
                        cargarDataPerfil();
                    } else {
                        toastr.error(result.mensaje, "Error");
                        $scope.limpiarCampos();
                    }
                });
    };


    $scope.limpiarCampos = function () {
        $scope.editinfo = {};
        $scope.info = {};
    };

    $scope.updatepassword = function () {
        if ($scope.info.password === undefined || $scope.info.password === "") {
            toastr.warning("El campo contraseña es obligatorio", "Advertencia");
            return;
        }
        var perfil = localStorage['ztrack.perfil'];
        if (parseInt(perfil) === 3) {
            $params = {
                user_id: localStorage['ztrack.despachador_id'],
                password: $scope.info.password
            };
        }
        if (parseInt(perfil) === 1) {
            $params = {
                user_id: localStorage['ztrack.user_id'],
                password: $scope.info.password
            };
        }       
        QueriesService.executeRequest('POST', '../laravel/public/usuario/updatepassword', $params, null)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        $('#editarPass').modal('hide');
                        $scope.limpiarCampos();
                    } else {
                        toastr.error(result.mensaje, "Error");
                        $scope.limpiarCampos();
                    }
                });
    };

});


