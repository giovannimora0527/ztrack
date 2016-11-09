<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
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
        <div class="page-header">
            <h1>
                Tiempos en Puntos de Control
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                </small>
            </h1>
        </div><!-- /.page-header -->

        <div class="row">
            <div class="col-xs-12">
                <!--label>A continuaci&oacute;n puede generar el reporte de puntos por vuelta</label-->
                <div class="tabbable">  
                    <!-- INICIO CONTENIDO PAGINA -->
                    <ul class="nav nav-tabs">
                        <li ng-class="{'active' : activeTab === 1}">
                            <a href="" ng-click="setActiveTab(1)">
                                <i class="blue fa fa fa-tachometer bigger-120"></i>
                                Puntos de Control
                            </a>
                        </li>
                        <!--li ng-class="{'active' : activeTab === 2}">
                            <a href="" ng-click="setActiveTab(2)">
                                <i class="blue fa fa-tachometer bigger-120"></i>
                                    Por Puntos de Control
                            </a>
                        </li-->                           
                    </ul>
                    <div class="tab-content">
                        <div ng-class="{'tab-pane active': activeTab === 1, 'tab-pane' : activeTab !== 1}">
                            <div class="container-fluid">
                                <h4>Generar Reporte de puntos de control por vuelta</h4>
                                <!-- Fecha -->
                                <!--label for="id-date-picker-1" class="control-label border blue">Fecha</label>
                                <div class="row">
                                    <div class="col-xs-8 col-sm-3">
                                        <div class="input-group">
                                            <input class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd-mm-yyyy" />
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar bigger-110"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div-->
                                <div class="row">
                                <label class="control-label border blue">Fecha</label>
                                <p class="input-group col-sm-2">
                                    <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="fechaselect2" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="true" close-text="Close" alt-input-formats="altInputFormats" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-blue btn-primary" ng-click="opencal()"><i class="glyphicon glyphicon-calendar"></i></button>
                                        
                                    </span>
                                </p>
                                </div>
                                <!-- Fecha -->

                                <div class="row">
                                    <label class="control-label border blue">Seleccione el Área</label>
                                    <select class="form-control" ng-model="areaselect" ng-options="area.name for area in areas track by area.name" ng-change="cargarRutasByArea()">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <!--option ng-repeat="ar in areas" value="{{ar.id}}">{{ar.name}}</option-->
                                    </select>
                                </div>
                                <br>
                                <div class="row">
                                    <table class="table table-bordered table-hover table-responsive table-striped">
                                        <thead>
                                            <tr>                                
                                                <th>Lista de Rutas</th>
                                                <th>No. de Vueltas</th>
                                                <th colspan="3" align="center">Accci&oacute; a realizar</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>                     
                                                <td align="center">
                                                    <select id="selrut" class="form-control" ng-model="selectruta" ng-options="ruta.route_name for ruta in rutas" ng-change="cargarVueltasByRuta()">                                                
                                                        <option value="" selected disabled>Seleccionar...</option> 
                                                        <!--option value="{{ruta.route_id}}" ng-repeat="ruta in rutas">{{ruta.route_name}}</option-->
                                                    </select>  
                                                </td>
                                                <td>
                                                    <select id="selvuel" class="form-control" ng-model="selectvuelta" ng-options="varvuelta.numero_recorrido for varvuelta in vueltas">  
                                                        <option value="" selected disabled>Seleccionar ...</option>
                                                        <!--option value="{{varvuelta.numero_recorrido}}" ng-repeat="varvuelta in vueltas">{{varvuelta.numero_recorrido}}</option-->
                                                    </select>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-white btn-info btn-bold" id="pcsql" ng-click="informePCxV()">
                                                        <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                        Consultar
                                                    </button>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-white btn-warning btn-bold" id="cancelarPC" ng-click="cancelarPCxV()">
                                                        <i class="ace-icon fa fa-times orange"></i>
                                                        Cancelar
                                                    </button>
                                                </td>	
                                                <td align="center">
                                                    <button type="button" class="btn btn-white btn-default btn-bold" id="pdfgeneraPC" ng-click="generaPCxV()">
                                                        <i class="ace-icon fa fa-cloud-download bigger-120 red2"></i>
                                                        PDF
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="verptosControl" class="main-container"> 
                                    <!-- Aqui se carga la Diferencia en tiempo y distancia real de Cada Vehiculo -->
                                </div>  
                            </div>
                        </div><!-- /.container-fluid -->
                        <!-- FIN CONTENIDO PAGINA -->
                    </div><!-- /.tab-content -->
                </div><!-- /.tabbable -->
            </div><!-- /.col -->
        </div><!-- /.row -->
   </body>
</html>