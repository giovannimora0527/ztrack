/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('NovedadesAdminController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Registro de Novedades";
    $scope.activeTab = 1;
    cargarNovedades();

    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
    };

    function cargarNovedades() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/novedades/novedadesadmin', null, $params)
                .then(function (result) {
                    $scope.novedadesregistradas = result.novedadesregistradas;
                });
    }

    $scope.registrarNovedad = function () {
        if ($scope.novedad === undefined || $scope.novedad === "") {
            toastr.warning("El campo nombre es obligatorio", "Advertencia");
            return;
        }
        if ($scope.novedad.tipo === undefined) {
            toastr.warning("Debe seleccionar el tipo de novedad a asignar.", "Advertencia");
            return;
        }
       
        $params = {
            descripcion: $scope.novedad.nombre,
            user_id: localStorage['ztrack.user_id'],
            tipo : $scope.novedad.tipo
        };
        QueriesService.executeRequest('POST', '../laravel/public/novedades/novedadnueva', null, $params)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        cargarNovedades();
                    } else {
                        toastr.error(result.mensaje, "Error");
                    }
                });

    };


    $scope.cargarModalEditar = function (novedad) {        
        $scope.novedadselect = {
            id: novedad.novedad_id,
            descripcion: novedad.descripcion
        };
    };

    $scope.updateNovedad = function () {
        if ($scope.novedadselect.descripcion === "") {
            toastr.warning("El campo nombre no puede estar vacio. Intente de nuevo.", "Advertencia");
            return;
        }
        $params = {
            descripcion: $scope.novedadselect.descripcion,
            novedad_id: $scope.novedadselect.id,
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('POST', '../laravel/public/novedades/updatenovedadadmin', null, $params)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        cargarNovedades();
                        $('#editarNovedad').modal('hide');
                    } else {
                        toastr.error(result.mensaje, "Error");
                    }
                });
    };

    $scope.onconfirmdelete = function (it) {
        var rta = confirm("¿Desea eliminar el registro?");
        if (rta) {
            $scope.deletenovedad(it);
        } else {
            return;
        }
    };

    $scope.deletenovedad = function (it) {        
        $params = {            
            novedad_id: it.novedad_id            
        };
        QueriesService.executeRequest('POST', '../laravel/public/novedades/deletenovedad', null, $params)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        cargarNovedades();                        
                    } else {
                        toastr.error(result.mensaje, "Error");
                    }
                });
    };   
    
   

});



