/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('HomeController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {

    $scope.data = SessionService.getInfo();
    $scope.title = "Principal";
    toastr.success("Bienvenido a ZTrack : " + $scope.data.user.name);
    getCantidadVehiculos();
    getCantidadConductores();
    getVehiculosEnRuta();
    getVehiculosEnParqueadero();
    getCantidadRutas();
    getCantidadDespachadores();
    getTiempoPromedio();

    function getCantidadVehiculos() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/countvehiculos', null, $params)
                .then(function (result) {
                    $scope.cantvehiculos = result.resultado;
                });
    }
    
    function getCantidadConductores() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/countconductores', null, $params)
                .then(function (result) {
                    $scope.conductores = result.resultado;
                });
    }
    
    function getVehiculosEnRuta() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/vehiculosenruta', null, $params)
                .then(function (result) {
                    $scope.enruta = result.resultado;
                });
    }
    
    function getVehiculosEnParqueadero() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/vehiculosenparqueadero', null, $params)
                .then(function (result) {
                    $scope.enparqueadero = result.resultado;
                });
    }
    
    function getCantidadRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/cantidadrutas', null, $params)
                .then(function (result) {
                    $scope.totalrutas = result.resultado;
                });
    }
    
    function getCantidadDespachadores() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/cantidaddespachadores', null, $params)
                .then(function (result) {
                    $scope.totaldesp = result.resultado;
                });
    }
    
    function getTiempoPromedio() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/reportes/tiempopromedio', null, $params)
                .then(function (result) {
                    $scope.promedio = result.resultado;
                });
    }
    
    $scope.pruebas = function(){
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/pruebas/databases', null, $params)
                .then(function (result) {
                    toastr.success(result.mensaje,"OK");
                }); 
    };

});


