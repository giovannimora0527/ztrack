/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('ReportesController', function ($rootScope, $scope, $state, QueriesService, toastr) {   
   $scope.title = "Reportes";
   $scope.activeTab = 1; 
   $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab; 
        if(tab === 2 || tab === 3){
          cargarAreas();
        }
    };    
    cargarAreas();
    
    
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
    
    $scope.cargarRutasByArea = function (){
         $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };        
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = {};
                    $scope.rutas = result.rutas;
                });
    };
    
});


