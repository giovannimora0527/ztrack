/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var ztrack = angular.module('ztrack');
ztrack.controller('GruposController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Gestión de Grupos";
    $scope.activeTab = 1;
    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {
            getGrupos();
        }
    };
    $scope.grupo = {};
    $scope.resultados = {};
    $scope.hayGrupos = false;
    getGrupos();

    function getGrupos() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/grupos/grupos', null, $params)
                .then(function (result) {
                    $scope.grupos = result.grupos;
                    if ($scope.grupos.length > 0) {
                        $scope.hayGrupos = true;
                    } else {
                        $scope.hayGrupos = false;
                    }
                });
    }
    ;


    $scope.guardarGrupo = function () {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        if ($scope.grupo.nombre === undefined) {
            toastr.warning("Debe escribir el nombre del grupo para poder continuar. Intente de nuevo.", "Advertencia");
            return;
        }
        QueriesService.executeRequest('POST', '../laravel/public/grupos/savegrupo', $scope.grupo, $params)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        getGrupos();
                    } else {
                        toastr.error(result.mensaje, "Error");
                    }
                });
    };

    $scope.buscarGrupo = function () {        
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('POST', '../laravel/public/grupos/searchgrupo', $scope.filtro, $params)
                .then(function (result) {                    
                        $scope.resultados = result.resultados;
                        if ($scope.resultados.length === 0){
                            toastr.warning("No se encontraron resultados con los criterios de búsqueda. Intente de nuevo.","Advertencia");
                            $scope.filtro = {};
                        }
                });
    };

    $scope.limpiarFiltros = function () {
        $scope.filtro = {};
        $scope.resultados = {};
    };

    $scope.actualizarGrupo = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            group_id : $scope.gruposelect.group_id
        };        
        QueriesService.executeRequest('POST', '../laravel/public/grupos/actualizargrupo', $scope.gruposelect, $params)
                .then(function (result) {                    
                      if(result.success){
                         toastr.success(result.mensaje,"OK");
                         $scope.buscarGrupo(); 
                      } 
                      else{
                         toastr.error(result.mensaje,"Error"); 
                      }
                });
    };
    
    $scope.cargarModal = function(item){
       $scope.gruposelect = {
           group_id : item.group_id,
           group_name : item.group_name,
           group_desc : item.group_desc
       }; 
    };

    $scope.borrarGrupo = function () {
       
    };

});


