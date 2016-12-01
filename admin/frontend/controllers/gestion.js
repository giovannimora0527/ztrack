/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('GestionController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Gestión de Vehículos";
    $scope.activeTab = 1;
    $scope.vehiculo = {
        nombre: "",
        imei: "",
        conductor: null
    };
    $scope.filter = {
        placa: "",
        numvehiculo: "",
        conductor: "",
        ruta: ""
    };
    var min = 0;
    var max = 10;
    $scope.maxcount = 0;
    $scope.paginationtab = false;
    $scope.pag = 1;
    $scope.resultsfound = false;


    cargarListadoConductores();


    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {
            cargarListadoConductores();
        }
        if (tab === 2) {
            cargarListadoConductoresAsignados();
            cargarListadoRutas();
        }
    };

    function cargarListadoRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/allinforutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = result.rutas;
                });
    }

    function cargarListadoConductoresAsignados() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/conductores/conductoresasignados', null, $params)
                .then(function (result) {
                    $scope.conductoresasignados = result.conductores;
                });
    }

    function cargarListadoConductores() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/conductores/conductores', null, $params)
                .then(function (result) {
                    $scope.conductores = result.conductores;
                });
    }

    $scope.nuevo = function () {
        $scope.vehiculo = {
            nombre: "",
            imei: "",
            conductor: null
        };
    };

    $scope.guardarVehiculo = function () {
        if ($scope.vehiculo.imei.length < 15) {
            toastr.warning("El IMEI no puede tener menos de 15 dígitos. Intente de nuevo.", "Advertencia");
            return;
        }
        if (isNaN($scope.vehiculo.imei)) {
            toastr.warning("El campo IMEI debe contener solo valores numéricos. ", "Advertencia");
            return;
        }
        if ($scope.vehiculo.nombre === "") {
            toastr.warning("El campo Nombre del Vehículo no puede quedar vacío. Intente de nuevo.", "Advertencia");
            return;
        }
        if ($scope.vehiculo.conductor === null) {
            toastr.warning("Debe asignar un conductor al vehículo para poder continuar. Intente de nuevo.", "Advertencia");
            return;
        }

        $params = {
            nombre: $scope.vehiculo.nombre,
            imei: $scope.vehiculo.imei,
            conductor_id: $scope.vehiculo.conductor.driver_id,
            user_id: localStorage['ztrack.user_id']
        };

        QueriesService.executeRequest('POST', '../laravel/public/vehiculos/savevehiculo', $params, null)
                .then(function (result) {
                    if (result.success) {
                        $scope.nuevo();
                        cargarListadoConductores();
                    }
                });
    };
    
    function cargarConductores(){
      $scope.filtrarVehiculo();  
    }

    $scope.filtrarVehiculo = function () {
        var hayFiltros = false;
        if ($scope.filter.conductor === "" && $scope.filter.placa === "" && $scope.filter.numvehiculo === "" && $scope.filter.ruta === "") {
            hayFiltros = false;
        }
        if ($scope.filter.conductor !== "" || $scope.filter.placa !== "" || $scope.filter.numvehiculo !== "" || $scope.filter.ruta !== "") {
            hayFiltros = true;
        }
        if (hayFiltros) {
            $params = {
                hasFiltros: true,
                user_id: localStorage['ztrack.user_id'],
                min: min,
                max: max
            };
        } else {
            $params = {
                hasFiltros: false,
                min: min,
                max: max,
                user_id: localStorage['ztrack.user_id']
            };
        }
        QueriesService.executeRequest('POST', '../laravel/public/vehiculos/filtrarvehiculo', $scope.filter, $params)
                .then(function (result) {
                    if (result.success === true) {
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
                        $scope.vehiculosresultados = result.vehiculos;
                    } else {
                        $scope.resultsfound = false;
                        $scope.paginationtab = false;
                    }
                });
    };
    
    $scope.pagant = function () {
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
        cargarConductores();
    };

    $scope.pagsig = function () {
        if ($scope.maxlabel > parseInt($scope.maxcount)) {
            toastr.warning("Ya se encuentra en la última página de los resultados", "Advertencia");
            return;
        }
        min = max;
        max = (max) + 10;
        $scope.pag = $scope.pag + 1;
        cargarConductores();
    };

    $scope.limpiarCampos = function () {
        $scope.filter = {
            placa: "",
            numvehiculo: "",
            conductor: "",
            ruta: ""
        };
        min = 0;
        max = 10;
        $scope.maxcount = 0;
        $scope.paginationtab = false;
        $scope.pag = 1;
        $scope.resultsfound = false;
        $scope.vehiculosresultados = {};
    };

});


