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
        codigo: "",
        direccion: "",
        telefono: "",
        email: "",
        descripcion: "",
        user_id: localStorage['ztrack.user_id']
    };
    $scope.hayConductores = false;
    var min = 0;
    var max = 10;
    $scope.maxcount = 0;
    $scope.paginationtab = false;
    $scope.pag = 1;
    getConductoresInfo();


    $scope.activeTab = 1;
    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {
            getConductoresInfo();
        }
        if (tab === 2) {
            $scope.filtro = {};
            getRutas();
        }
    };

    function getRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutas', null, $params)
                .then(function (result) {
                    $scope.rutas = result.rutas;
                });
    }


    function getConductoresInfo() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/conductores/conductores', null, $params)
                .then(function (result) {
                    $scope.conductores = result.conductores;
                    if ($scope.conductores.length > 0) {
                        $scope.hayConductores = true;
                    }
                });
    }

    $scope.guardarConductor = function () {
        if ($scope.conductor.nombres === "" || $scope.conductor.direccion === "" || $scope.conductor.identificacion === "" || $scope.conductor.telefono === "") {
            toastr.warning("Hay campos vacios presentes en el formulario que son obligatorios. Revise e intente de nuevo. ", "Advertencia");
            return;
        }
        if (isNaN($scope.conductor.identificacion)) {
            toastr.warning("El campo Número de Identifcación debe contener solo valores numéricos. ", "Advertencia");
            return;
        }
        if (isNaN($scope.conductor.telefono)) {
            toastr.warning("El campo Teléfono debe contener solo valores numéricos. ", "Advertencia");
            return;
        }
        QueriesService.executeRequest('POST', '../laravel/public/conductores/saveconductor', $scope.conductor, null)
                .then(function (result) {
                    if (result.success) {
                        $('#agregarConductor').modal('hide');
                        getConductoresInfo();
                        toastr.success(result.mensaje, "OK");
                    } else {
                        toastr.error(result.mensaje, "Error");
                    }
                });
    };

    $scope.cargarModal = function (data) {
        $scope.conductorseleccionado = {
            driver_idn : data.driver_idn,
            driver_name : data.driver_name,
            driver_assign_id : data.driver_assign_id,
            driver_address : data.driver_address,
            driver_phone : data.driver_phone,
            driver_email : data.driver_email,
            driver_desc : data.driver_desc,
            driver_id : data.driver_id
        };
    };

    $scope.actualizarConductor = function () {
        if ($scope.conductorseleccionado.driver_name === "" || $scope.conductorseleccionado.driver_address === "" || $scope.conductorseleccionado.driver_idn === "" || $scope.conductorseleccionado.driver_phone === "") {
            toastr.warning("Hay campos vacios presentes en el formulario que son obligatorios. Revise e intente de nuevo. ", "Advertencia");
            return;
        }
        if (isNaN($scope.conductorseleccionado.driver_idn)) {
            toastr.warning("El campo Número de Identifcación debe contener solo valores numéricos. ", "Advertencia");
            return;
        }
        if (isNaN($scope.conductorseleccionado.driver_phone)) {
            toastr.warning("El campo Teléfono debe contener solo valores numéricos. ", "Advertencia");
            return;
        }
        QueriesService.executeRequest('POST', '../laravel/public/conductores/updateconductor', $scope.conductorseleccionado, null)
                .then(function (result) {
                    if (result.success) {
                        if($scope.activeTab === 1){                            
                            getConductoresInfo(); 
                        }
                        if($scope.activeTab === 2){                                                      
                            $('#verInfo').modal('hide');
                            //$scope.filtro = $scope.conductorseleccionado;
                            if($scope.filtro.nombreconductor === $scope.conductorseleccionado.driver_name || $scope.filtro.documento === $scope.conductorseleccionado.driver_idn || $scope.filtro.telefono === $scope.conductorseleccionado.driver_phone ){
                               buscarFiltro();   
                            }
                            else{                               
                                $scope.limpiarFiltros();
                            }
                           
                        }
                        toastr.success(result.mensaje, "OK");
                    } 
                });
    };

    $scope.onconfirmdeleteconductor = function (conductor_id) {
        var rta = confirm("¿Desea eliminar el registro?");
        if (rta) {
            $scope.eliminarconductor(conductor_id);
        } else {
            return;
        }
    };

    $scope.eliminarconductor = function (conductor_id) {
        $params = {
            conductor_id: conductor_id
        };
        QueriesService.executeRequest('POST', '../laravel/public/conductores/deleteconductor', $params, null)
                .then(function (result) {
                    if (result.success) {
                        getConductoresInfo();
                        toastr.success(result.mensaje, "OK");
                    } else {
                        toastr.error(result.mensaje, "Error");
                    }
                });
    };
    
    function buscarFiltro(){        
        $scope.buscarConductor();
    }


    $scope.buscarConductor = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            min: min,
            max: max
        };        
        QueriesService.executeRequest('POST', '../laravel/public/conductores/searchconductor', $scope.filtro, $params)
                .then(function (result) {
                    if (result.success) {
                        $scope.conductoresfiltro = result.conductores;
                        if ($scope.conductoresfiltro.length === 0)
                        {
                            $scope.resultsfound = false;
                            $scope.paginationtab = false;
                            $scope.limpiarFiltros();                            
                            toastr.warning("No se encontraron resultados con el criterio de búsqueda. Intente de nuevo.", "Advertencia");
                        }
                        $scope.resultsfound = true;
                        $scope.paginationtab = result.moreresults;                        
                        $scope.minlabel = min;
                        $scope.maxlabel = max;
                        $scope.maxcount = parseInt(result.count);
                        if ($scope.maxcount > max) {                            
                            $scope.paginationtab = true;
                        } else {
                            $scope.paginationtab = false;
                        }
                    }
                });
    };
    
    $scope.paginationtabant = function () {
        if ($scope.minlabel === 0) {
            toastr.warning("Ya se encuentra en la primera página de los resultados", "Advertencia");
            return;
        }
        if ((min - 10) <= 0) {
            min = 0;
        } else {
            min = ((min) - 10);
        }
        max = (max - 10);
        $scope.pag = $scope.pag - 1;
        buscarFiltro();
    };

    $scope.paginationtabsig = function () {
        if ($scope.maxlabel > parseInt($scope.maxcount)) {
            toastr.warning("Ya se encuentra en la última página de los resultados", "Advertencia");
            return;
        }
        min = max;
        max = (max) + 10;
        $scope.pag = $scope.pag + 1;
        buscarFiltro();
    };

    $scope.limpiarFiltros = function () {
        $scope.filtro = {};
        $scope.conductoresfiltro = {};
    };


});


