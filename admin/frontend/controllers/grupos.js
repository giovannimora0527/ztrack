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
        if(tab === 1){
           getGrupos(); 
        }
    };    
    $scope.grupo = {};
    $scope.hayGrupos = false; 
    getGrupos();
    
    function getGrupos() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/grupos/grupos', null, $params)
                .then(function (result) {
                    $scope.grupos = result.grupos;
                    if($scope.grupos.length > 0){
                      $scope.hayGrupos = true;  
                    }
                    else{
                      $scope.hayGrupos = false;    
                    }
                });
    }
    ;
    
    
    $scope.guardarGrupo = function(){
       $params = {
            user_id: localStorage['ztrack.user_id']
        };
        if($scope.grupo.nombre === undefined){
           toastr.warning("Debe escribir el nombre del grupo para poder continuar. Intente de nuevo.","Advertencia"); 
           return;
        }
        QueriesService.executeRequest('POST', '../laravel/public/grupos/savegrupo', $scope.grupo, $params)
                .then(function (result) {
                    if(result.success){
                       getGrupos(); 
                    }
                });
    };    
    
    $scope.actualizarGrupo = function(){
        
    };
    
    $scope.borrarGrupo = function(){
        
    };

});


