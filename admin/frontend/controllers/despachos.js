/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('DespachosController', function ($rootScope, $scope, $filter, AuthService, SessionService, $state, QueriesService, toastr) {

    $scope.title = "Despachos";
    cargarAreas();
    cargarGruposRutas();
    $scope.areaselect = {};
    $scope.activeTab = 1;
    $scope.activeTab2 = 5;
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
    $scope.areadespachos = {};
    $scope.despachadores = {};
    var min = 0;
    var max = 10;
    $scope.maxcount = 0;
    $scope.paginationtab2 = false;
    $scope.pag = 1;
    $scope.firstcharge = false;
    $scope.hasFiltrosTab2 = false;
    $scope.hasFiltrosTab2 = false;
    $scope.filtros = {
        placa: "",
        vehiculo: "",
        conductor: ""
    };
    $scope.puntosdecontrol = {};

    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 1) {
            cargarAreas();
            cargarGrupos();
            min = 0;
            max = 10;
            $scope.maxcount = 0;
            cargarGruposRutas();
        }
        if (tab === 2) {
            cargarGrupos();
            cargarVehiculos();
            min = 0;
            max = 10;
            $scope.maxcount = 0;
            cargarAsignaciones();
        }
        if (tab === 3) {
            cargarConductores();
            cargarVehiculos();
            min = 0;
            max = 10;
            $scope.maxcount = 0;
            cargarAsignacionesConductores();
        }
        if (tab === 4) {
            $scope.ptosdecontrol = {};
            cargarAreas();
        }
    };
    $scope.setActiveTab2 = function (tab) {
        $scope.activeTab2 = tab;
        if (tab === 5) {
            cargarAreas();
        }
        if (tab === 6) {
            $scope.ptosdecontrol = {};
            cargarAreasTiempos();
            cargarTimePicker();

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
    function cargarGruposRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            min: 0,
            max: 10
        };
        if ($scope.firstcharge) {
            $params = {
                user_id: localStorage['ztrack.user_id'],
                min: min,
                max: max
            };
        }
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/gruposrutasbyid', null, $params)
                .then(function (result) {
                    $scope.gruposrutas = result.gruposrutas;
                    if (result.success) {
                        $scope.gruposrutas = result.gruposrutas;
                        $scope.resultsfound = true;
                        $scope.paginationtab1 = result.moreresults;
                        $scope.minlabel = min;
                        $scope.maxlabel = max;
                        $scope.maxcount = parseInt(result.count);
                    } else {
                        $scope.resultsfound = false;
                        $scope.paginationtab1 = false;
                    }
                    $scope.firstcharge = true;
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

    function cargarAreasTiempos() {
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
            group_id: $scope.gruporuta.grupo,
            route_id: $scope.gruporuta.ruta.route_id,
            fechaini: $scope.gruporuta.fechaini,
            fechafin: $scope.gruporuta.fechafin
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/gruposrutas', null, $params)
                .then(function (result) {
                    if (result.success) {
                        cargarGruposRutas();
                    } else {
                        toastr.warning(result.mensaje);
                    }
                });
    };
    function cargarRutas() {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = {};
                    $scope.rutas = result.rutas;
                });
    }

    $scope.cargarRutasEnTabTiempo = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselecttime.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = {};
                    $scope.rutas = result.rutas;
                });
    }
    ;
    $scope.onChangeSelectedArea = function () {
        cargarRutas();
        cargarGrupos();
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

    $scope.filtrar = function () {
        if (($scope.filtros.placa === "") && ($scope.filtros.vehiculo === "") && ($scope.filtros.conductor === "")) {
            toastr.warning("No se puede realizar la búsqueda. No hay filtros asociados. Intente de Nuevo", "Advertencia");
        } else {
            $scope.hasFiltrosTab2 = true;
        }
        cargarAsignaciones();
    };


    function cargarAsignaciones() {
        if ($scope.hasFiltrosTab2) {
            $params = {
                user_id: localStorage['ztrack.user_id'],
                min: min,
                max: max,
                filtros: $scope.filtros,
                hasFiltros: true
            };
        } else {
            $params = {
                user_id: localStorage['ztrack.user_id'],
                min: min,
                max: max
            };
        }
        QueriesService.executeRequest('POST', '../laravel/public/gruposrutas/asignaciones', $scope.filtros, $params)
                .then(function (result) {
                    if (result.success) {
                        $scope.asignaciones = result.asignaciones;
                        $scope.resultsfound1 = true;
                        $scope.paginationtab2 = result.moreresults;
                        $scope.minlabel = min;
                        $scope.maxlabel = max;
                        $scope.maxcount = parseInt(result.count);
                        if ($scope.maxcount > max) {
                            $scope.paginationtab2 = true;
                        } else {
                            $scope.paginationtab2 = false;
                        }
                    } else {
                        $scope.resultsfound1 = false;
                        $scope.paginationtab2 = false;
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
            user_id: localStorage['ztrack.user_id'],
            min: min,
            max: max
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/asignacionconductores', null, $params)
                .then(function (result) {
                    if (result.success) {
                        $scope.asignacionconductores = result.asignaciones;
                        $scope.resultsfound3 = true;
                        $scope.paginationtab3 = result.moreresults;
                        $scope.minlabel = min;
                        $scope.maxlabel = max;
                        $scope.maxcount = parseInt(result.count);
                        if ($scope.maxcount > max) {
                            $scope.paginationtab3 = true;
                        } else {
                            $scope.paginationtab3 = false;
                        }
                    } else {
                        $scope.resultsfound3 = false;
                        $scope.paginationtab3 = false;
                    }

                });
    }

    $scope.paginationtab3ant = function () {
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
        cargarAsignacionesConductores();
    };

    $scope.paginationtab3sig = function () {
        if ($scope.maxlabel > parseInt($scope.maxcount)) {
            toastr.warning("Ya se encuentra en la última página de los resultados", "Advertencia");
            return;
        }
        min = max;
        max = (max) + 10;
        $scope.pag = $scope.pag + 1;
        cargarAsignacionesConductores();
    };

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
    $scope.onconfirmdeleteasignacion = function (vh_id) {
        var rta = confirm("¿Desea eliminar el registro?");
        if (rta) {
            $scope.eliminarinformacionconductor(vh_id);
        } else {
            return;
        }
    };
    $scope.eliminarinformacionconductor = function (id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            vehiculo_id: id
        };
        QueriesService.executeRequest('GET', '../laravel/public/gruposrutas/deleteinformacionconductores', null, $params)
                .then(function (result) {
                    if (result.warning) {
                        toastr.warning(result.value);
                        return;
                    }
                    cargarAsignacionesConductores();
                });
    };


    $scope.onChangeSelectedDespachos = function (area_id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: area_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachos/gruposxrutaid', null, $params)
                .then(function (result) {
                    $scope.grupodespachos = result.groups;
                });
    };

    $scope.paginationtab2ant = function () {
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
        cargarAsignaciones();
    };

    $scope.paginationtab2sig = function () {
        if ($scope.maxlabel > parseInt($scope.maxcount)) {
            toastr.warning("Ya se encuentra en la última página de los resultados", "Advertencia");
            return;
        }
        min = max;
        max = (max) + 10;
        $scope.pag = $scope.pag + 1;
        cargarAsignaciones();
    };

    $scope.paginationtab1ant = function () {
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
        cargarGruposRutas()();
    };

    $scope.paginationtab1sig = function () {
        if ($scope.maxlabel > parseInt($scope.maxcount)) {
            toastr.warning("Ya se encuentra en la última página de los resultados", "Advertencia");
            return;
        }
        min = max;
        max = (max) + 10;
        $scope.pag = $scope.pag + 1;
        cargarGruposRutas();
    };


    $scope.onChangeSelectedGrupos = function (gru_id) {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            group_id: gru_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachos/vehiculoxgroupid', null, $params)
                .then(function (result) {
                    $scope.vehiculosdespachos = result.vehiculos;
                });

    };

    $scope.cargarRutas = function () {
        limpiarCamposDespachos();
        cargarRutas();
    };

    $scope.onChangeareaPC = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachos/puntoscontrol', null, $params)
                .then(function (result) {
                    $scope.puntoscontrol = result.puntoscontrol;
                    cargarPuntosControlRuta();
                    toastr.success("Puntos de Control cargados con éxito", "OK");
                });
    };

    $scope.limpiarCamposDespachos = function () {
        $scope.areaselect = {};
        $scope.rutaselect = {};
        limpiarCamposDespachos();
    };


    function limpiarCamposDespachos() {
        document.getElementById("selectareadespachos").disabled = false;
        document.getElementById("selectrutasdespachos").disabled = false;
        $scope.puntoscontrol = {};
        $scope.ptosdecontrol = {};
    }
    ;
    

    $scope.guardarPtoControlRuta = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id,
            route_id: $scope.rutaselect.route_id,
            ptocontrol_id: $scope.ptocontrolselect.zone_id
        };
        QueriesService.executeRequest('POST', '../laravel/public/despachos/savepuntoscontrolaruta', $params, null)
                .then(function (result) {
                    if (result.success) {
                        cargarPuntosControlRuta();
                    }
                });
    };

    function cargarPuntosControlRuta() {
        if ($scope.rutaselect === null) {
            return;
        }
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id,
            route_id: $scope.rutaselect.route_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachos/cargarpuntoscontrolaruta', null, $params)
                .then(function (result) {
                    $scope.ptosdecontrol = result.ptosdecontrol;
                });
    }
    ;


    $scope.cargarPtosControlRuta = function () {
        if ($scope.rutaselect === null) {
            return;
        }
        $params = {
            user_id: localStorage['ztrack.user_id'],
            route_id: $scope.rutaselecttime.route_id
        };
        QueriesService.executeRequest('GET', '../laravel/public/despachos/cargarpuntoscontrolaruta', null, $params)
                .then(function (result) {
                    $scope.ptosdecontrol = result.ptosdecontrol;
                });
    };

    function cargarTimePicker() {
        $scope.horas = [];
        $scope.minutos = [];
        $scope.segundos = [];
        for (var i = 1; i < 24; i++) {
            if (i < 10) {                
                $array = {value: '0' + i};
            } else {
                $array = {value: i};
            }            
            $scope.horas.push($array);
        }
        for (var i = 0; i < 60; i++) {
            if (i < 10) {
                $array = {value: '0' + i};
            } else {
                $array = {value: i};
            }
            $scope.minutos.push($array);
            $scope.segundos.push($array);
        }


    }
    ;

    $scope.cargarDatosModalTiempo = function (pcid, rzid) {
        $scope.pcselect = pcid;
        $params = {
            user_id: localStorage['ztrack.user_id'],
            pc_id: pcid,
            rz_id: rzid
        };        
        QueriesService.executeRequest('GET', '../laravel/public/despachos/rutazonainfo', null, $params)
                .then(function (result) {
                    $scope.rutazona = result.rutazona;
                });
    };

    $scope.asigarTiempoPuntoControl = function () {
        if (($scope.tiempo.horasel === undefined || $scope.tiempo.horasel === null) && ($scope.tiempo.minsel === undefined || $scope.tiempo.minsel === null) && ($scope.tiempo.segsel === undefined || $scope.tiempo.segsel === null)) {
            toastr.warning("Debe asignar un tiempo diferente a vacio para poder continuar. Intente de nuevo.", "Advertencia");
            return;
        }
        if (($scope.tiempo.horasel === undefined || $scope.tiempo.horasel === null) && ($scope.tiempo.minsel === undefined || $scope.tiempo.minsel === null)) {
            toastr.warning("Verifique si el punto de control va a tener un tiempo de solo segundos. Intente de nuevo.", "Advertencia");
            return;
        }
        
        if($scope.tiempo.horasel === undefined || $scope.tiempo.horasel === null){
          $scope.hora = "00";  
        } 
        else{
          $scope.hora = $scope.tiempo.horasel.value;    
        }
        if($scope.tiempo.minsel === undefined || $scope.tiempo.minsel === null){
          $scope.minuto =  "00";  
        }
        else{
          $scope.minuto = $scope.tiempo.minsel.value;    
        }
        if($scope.tiempo.segsel === undefined || $scope.tiempo.segsel === null){
          $scope.segundo = "00";  
        }
        else{
          $scope.segundo = $scope.tiempo.segsel.value;    
        }
        $params = {
            tiempo: $scope.hora + ":" + $scope.minuto + ":" + $scope.segundo,
            user_id: localStorage['ztrack.user_id'],
            pc: $scope.pcselect
        };
        
        QueriesService.executeRequest('POST', '../laravel/public/despachos/guardartiempopc', $params, null)
                .then(function (result) {

                });

    };

    $scope.limpiarCamposTiempos = function () {
        $scope.tiempo = {};        
    };
    
    $scope.deletePtoControlRuta = function (it) {
        var rta = confirm("¿Desea eliminar el registro?");
        if (rta) {
            console.log("entro a borrar");
        } else {
            return;
        }
    };
        

});


