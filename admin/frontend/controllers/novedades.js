/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('NovedadesController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Registro de Novedades";
    $scope.activeTab = 1;
    cargarAreas();

    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {

        }
        if (tab === 2) {

        }
        if (tab === 3) {

        }

    };

    function cargarAreas() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/grupos/cargarareas', null, $params)
                .then(function (result) {
                    $scope.areas = result.areas;
                });
    }
    ;

    $scope.onChangeSelectedArea = function () {
        cargarRutas();
    };

    function cargarRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = {};
                    $scope.rutas = result.rutas;
                });
    }

    $scope.cargarVehiculos = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            ruta_id: $scope.rutaselect.route_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/novedades/vehiculos', null, $params)
                .then(function (result) {
                    $scope.vehiculos = {};
                    $scope.vehiculos = result.vehiculos;
                });
    };
    
    $scope.cargarModal = function(veh){
      $scope.vehiculo = veh;  
      $scope.cargarNovedades();
    };
    
    $scope.cargarNovedades = function(){
      $params = {
            user_id: localStorage['ztrack.user_id']           
        };  
    };
    
    

});

