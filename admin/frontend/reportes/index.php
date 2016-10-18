<?php
// Conectando, seleccionando la base de datos
$conexion = mysql_connect('localhost', 'root', '')
        or die('No se pudo conectar: ' . mysql_error());
//echo 'Connected successfully';
mysql_select_db('gs') or die('No se pudo seleccionar la base de datos');
?>
<body class="no-skin">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="menu icon fa fa-home"></i>
                <a ui-sref="principal">Inicio</a>
            </li>
            <li class="active"><font color="#0B0B3B">{{title}}</font></li>
        </ul><!-- /.breadcrumb -->    
    </div>

    <!-- PAGE CONTENT BEGINS -->
    <div class="page-header">
        <h1>
            Reporte General de Ruta
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
            </small>
        </h1>
    </div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <label>A continuación puede generar el reporte correspondiente a una ruta.</label>
            <div class="tabbable">
                <ul class="nav nav-tabs">
                    <li ng-class="{'active' : activeTab === 1}"><a href="" ng-click="setActiveTab(1)">
                            <i class="blue fa fa-automobile bigger-120"></i>
                            Entre Vehículos
                        </a>
                    </li>
                    <li ng-class="{'active' : activeTab === 2}"><a href="" ng-click="setActiveTab(2)">
                            <i class="blue fa fa-tachometer bigger-120"></i>
                            Por Puntos de Control
                        </a>
                    </li> 
                    <li ng-class="{'active' : activeTab === 3}"><a href="" ng-click="setActiveTab(3)">
                            <i class="blue fa fa-file bigger-120"></i>
                            General
                        </a>
                    </li>                                    
                </ul> 
                <div class="tab-content">
                    <div ng-class="{'tab-pane active': activeTab === 1, 'tab-pane' : activeTab !== 1}">
                        <div class="container-fluid">
                            <h4>Generar Reporte de Tiempo y Distancia al Instante.</h4>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th>Seleccione el Área</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td colspan="4">
                                            <select class="form-control" ng-model="areaselect" ng-options="area.name for area in areas track by area.name" ng-change="cargarRutasByArea()">
                                                <option value="" selected disabled>Seleccionar un área</option>
                                            </select>                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th colspan="4" valign="middle">Seleccione la Ruta</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td align="center">
                                            <select id="selrut" class="form-control" ng-model="selectruta">                                                
                                                <option value="" selected disabled>Seleccionar una ruta</option> 
                                                <option value="{{ruta.route_id}}" ng-repeat="ruta in rutas">{{ruta.route_name}}</option>
                                            </select>
                                        </td>										
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="tdsql" onClick="informeTD()">
                                                <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                Consultar
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="cancelar" onClick="cancelarTD()">
                                                <i class="ace-icon fa fa-times blue"></i>
                                                Cancelar
                                            </button>
                                        </td>	
                                        <td align="center">
                                            <button type="button" class="btn btn-white btn-info btn-bold" id="pdfgenera"  onClick="generaTD()">
                                                <i class="ace-icon fa fa-cloud-download bigger-120 blue"></i>
                                                PDF
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div id="tabladifTD" class="main-container"> 
                                <!-- Aqui se carga la Diferencia en tiempo y distancia real de Cada Vehiculo -->
                            </div>      
                        </div>
                    </div>
                    <!--Tab 2 Reportes por puntos de control-->
                    <div id="ptoscontrol" ng-class="{'tab-pane active': activeTab === 2, 'tab-pane' : activeTab !== 2}">
                        <div class="container-fluid">
                            <h4><p>Reporte Control de Reloj.</p></h4>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th>Seleccione el Área</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td colspan="4">
                                            <select class="form-control" ng-model="areaselect" ng-options="area.name for area in areas track by area.name" ng-change="cargarRutasByArea()">
                                                <option value="" selected disabled>Seleccionar un área</option>
                                            </select>                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th colspan="4" valign="middle">Seleccione la Ruta</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                <tbody>
                                    <tr>
                                        <td align="center">
                                            <select class="form-control" ng-model="selectruta" ng-options="ruta.route_name for ruta in rutas track by ruta.route_name">
                                                <option value="" selected disabled>Seleccionar una ruta</option>                                                
                                            </select>
                                        </td>									
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="pcsql" onClick="informePC()">
                                                <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                Consultar
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="cancelarPC" onClick="cancelarPC()">
                                                <i class="ace-icon fa fa-times blue"></i>
                                                Cancelar
                                            </button>
                                        </td>	
                                        <td align="center">
                                            <button type="button" class="btn btn-white btn-info btn-bold" id="pdfgeneraPC"  onClick="generaPC()">
                                                <i class="ace-icon fa fa-cloud-download bigger-120 blue"></i>
                                                PDF
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div id="tablaPC" class="main-container"> 
                                <!-- Aqui se carga la informacion respecto a los puntos de control en tiempo real vehiculos en ruta -->
                            </div> 
                        </div>                                        
                    </div>
                    <!--Tab 3 Reportes Generales-->
                    <div id="general" ng-class="{'tab-pane active': activeTab === 3, 'tab-pane' : activeTab !== 3}">
                        <div class="container-fluid">
                            <h4>Reportes Generales</h4>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th valign="middle">Seleccione el Área</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td align="center">
                                           <select class="form-control" ng-model="areaselect" ng-options="area.name for area in areas track by area.name" ng-change="cargarRutasByArea()">
                                                <option value="" selected disabled>Seleccionar un área</option>
                                            </select>  
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th colspan="4" valign="middle">Seleccione la Ruta</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td align="center">
                                            <select class="form-control" ng-model="selectruta" ng-options="ruta.route_name for ruta in rutas track by ruta.route_name">
                                                <option value="" selected disabled>Seleccionar una ruta</option>                                                
                                            </select>
                                        </td>										
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="tdsql" onClick="informeTD()">
                                                <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                Consultar
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="cancelar" onClick="cancelarTD()">
                                                <i class="ace-icon fa fa-times blue"></i>
                                                Cancelar
                                            </button>
                                        </td>	
                                        <td align="center">
                                            <button type="button" class="btn btn-white btn-info btn-bold" id="pdfgenera"  onClick="generaTD()">
                                                <i class="ace-icon fa fa-cloud-download bigger-120 blue"></i>
                                                PDF
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->
<div id="df">

</div>

<a href="" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
<?
//mysql_free_result($stm);
mysql_close($conexion);
?>

<script type="text/javascript">
    var pdf = '';
    var pdfpc = '';
    var css = '<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>';
    var img = '<img style="border:0px" src="img/logo.png" /><hr/>';
    var label_t = '<label>Reporte Tiempo - Distancia entre Vehiculos</label>';
    var label_pc = '<label>Reporte Tiempo - Puntos de Control de la Ruta</label>';

    function informeTD() {
        var id = document.getElementById("selrut").value; 
        var user_id = localStorage['ztrack.user_id'];
        $.ajax({
            type: "post",
            data: { 
                selrut : id,
                user_id : user_id
            },
            url: "reportes/tiempodis.php",
            success: function (a) {
                $('#tabladifTD').html(a);                
                pdf = a;
            }
        });
    }


    function generaTD() {
        pdf = css + img + label_t + pdf;
        $.ajax({
            type: "post",
            url: "reportes/tablapdf.php",
            data: {df: pdf},
            success: function (a) {                
                window.open('reportes/tablapdf.php', '_blank');
            }
        });
    }

    function informePC() {
        $.ajax({
            type: "post",
            url: "reportes/pctiemporeal.php",
            success: function (apc) {
                $('#tablaPC').html(apc);                
                pdfpc = apc;
            }
        });
    }

    function generaPC() {
        pdfpc = css + img + label_pc + pdfpc;
        $.ajax({
            type: "post",
            url: "reportes/listapdfPC.php",
            data: {dfpc: pdfpc},
            success: function (apc) {                
                window.open('reportes/listapdfPC.php', '_blank');
            }
        });
    }

    function cancelarTD() {
        var d = document.getElementById("tabladifTD");
        while (d.hasChildNodes()) {
            d.removeChild(d.firstChild);
        }
    }

    function cancelarPC() {
        var d = document.getElementById("tablaPC");
        while (d.hasChildNodes()) {
            d.removeChild(d.firstChild);
        }        
    }


</script>
</body>
</html>

