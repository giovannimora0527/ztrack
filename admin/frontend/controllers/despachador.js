/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
angular.module('ztrack').controller('AdminDespachosController', function ($rootScope, $scope, $filter, AuthService, SessionService, $state, QueriesService, toastr) {
    
    $scope.title = "Gestión de Despachadores";
    $scope.activeTab = 1;    
    $scope.hasSelected = false;
    getFechahoy();


    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 2) {
            cargarDespachadores();
            cargarAreas();
        }
    };
    
    function getFechahoy(){
       var str = "" + new Date();
       str = str.slice(3,15);
       //$scope.fechaptos = str;      
    }

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

    $scope.guardarDespachador = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            username: $scope.despachador.username,
            password: $scope.despachador.password,
            nombre: $scope.despachador.nombre,
            apellidos: $scope.despachador.apellidos,
            telefono: $scope.despachador.telefono,
            direccion: $scope.despachador.direccion
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachador/despachador', null, $params)
                .then(function (result) {
                    if (result.error) {
                        toastr.error(result.mensaje);
                        $scope.despachador.username = "";
                    } else {
                        toastr.success(result.mensaje, 'Éxito');
                        $scope.despachador = {};
                    }

                });
    };

    function cargarDespachadores() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachos/despachadoresbyuserid', null, $params)
                .then(function (result) {
                    $scope.despachadores = result.despachadores;
                });
    }
    ;

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
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = result.rutas;
                    toastr.success("Rutas cargadas con éxito.", "OK");
                });
    }
    ;

    $scope.itemsselected = [];
    $scope.seleccionarRuta = function () {
        if($scope.rutaselect === undefined){
            toastr.error("Debe seleccionar una ruta para continuar. Intente de nuevo","Error");
            return;
        }
        if ($scope.itemsselected.length === 0) {
            $scope.itemsselected.push({
                route_id: $scope.rutaselect.route_id,
                route_name: $scope.rutaselect.route_name
            });
            toastr.success("Ruta preseleccionada con exito.", "Exito");
        } else {
            var esta = false;
            for (var i = 0; i < $scope.itemsselected.length; i++) {
                if (parseInt($scope.itemsselected[i].route_id) === parseInt($scope.rutaselect.route_id)) {
                    esta = true;
                }
            }
            if (!esta) {
                $scope.itemsselected.push({
                    route_id: $scope.rutaselect.route_id,
                    route_name: $scope.rutaselect.route_name
                });
            } else {
                toastr.warning("La ruta ya se encuentra preseleccionada. Intente de nuevo con otra.", "Atención");
            }
        }
    };

    $scope.deleteRuta = function (item) {
        $scope.itemsselected.splice($scope.itemsselected.indexOf(item), 1);
    };


    $scope.asignarRuta = function () {        
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id,
            despachador_id: $scope.despachadorselect.id                      
        };                
        QueriesService.executeRequest('POST', '../laravel/public/rutas/saveasignacionrutas', $scope.itemsselected, $params)
                .then(function (result) {
                    if(!result.success){
                       toastr.error("No se ha podido guardar el registro. Intente de nuevo.","Error");
                    }
                    else{
                        $scope.itemsselected = [];
                        $scope.despachadorselect = {}; 
                        document.getElementById("selectDespachador").disabled = false;
                    }                    
                });
    };

    $scope.cancelar = function () {
        $scope.itemsselected = [];
    };



});


