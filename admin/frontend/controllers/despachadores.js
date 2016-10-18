/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('DespachadoresController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {
    $scope.title = "Despachos";    
    $scope.activeTab = 1;
    $scope.despachador = {};
    cargarRutas();

    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {
            $scope.limpiarCampos();
        }
        if (tab === 2) {
            cargarAllVehiculos();
            cargarVehiculosDespachados();
        }
        if (tab === 3) {
            cargarVehiculosDespachados();
            cargarVehiculosLLegada();
        }
    };

    $scope.dateTimeNow = function () {
        $scope.date = new Date();
    };
    $scope.dateTimeNow();

    $scope.toggleMinDate = function () {
        var minDate = new Date();
        // set to yesterday
        minDate.setDate(minDate.getDate() - 1);
        $scope.dateOptions.minDate = $scope.dateOptions.minDate ? null : minDate;
    };

    $scope.dateOptions = {
        showWeeks: false,
        startingDay: 0
    };

    $scope.toggleMinDate();

    // Disable weekend selection
    $scope.disabled = function (calendarDate, mode) {
        return mode === 'day' && (calendarDate.getDay() === 0 || calendarDate.getDay() === 6);
    };

    $scope.open = function ($event, opened) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.dateOpened = true;
    };

    $scope.dateOpened = false;
    $scope.hourStep = 1;
    $scope.format = "dd-MMM-yyyy";
    $scope.minuteStep = 15;
    // add min-time="minTime" to datetimepicker to use this value 
    $scope.minTime = new Date(0, 0, 0, Math.max(1, $scope.date.getHours() - 2), 0, 0, 0);

    $scope.timeOptions = {
        hourStep: [1, 2, 3],
        minuteStep: [1, 5, 10, 15, 25, 30]
    };

    $scope.showMeridian = true;
    $scope.timeToggleMode = function () {
        $scope.showMeridian = !$scope.showMeridian;
    };

    $scope.$watch("date", function (date) {
        // read date value
    }, true);

    $scope.resetHours = function () {
        $scope.date.setHours(1);
    };



    function cargarRutas() {
        $params = {
            user_id: localStorage['ztrack.despachador_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasdespachador', null, $params)
                .then(function (result) {
                    $scope.rutas = result.rutas;
                });
    }
    ;

    $scope.cargarGrupos = function () {
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            route_id: $scope.rutaselect.route_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/gruposvehiculosbyrutasid', null, $params)
                .then(function (result) {
                    $scope.grupos = result.grupos;
                });
    };

    $scope.cargarVehiculos = function () {
        cargarVehiculosParadero();
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            group_id: $scope.gruposelect.group_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/vehiculosbygroupid', null, $params)
                .then(function (result) {
                    if (result.empty) {
                        toastr.warning(result.mensaje, "Advertencia");
                        return;
                    }
                    $scope.vehiculos = result.vehiculos;
                    toastr.success("Vehículos cargados con éxito.", "OK");
                });
        document.getElementById("selectvehiculos").disabled = true;
        document.getElementById("selectruta").disabled = true;

    };

    $scope.limpiarCampos = function () {
        document.getElementById("selectvehiculos").disabled = false;
        document.getElementById("selectruta").disabled = false;
        $scope.vehiculos = [];
        $scope.vehiculosparadero = [];
    };


    $scope.adicionarVehiculo = function () {
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            vehiculo_id: $scope.vehiculoselect.object_id,
            group_id: $scope.gruposelect.group_id,
            route_id: $scope.rutaselect.route_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/vehiculosparadero', null, $params)
                .then(function (result) {
                    if (result.success) {
                        toastr.success(result.mensaje, "OK");
                        cargarVehiculosParadero();
                    } else {
                        toastr.warning(result.mensaje, "OK");
                    }
                });
    };

    function cargarVehiculosParadero() {
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            group_id: $scope.gruposelect.group_id,
            route_id: $scope.rutaselect.route_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/vehiculosdisponibles', null, $params)
                .then(function (result) {
                    $scope.vehiculosparadero = result.vehiculosparadero;
                });
    }

    function cargarAllVehiculos() {
        $params = {
            user_id: localStorage['ztrack.despachador_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/allvehiculos', null, $params)
                .then(function (result) {
                    $scope.vehiculos = result.vehiculosparadero;
                });
    }

    $scope.cargarDatosModal = function (it) {        
        $params = {
            vehiculo_id: it.object_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/infovehiculo', null, $params)
                .then(function (result) {
                    $scope.vehiculo = result.vehiculo;
                });
    };

    $scope.despachar = function (it) {
        document.getElementById("btndespachar").disabled = true;
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            imei: it.imei,
            object_id: it.object_id,
            ruta_id: it.ruta_id,
            group_id: it.group_id,
            coordenadas: it.coordenadas
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/despachovehiculo', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarAllVehiculos();
                        cargarVehiculosDespachados(); 
                        document.getElementById("btndespachar").disabled = false;
                        toastr.success("Vehículo despachado con éxito.","OK");
                    }
                });
    };

    function cargarVehiculosDespachados() {
        $params = {
            user_id: localStorage['ztrack.despachador_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/vehiculosdespachados', null, $params)
                .then(function (result) {
                    $scope.vehiculosdespachados = result.vehiculosdespachados;
                });
    }

    $scope.cargarDatosInfo = function (it) {
        $scope.info = it;
    };

    $scope.registrarLlegada = function (it) {
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            despacho_id: it.despacho_id,
            imei: it.imei,
            object_id: it.object_id,
            route_id: it.ruta_id,
            group_id: it.group_id,
            vuelta: it.vuelta
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/llegadavehiculo', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarVehiculosDespachados();
                        cargarAllVehiculos();
                        cargarVehiculosLLegada();
                    }
                });
    };

    function cargarVehiculosLLegada() {
        $params = {
            user_id: localStorage['ztrack.despachador_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/llegadavehiculos', null, $params)
                .then(function (result) {
                    if (result.success) {
                        $scope.llegadavehiculos = result.llegadavehiculos;
                    }
                });
    }

    $scope.verInfoLlegada = function (item) {
        $params = {
            user_id: localStorage['ztrack.despachador_id'],
            object_id: item.object_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/historialrecorrido', null, $params)
                .then(function (result) {
                    if (result.success) {
                        $scope.estadistica = result.estadistica;
                        $scope.conductor = result.conductor;
                        $scope.vueltas = result.vueltas;
                    }
                });
    };


});


