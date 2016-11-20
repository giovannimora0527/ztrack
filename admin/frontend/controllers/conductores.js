/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('GestionConductorController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Gestión de Conductores";

    $scope.conductor = {
        nombres: "",
        identificacion: "",
        direccion: "",
        telefono: "",
        email: "",
        descripcion: "",
        user_id : localStorage['ztrack.user_id']
    };
    $scope.hayConductores = false;
    getConductoresInfo();


    $scope.activeTab = 1;
    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if(tab === 1){
            getConductoresInfo();
        }
    };

    function getConductoresInfo() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/conductores/conductores', null, $params)
                .then(function (result) {
                    $scope.conductores = result.conductores;
                    if ($scope.conductores.length > 0){
                        $scope.hayConductores = true;
                    }
                });
    }




    $scope.guardarConductor = function () {
        console.log($scope.conductor);
        if ($scope.conductor.nombres === "" || $scope.conductor.direccion === "" || $scope.conductor.identificacion === "" || $scope.conductor.telefono === "") {
            toastr.warning("Hay campos vacios presentes en el formulario que son obligatorios. Revise e intente de nuevo. ", "Advertencia");
            return;
        }        
        if(isNaN($scope.conductor.identificacion)){
           toastr.warning("El campo Número de Identifcación debe contener solo valores numéricos. ", "Advertencia");  
           return;
        }        
        if(isNaN($scope.conductor.telefono)){
           toastr.warning("El campo Teléfono debe contener solo valores numéricos. ", "Advertencia");  
           return;
        }
        QueriesService.executeRequest('POST', '../laravel/public/conductores/saveconductor', $scope.conductor, null)
                .then(function (result) {
                    getConductoresInfo();  
                });
    };


});


