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
                Reporte de tiempos en ruta
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
                                <i class="blue glyphicon glyphicon-road bigger-120"></i>
                                Por ruta
                            </a>
                        </li>
                        <li ng-class="{'active' : activeTab === 2}">
                            <a href="" ng-click="setActiveTab(2)">
                                <i class="blue fa fa-automobile bigger-120"></i>
                                Por veh&iacute;culo
                            </a>
                        </li>                           
                    </ul>
                    <div class="tab-content">
                        <div ng-class="{'tab-pane active': activeTab === 1, 'tab-pane' : activeTab !== 1}">
                            <div class="container-fluid">
                                <h4>Reporte del tiempo en ruta del veh&iacute;culo</h4>
                                <!-- Fecha -->
                                <div class="row">
                                    <label class="control-label border blue">Fecha</label>
                                    <p class="input-group  col-sm-2">
                                        <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="fechaselect" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="true" close-text="Close" alt-input-formats="altInputFormats" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-blue btn-primary" ng-click="opencal()"><i class="glyphicon glyphicon-calendar"></i></button>

                                        </span>
                                    </p>
                                    <!-- Fecha -->
                                </div>
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
                                                <th>Lista de Veh&iacute;culos</th>
                                                <th colspan="3" align="center">Accci&oacute; a realizar</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>                     
                                                <td align="center">
                                                    <select id="selrut" class="form-control" ng-model="selectruta" ng-options="ruta.route_name for ruta in rutas" ng-change="cargarVehiculoByRuta()">                                                
                                                        <option value="" selected disabled>Seleccionar...</option> 
                                                        <!--option value="{{ruta.route_id}}" ng-repeat="ruta in rutas">{{ruta.route_name}}</option-->
                                                    </select>  
                                                </td>
                                                <td>
                                                    <select id="selvuel" class="form-control" ng-model="selectvehi" ng-options="varvehiculo.name for varvehiculo in vehiculos">  
                                                        <option value="" selected disabled>Seleccionar ...</option>
                                                        <option value="{{varvehiculo.imei}}" ng-repeat="varvehiculo in vehiculos">{{varvehiculo.name}}</option>
                                                    </select>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-white btn-info btn-bold" id="pcsql" ng-click="informeVehi()">
                                                        <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                        Consultar
                                                    </button>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-white btn-warning btn-bold" id="cancelarPC" ng-click="cancelarVehi()">
                                                        <i class="ace-icon fa fa-times orange"></i>
                                                        Cancelar
                                                    </button>
                                                </td>	
                                                <td align="center">
                                                    <button type="button" class="btn btn-white btn-default btn-bold" id="pdfgeneraPC" ng-click="generaVehi()">
                                                        <i class="ace-icon fa fa-cloud-download bigger-120 red2"></i>
                                                        PDF
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="vertiemposrut" class="main-container"> 
                                    <!-- Aqui se carga el tiempo Cada Vehiculo por ruta y vuelta -->
                                </div>  
                            </div>
                        </div><!-- /.container-fluid --> <!-- Fin Tab 1 -->
                        <!-- Inicio Tab 2 -->
                        <div ng-class="{'tab-pane active': activeTab === 2, 'tab-pane' : activeTab !== 2}">
                            <div class="container-fluid">
                                <h4>Reporte del tiempo del veh&iacute;culo con puntos de control</h4>
                                <!-- Fecha -->
                                <div class="row">
                                    <label class="control-label border blue">Fecha</label>
                                    <p class="input-group  col-sm-2">
                                        <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="fechaselect" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="true" close-text="Close" alt-input-formats="altInputFormats" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-blue btn-primary" ng-click="opencal()"><i class="glyphicon glyphicon-calendar"></i></button>

                                        </span>
                                    </p>
                                    <!-- Fecha -->
                                </div>
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
                                                <th>Lista de Veh&iacute;culos</th>
                                                <th colspan="3" align="center">Accci&oacute; a realizar</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>                     
                                                <td align="center">
                                                    <select id="selrut" class="form-control" ng-model="selectruta" ng-options="ruta.route_name for ruta in rutas" ng-change="cargarVehiculoByRuta()">                                                
                                                        <option value="" selected disabled>Seleccionar...</option> 
                                                        <!--option value="{{ruta.route_id}}" ng-repeat="ruta in rutas">{{ruta.route_name}}</option-->
                                                    </select>  
                                                </td>
                                                <td>
                                                    <select id="selvuel" class="form-control" ng-model="selectvehi" ng-options="varvehiculo.name for varvehiculo in vehiculos">  
                                                        <option value="" selected disabled>Seleccionar ...</option>
                                                        <option value="{{varvehiculo.imei}}" ng-repeat="varvehiculo in vehiculos">{{varvehiculo.name}}</option>
                                                    </select>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-white btn-info btn-bold" id="pcsql" ng-click="informeVehi_ptos()">
                                                        <i class="ace-icon fa fa-search bigger-120 blue"></i>
                                                        Consultar
                                                    </button>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-white btn-warning btn-bold" id="cancelarPC" ng-click="cancelarVehi_ptos()">
                                                        <i class="ace-icon fa fa-times orange"></i>
                                                        Cancelar
                                                    </button>
                                                </td>	
                                                <td align="center">
                                                    <button type="button" class="btn btn-white btn-default btn-bold" id="pdfgeneraPC" ng-click="generaVehi_ptos()">
                                                        <i class="ace-icon fa fa-cloud-download bigger-120 red2"></i>
                                                        PDF
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>

                                <div id="vervehixptos" class="main-container"> 
                                    <!-- Aqui se carga la el tiempo del Vehiculo y los puntos -->
                                </div>  
                            </div>
                        </div>
                        <!-- Fin Tab 2-->
                        <!-- FIN CONTENIDO PAGINA -->
                    </div><!-- /.tab-content -->
                </div><!-- /.tabbable -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </body>
</html>