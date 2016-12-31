/**
 * Archivo principal aplicación angular
 * Actualizado para el funcionamiento con AngularJS v1.3.4
 * Dependencias de la aplicación y estados (ui.router)
 *
 * @author Giovanni Mora
 * @email giovannimora0527@gmail.com
 * @version 1.1
 */
var ztrack = angular.module('ztrack', ['ui.bootstrap', 'ngCookies', 'ui.router', 'zmodo-directives', 'queries-service', 'formValidationService', 'toastr', 'anchorScrollOffset', 'easypiechart']);
ztrack.config(['$stateProvider', '$urlRouterProvider', '$httpProvider',
    function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise('principal');
        $stateProvider
                .state('principal', {
                    url: '/principal',
                    templateUrl: 'html/home.html',
                    controller: 'HomeController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reportesdespachos', {
                    url: '/reportes_despachos',
                    templateUrl: 'html/repodespachos.html',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reporteseventos', {
                    url: '/reportes_eventos',
                    templateUrl: 'html/repoeventos.html',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reportestiempos', {
                    url: '/reportes_tiempo_en_ruta',
                    templateUrl: 'reportes/tiemporuta/index.php',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reportesduracion', {
                    url: '/reportes_duracion_en_ruta',
                    templateUrl: 'html/duracion.html',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reportesPC', {
                    url: '/reportes_puntos_control',
                    templateUrl: 'reportes/ptoscontrol/index.php',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reportesTFR', {
                    url: '/reportes_tiepo_fuera_ruta',
                    templateUrl: 'html/timesOutRuta.html',
                    controller: 'ReportesController'
                })
                .state('reportesdiferencia', {
                    url: '/reportes_tiepo_fuera_ruta',
                    templateUrl: 'html/diferenciatimes.html',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('reportesactuales', {
                    url: '/reportes_generales_rutas',
                    templateUrl: 'reportes/actual/index.php',
                    controller: 'ReportesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('gestiondespachos', {
                    url: '/gestion_despachos',
                    templateUrl: 'html/despachos.html',
                    controller: 'DespachosController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('gestiongrupos', {
                    url: '/gestion_grupos',
                    templateUrl: 'html/grupos.html',
                    controller: 'GruposController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('gestiondespachadores', {
                    url: '/gestion_despachadores',
                    templateUrl: 'html/despachadores.html',
                    controller: 'AdminDespachosController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('gestionconductores', {
                    url: '/gestion_de_conductores',
                    templateUrl: 'html/gestionconductores.html',
                    controller: 'GestionConductorController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('gestionvehiculos', {
                    url: '/gestion_de_vehiculos',
                    templateUrl: 'html/gestionvehiculos.html',
                    controller: 'GestionController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                    
                })
                .state('consultaconductores', {
                    url: '/consultas',
                    templateUrl: 'html/conductores.html',
                    controller: 'ConsultasController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('consultavehiculos', {
                    url: '/consultas',
                    templateUrl: 'html/vehiculos.html',
                    controller: 'ConsultasController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('consultarutas', {
                    url: '/consultas',
                    templateUrl: 'html/rutas.html',
                    controller: 'ConsultasController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('modulodespachos', {
                    url: '/modulo_despachos',
                    templateUrl: 'html/modulodespachos.html',
                    controller: 'DespachadoresController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('modulonovedades', {
                    url: '/novedades',
                    templateUrl: 'html/novedades.html',
                    controller: 'NovedadesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('modulonovedadesconductores', {
                    url: '/novedades_conductores',
                    templateUrl: 'html/novedadesconductores.html',
                    controller: 'NovedadesController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('perfil', {
                    url: '/perfil',
                    templateUrl: 'html/perfil.html',
                    controller: 'PerfilController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                            $state.go('salir');
                        }
                    }
                })
                .state('salir', {
                    onEnter: function (SessionService) {
                        SessionService.destroy();  
                        window.location = "login.html";
                    }
                })

                ;
    }]).factory('myHttpInterceptor', function ($q, $injector) {
    return {
        // optional method
        'request': function (config) {
            return config;
        },
        // optional method
        'requestError': function (rejection, response) {
            if (response.status === 401) {
                $injector.invoke(function ($http, SessionService) {
                    SessionService.destroy();
                    SessionService.redirectToLogin();
                });
            } else if (response.status === 403) {
                $injector.invoke(function ($http, SessionService) {
                    SessionService.unauthorized();
                });
            }            
            if (canRecover(rejection)) {
                return $q.reject(response);
            }
            return $q.reject(rejection);
        },
        // optional method
        'response': function (response) {
            // do something on success
            return response;
        }

    };
}).
        run(function ($rootScope, $state, SessionService, $http) {
            $rootScope.$state = $state;
            $rootScope.url_base = "http://localhost/ztrack/";
//          $rootScope.url_base = "http://208.11.32.127/ztrack/"; -> url del servidor - Document_Root
            SessionService.refresh();
        });