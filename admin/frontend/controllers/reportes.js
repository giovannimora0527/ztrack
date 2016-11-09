/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('ReportesController', function ($rootScope, $scope, $state, QueriesService, toastr) {   
   $scope.title = "Reportes";
    $scope.varpdf = null;
    //getFechahoy();

    $scope.activeTab = 1;
    $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        if (tab === 2 || tab === 3) {
            cargarAreas();
        }
    };
    cargarAreas();


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

    $scope.cargarRutasByArea = function () {
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id
        };
        QueriesService.executeRequest('GET', '../laravel/public/rutas/rutasbyid', null, $params)
                .then(function (result) {
                    $scope.rutas = {};
                    $scope.rutas = result.rutas;
                    //$scope.cancelarPCxV();
                    if ($scope.rutas.length === 0) {
                        toastr.warning("No existen rutas asociadas", "Advertencia");
                    }
                });
    };

    $scope.cargarVehiculoByRuta = function (){
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id,
            ruta_id: $scope.selectruta.route_id,
            fecha: $scope.fechaselect
            //group_id: $scope.gruposelect.group_id
        };
         QueriesService.executeRequest('GET', '../laravel/public/rutas/vehiculosbyruta', null, $params)
                .then(function (result) {
                    $scope.vehiculos = {};
                    $scope.vehiculos = result.vehiculos;
                    if ($scope.vehiculos.length === 0){
                        toastr.warning("No existen vehiculos asociados", "Advertencia");
                    }
                });
    };
    
   
    //<CHOVE>
    $scope.cargarVueltasByRuta = function () {
        if ($scope.selectruta === null) {
            return;
        }        
        $params = {
            user_id: localStorage['ztrack.user_id'],
            area_id: $scope.areaselect.id,
            ruta_id: $scope.selectruta.route_id,
            fecha: $scope.fechaselect2
        };
        console.log($params);
        
        QueriesService.executeRequest('GET', '../laravel/public/rutas/vueltasbyruta', null, $params)
                .then(function (result) {
                    $scope.vueltas = result.vueltas;
                    if ($scope.vueltas.length === 0){
                        toastr.warning("No existen vueltas asociadas", "Advertencia");
                    }
                });

    };

    $scope.informePCxV = function () {
        var id = $scope.selectruta.route_id;
        var user_id = localStorage['ztrack.user_id'];
        var fecptos = $scope.fechaselect;
        var recorrido = $scope.selectvuelta.numero_recorrido;
        //  console.log(fecptos);
        $.ajax({
            type: "post",
            url: "reportes/ptoscontrol/puntos_ruta.php",
            data: {
                ruta_id: id,
                user_id: user_id,
                numero_recorrido: recorrido,
                fecha: fecptos
            },
            success: function (pxv) {
                $('#verptosControl').html(pxv);
                $scope.varpdf = pxv;
            }
        });
        //console.log(fecha);
    };


    $scope.cancelarPCxV = function () {
        $scope.varpdf = null;
        var d = document.getElementById("verptosControl");
        $scope.selectruta = {};
        $scope.selectvuelta = {};
        //getFechahoy() ;
        
        while (d.hasChildNodes()) {
            d.removeChild(d.firstChild);
        }
    };

    $scope.generaPCxV = function () {
        // console.log($scope.varpdf);
        $.ajax({
            type: "post",
            url: "reportes/ptoscontrol/puntos_rutaPDF.php",
            data: {dfpc: $scope.varpdf},
            success: function () {
                window.open('reportes/ptoscontrol/puntos_rutaPDF.php', '_blank');
            }
        });
        cancelarPCxV();

    };
    
    $scope.informeVehi = function () {
        var id = $scope.selectruta.route_id;
        var user_id = localStorage['ztrack.user_id'];
        var feselec = $scope.fechaselect;
        var vehiculo = $scope.selectvehi.imei;
        // console.log(vehiculo);
        $.ajax({
            type: "post",
            url: "reportes/tiemporuta/tiempo_ruta.php",
            data: {
                ruta_id: id,
                user_id: user_id,
                vehiculo_imei : vehiculo,
                fecha: feselec
            },
            success: function (pxv) {
                $('#vertiemposrut').html(pxv);
                $scope.varpdf = pxv;
            }
        });
    };
    
    $scope.cancelarVehi = function () {
        $scope.varpdf = null;
        var d = document.getElementById("vertiemposrut");
        $scope.selectruta = {};
        $scope.selectvehi = {};
        //getFechahoy() ;
        
        while (d.hasChildNodes()) {
            d.removeChild(d.firstChild);
        }
    };
    
    $scope.informeVehi_ptos = function () {
        var id = $scope.selectruta.route_id;
        var user_id = localStorage['ztrack.user_id'];
        var feselec = $scope.fechaselect;
        var vehiculo = $scope.selectvehi.imei;
        // console.log(vehiculo);
        $.ajax({
            type: "post",
            url: "reportes/tiemporuta/tiempo_puntos.php",
            data: {
                ruta_id: id,
                user_id: user_id,
                vehiculo_imei : vehiculo,
                fecha: feselec
            },
            success: function (pxv) {
                $('#vertiemposrut').html(pxv);
                $scope.varpdf = pxv;
            }
        });
    };
    //</CHOVE>
//     $scope.dateTimeNow = function () {
//        $scope.date = new Date();
//    };
//    function getFechahoy() {
//        $scope.fechaselect2 = new Date();
//    }
    //<CHOVE> COD GIOVANNI
    $scope.toggleMin = function () {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();
    $scope.maxDate = new Date(2030, 12, 31);
    $scope.opencal = function () {
        $scope.popup1.opened = true;

    };
    $scope.opencal2 = function () {
        $scope.popup2.opened = true;
    };
    $scope.opencal3 = function () {
        $scope.popup3.opened = true;
    };
    $scope.opencal4 = function () {
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

});


