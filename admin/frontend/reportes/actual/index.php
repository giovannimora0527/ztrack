<?php
//Include database connection details
require_once('../../php/connection.php');
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
            Reporte Actual en la Ruta
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
            </small>
        </h1>
    </div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <label>A continuaci&oacute;n puede generar el reporte actual correspondiente a una ruta.</label>
            <div class="tabbable">
                <ul class="nav nav-tabs">
                    <li ng-class="{'active' : activeTab === 1}"><a href="" ng-click="setActiveTab(1)">
                            <i class="blue fa fa-automobile bigger-120"></i>
                            Entre Veh&iacute;culos
                        </a>
                    </li>
                    <li ng-class="{'active' : activeTab === 2}"><a href="" ng-click="setActiveTab(2)">
                            <i class="blue fa fa-tachometer bigger-120"></i>
                            Por Puntos de Control
                        </a>
                    </li>                           
                </ul> 
                <div class="tab-content">
                    <!-- Inicio Tab 1 -->
                    <div ng-class="{'tab-pane active': activeTab === 1, 'tab-pane' : activeTab !== 1}">
                        <div class="container-fluid">
                            <h4>Visualizar Reporte de Tiempo y Distancia al Instante.</h4>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th>Seleccione el &Aacute;rea</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td colspan="4">
                                            <select class="form-control" ng-model="areaselect" ng-options="area.name for area in areas track by area.name" ng-change="cargarRutasByArea()">
                                                <option value="" selected disabled>Seleccionar un &Aacute;rea</option>
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
                                            <button class="btn btn-white btn-info btn-bold" id="tdsql" onClick="informeTD()" ng-disabled="bntdisabled">
                                                <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                Consultar
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-white btn-warning btn-bold" id="cancelar" ng-click="asignarVehiculos()" onClick="cancelarTD()">
                                                <i class="ace-icon fa fa-times orange"></i>
                                                Cancelar
                                            </button>
                                        </td>	
                                        <td align="center">
                                            <button type="button" class="btn btn-white btn-default btn-bold" id="pdfgenera"  onClick="generaTD()">
                                                <i class="ace-icon fa fa-cloud-download bigger-120 red2"></i>
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
                    <!-- Fin Tab 1-->
                    <!-- Inicio Tab 2 Reportes por puntos de control-->
                    <div id="ptoscontrol" ng-class="{'tab-pane active': activeTab === 2, 'tab-pane' : activeTab !== 2}">
                        <div class="container-fluid">
                            <h4><p>Reporte Control de Reloj.</p></h4>
                            <table class="table  table-bordered table-hover table-responsive table-striped">
                                <thead>
                                    <tr>                                
                                        <th>Seleccione el &Aacute;rea</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>  
                                        <td colspan="4">
                                            <select class="form-control" ng-model="areaselect" ng-options="area.name for area in areas track by area.name" ng-change="cargarRutasByArea()">
                                                <option value="" selected disabled>Seleccionar un &Aacute;rea</option>
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
                                            <select id="selrut1" class="form-control">
                                                <option value="" selected disabled>Seleccionar una ruta</option>   
                                                <option value="{{ruta.route_id}}" ng-model="selectruta" ng-repeat="ruta in rutas">{{ruta.route_name}}</option>  
                                            </select>
                                        </td>									
                                        <td align="center">
                                            <button class="btn btn-white btn-info btn-bold" id="pcsql" onClick="informePC()">
                                                <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                Consultar
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-white btn-warning btn-bold" id="cancelarPC" onClick="cancelarPC()">
                                                <i class="ace-icon fa fa-times orange"></i>
                                                Cancelar
                                            </button>
                                        </td>	
                                        <td align="center">
                                            <button type="button" class="btn btn-white btn-default btn-bold" id="pdfgeneraPC"  onClick="generaPC()">
                                                <i class="ace-icon fa fa-cloud-download bigger-120 red2"></i>
                                                PDF
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div id="tablaPC" class="main-container main-responsibe"> 
                                <!-- Aqui se carga la informacion respecto a los puntos de control en tiempo real vehiculos en ruta -->
                            </div> 
                        </div>                                       
                    </div>
                    <!-- Fin Tab 2 -->
                    <!--Tab 3 Reportes Generales-->
                    <!-- Fin Tab 3 -->
                </div>
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
<!--div id="df">

</div-->

<a href="" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
<script type="text/javascript">
    var pdf = '';
    var pdfpc = '';
    function informeTD() {
        var id = document.getElementById("selrut").value; 
        var user_id = localStorage['ztrack.user_id'];
        $.ajax({
            type: "post",
            data: { 
                ruta_id : id,
                user_id : user_id
            },
            url: "reportes/actual/tiempodis.php",
            success: function (a) {
                $('#tabladifTD').html(a);                
                pdf = a;
            }
        });
        
        //console.log(route_name);
    }


    function generaTD() {
        
        $.ajax({
            type: "post",
            url: "reportes/actual/tablapdf.php",
            data: {df: pdf},
            success: function () {                
                window.open('reportes/actual/tablapdf.php', '_blank');
            }
        }); 
        cancelarTD();
        pdf='';
        
    }

    function informePC() {
        var id = document.getElementById("selrut1").value; 
        var user_id = localStorage['ztrack.user_id'];
        $.ajax({
            type: "post",
            url: "reportes/actual/pctiemporeal.php",
            data: { 
                ruta_id : id,
                user_id : user_id
            },
            success: function (apc) {
                $('#tablaPC').html(apc);                
                pdfpc = apc;
            }
        });
    }

    function generaPC() {
        
        $.ajax({
            type: "post",
            url: "reportes/actual/listapdfPC.php",
            data: {dfpc: pdfpc},
            success: function () {                
                window.open('reportes/actual/listapdfPC.php', '_blank');
            }
        });
        cancelarPC();
        pdfpc='';
        
    }

    function cancelarTD() {
        pdf='';
        var d = document.getElementById("tabladifTD");
        while (d.hasChildNodes()) {
            d.removeChild(d.firstChild);
        }
        //document.getElementById("selrut1").value='';
       
    }

    function cancelarPC() {
        pdfpc='';
        var d = document.getElementById("tablaPC");
        while (d.hasChildNodes()) {
            d.removeChild(d.firstChild);
        }        
    }


</script>
</body>
</html>

