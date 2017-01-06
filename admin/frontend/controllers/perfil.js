/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('PerfilController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService) {
    $scope.title = "Perfil de Usuario";

    cargarDataPerfil();

    function cargarDataPerfil() {
        var perfil = localStorage['ztrack.perfil'];
        console.log(perfil);
        if (parseInt(perfil) === 3) {            
            $params = {
                user_id : localStorage['ztrack.despachador_id']
            };
        }
        if(parseInt(perfil) === 1){           
           $params = {
                user_id : localStorage['ztrack.user_id']
            }; 
        }        
        QueriesService.executeRequest('GET', '../laravel/public/usuario/user', null, $params)
                .then(function (result) {
                    $scope.user = result.user;
                });

    }

});


