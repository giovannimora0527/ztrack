/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('DespachosController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {

    cargarAreas();
    cargarGrupos();
    cargarGruposRutas();
    $scope.areaselect = {};
    $scope.activeTab = 1;
    $scope.resultsfound = false;
    $scope.resultsfound2 = false;
    $scope.resultsfound3 = false;
    $scope.gruporuta = {};
    $scope.bntdisabled = true;
    $scope.cargoVehiculos = false;
    $scope.asignacion = {};
    $scope.onChangeVehiculos = false;
    $scope.onChangeGrupos = false;
    $scope.seleccionaGrupo = false;
    $scope.seleccionaVehiculos = false;
    $scope.selectGrupos = false;
    $scope.gruposelect = false;
    $scope.rutaselect = false;
    $scope.selectvehiculo = false;
    $scope.asign = {};


    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {
            cargarAreas();
            cargarGrupos();
            cargarGruposRutas();
        }
        if (tab === 2) {
            cargarVehiculos();
            cargarAsignaciones();
        }
        if (tab === 3) {
            cargarConductores();
            cargarVehiculos();
            cargarAsignacionesConductores();
        }
    };

    $scope.despacho = {};
    $scope.clear = function () {
        $scope.dt = null;
    };

    $scope.toggleMin = function () {
        $scope.minDate = $scope.minDate ? null : new Date();
    };

    $scope.toggleMin();
    $scope.maxDate = new Date(2030, 12, 31);

    $scope.open1 = function () {
        $scope.popup1.opened = true;
    };

    $scope.open2 = function () {
        $scope.popup2.opened = true;
    };

    $scope.open3 = function () {
        $scope.popup3.opened = true;
    };

    $scope.open4 = function () {
        $scope.popup4.opened = true;
    };

    $scope.setDate = function (year, month, day) {
        $scope.dt = new Date(year, month, day);
    };

    $scope.idioma = ({
        dateFormat: "dd-mm-yy",
        firstDay: 1,
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames:
                ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
                    "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNamesShort:
                ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
                    "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
    });

    $scope.dateOptions = {
        formatYear: 'yy',
        language: $scope.idioma
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy-MM-dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[1];
    $scope.altInputFormats = ['M!/d!/yyyy'];

    $scope.popup1 = {
        opened: false
    };

    $scope.popup2 = {
        opened: false
    };

    $scope.popup3 = {
        opened: false
    };

    $scope.popup4 = {
        opened: false
    };

    function cargarGrupos() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/grupos/grupos', null, $params)
                .then(function (result) {
                    $scope.grupos = result.grupos;
                });
    }
    ;

    $scope.cargarRutas = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = result.rutas;
                });
    };

    function cargarGruposRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/gruposrutasbyid', null, $params)
                .then(function (result) {
                    $scope.gruposrutas = result.gruposrutas;
                    if (result.gruposrutas.length > 0) {
                        $scope.resultsfound = true;
                    } else {
                        $scope.resultsfound = false;
                    }

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

    $scope.asignarGrupos = function () {
        if (!$scope.gruposelect) {
            toastr.warning("Debe seleccionar un grupo para poder continuar");
            return;
        }
        if (!$scope.rutaselect) {
            toastr.warning("Debe seleccionar una ruta para poder continuar");
            return;
        }
        if ($scope.gruporuta.fechaini > $scope.gruporuta.fechafin) {
            toastr.warning("La fecha Desde no puede ser mayor que la fecha Hasta");
            return;
        }
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id,
            group_id : $scope.gruporuta.grupo,
            route_id : $scope.gruporuta.ruta,
            fechaini : $scope.gruporuta.fechaini,
            fechafin : $scope.gruporuta.fechafin
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/gruposrutas', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarGruposRutas();
                    }
                    else{
                        toastr.warning(result.mensaje);
                    }
                });
    };

    $scope.onChangeSelectedArea = function () {
        $scope.cargarRutas();
        $scope.bntdisabled = false;
    };

    $scope.editargruporuta = function (gr_id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            grId: gr_id
        };
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/editar', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarGruposRutas();
                    }
                });
    };

    $scope.onconfirmdeletegr = function (gr_id) {
        var rta = confirm("¿Desea eliminar el registro?");
        if (rta) {
            $scope.eliminargruporuta(gr_id);
        } else {
            return;
        }
    };


    $scope.eliminargruporuta = function (gr_id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            grId: gr_id
        };
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/eliminar', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarGruposRutas();
                    }
                });
    };

    $scope.seleccionarItem = function (gr_id, group_name, fini, ffin, areaid, area_name) {
        $scope.item = {
            id: gr_id,
            name: group_name,
            fechaini: fini,
            fechafin: ffin,
            area_name: area_name
        };
        $scope.cargarRutasModal(areaid);
        $scope.gruporutaedit = {};

    };

    $scope.seleccionarItem2 = function (vh_id, placa, groupname, conductor, imei, telefono, direccion) {
        $scope.item2 = {
            id: vh_id,
            name: placa,
            grupo: groupname,
            conductor: conductor,
            imei: imei,
            telefono: telefono,
            direccion: direccion
        };
    };
    $scope.seleccionarItem3 = function (dr_id, driver_name, driver_phone, driver_address) {
        $scope.item3 = {
            id: dr_id,
            name: driver_name,
            telefono: driver_phone,
            direccion: driver_address
        };
    };

    $scope.cargarRutasModal = function (area_id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: area_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = result.rutas;
                });
    };

    $scope.actualizargruporuta = function (id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            group_id: id,
            route_id: $scope.gruporutaedit.ruta.route_id,
            fechaini: $scope.gruporutaedit.fechaini,
            fechafin: $scope.gruporutaedit.fechafin
        };
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/editargruporuta', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarGruposRutas();
                    }
                });
    };

    function cargarVehiculos() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/vehiculos', null, $params)
                .then(function (result) {
                    $scope.vehiculos = result.vehiculos;
                });
    }
    ;

    $scope.asignarVehiculos = function () {
        if (!$scope.onChangeVehiculos) {
            toastr.warning("Debe seleccionar un vehiculo para continuar. Intente de nuevo.");
            return;
        }
        if (!$scope.onChangeGrupos) {
            toastr.warning("Debe seleccionar un grupo para continuar. Intente de nuevo.");
            return;
        }
        $params = {
            user_id: localStorage['ztrack.user_id'],
            group_id: $scope.asignacion.grupo.group_id,
            group_name: $scope.asignacion.grupo.group_name,
            vehiculo_id: $scope.asignacion.vehiculo
        };
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/asignacion', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarAsignaciones();
                    }
                });
    };

    function cargarAsignaciones() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/asignaciones', null, $params)
                .then(function (result) {
                    if (result.success) {
                        $scope.asignaciones = result.asignaciones;
                        $scope.resultsfound1 = true;
                    } else {
                        $scope.resultsfound1 = false;
                    }

                });
    }
    ;

    $scope.onconfirmdeletevh = function (vh_id) {
        var rta = confirm("¿Desea eliminar el registro?");
        if (rta) {
            $scope.eliminarAsignacion(vh_id);
        } else {
            return;
        }
    };

    $scope.eliminarAsignacion = function (vh_id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            vehiculo_id: vh_id
        };
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/eliminarasignacion', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarAsignaciones();
                    }
                });
    };

    $scope.actualizarvehiculo = function (id) {
        if (!$scope.seleccionaGrupo) {
            toastr.warning("Debe seleccionar el grupo para poder continuar. Intente de nuevo.");
            return;
        }
        $params = {
            vehiculo_id: id,
            group_id: $scope.asignacion.grupo.group_id
        };
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/asignacion', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarAsignaciones();
                    }
                });
    };

    function cargarConductores() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/conductores', null, $params)
                .then(function (result) {
                    $scope.conductores = result.conductores;
                });
    }

    $scope.asignarConductor = function () {
        if (!$scope.seleccionaVehiculos) {
            toastr.warning("Debe seleccionar el conductor para poder continuar. Intente de nuevo.");
            return;
        }
        if (!$scope.selectGrupos) {
            toastr.warning("Debe seleccionar el grupo para poder continuar. Intente de nuevo.");
            return;
        }
        $params = {
            user_id: localStorage['ztrack.user_id'],
            conductor_id: $scope.vehiculo.conductor,
            vehiculo_id: $scope.vehiculo.select.vehiculo_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/asignacionconductor', null, $params)
                .then(function (result) {
                    if (result.warning) {
                        toastr.warning(result.value);
                        return;
                    }
                    cargarAsignacionesConductores();
                });
    };

    function cargarAsignacionesConductores() {
        $params = {
            user_id: localStorage['ztrack.user_id']
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/asignacionconductores', null, $params)
                .then(function (result) {
                    $scope.asignacionconductores = result.asignaciones;
                    $scope.resultsfound3 = true;
                });
    }


    $scope.actualizarinformacion = function (id) {
        if (!$scope.selectvehiculo) {
            toastr.warning("Debe seleccionar un vehiculo para continuar.");
            return;
        }
        $params = {
            user_id: localStorage['ztrack.user_id'],
            vehiculo_id: $scope.asign.vehiculo,
            driver_id: id
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/updateinformacionconductores', null, $params)
                .then(function (result) {
                    if (result.warning) {
                        toastr.warning(result.value);
                        return;
                    }
                    cargarAsignacionesConductores();
                });
    };

});


