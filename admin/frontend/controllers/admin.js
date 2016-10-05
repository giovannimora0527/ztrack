/**
 * Archivo principal aplicación angular
 * Actualizado para el funcionamiento con AngularJS v1.3.4
 * Dependencias de la aplicación y estados (ui.router)
 *
 * @author Giovanni Mora
 * @email giovannimora0527@gmail.com
 * @version 1.1
 */
var ztrack = angular.module('ztrack', ['ui.bootstrap','ngCookies', 'ui.router', 'zmodo-directives', 'queries-service', 'formValidationService', 'toastr', 'anchorScrollOffset']);
ztrack.config(['$stateProvider', '$urlRouterProvider', '$httpProvider',
    function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise('home');
        $stateProvider
                .state('home', {
                    url: '/home',
                    templateUrl: 'html/inicio.html',
                    controller: 'HomeController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                           $state.go('salir');
                        } 
                        else{
                          $state.go('principal');  
                        }
                    }
                })  
                .state('principal', {                    
                    url: '/principal',
                    templateUrl: 'html/home.html',
                    controller: 'PrincipalController'
                })
                .state('reportesdespachos', {                    
                    url: '/reportes_despachos',
                    templateUrl: 'html/repodespachos.html',
                    controller: 'ReportesController'
                })
                .state('reporteseventos', {                    
                    url: '/reportes_eventos',
                    templateUrl: 'html/repoeventos.html',
                    controller: 'ReportesController'
                })                
                .state('reportestiempos', {                    
                    url: '/reportes_tiempo_en_ruta',
                    templateUrl: 'html/reptimeruta.html',
                    controller: 'ReportesController'
                })
                .state('reportesduracion', {                    
                    url: '/reportes_duracion_en_ruta',
                    templateUrl: 'html/duracion.html',
                    controller: 'ReportesController'
                })
                .state('reportesPC', {                    
                    url: '/reportes_puntos_control',
                    templateUrl: 'html/timesPC.html',
                    controller: 'ReportesController'
                })
                .state('reportesTFR', {                    
                    url: '/reportes_tiepo_fuera_ruta',
                    templateUrl: 'html/timesOutRuta.html',
                    controller: 'ReportesController'
                })
                .state('reportesdiferencia', {                    
                    url: '/reportes_tiepo_fuera_ruta',
                    templateUrl: 'html/diferenciatimes.html',
                    controller: 'ReportesController'
                })
                .state('gestiondespachos', {
                    url: '/gestion_despachos',
                    templateUrl: 'html/despachos.html',
                    controller: 'DespachosController'                    
                })
                .state('gestionconductores', {
                    url: '/gestions_de_conductores',
                    templateUrl: 'html/gestionconductores.html',
                    controller: 'GestionController'                    
                })
                .state('gestiongrupos', {
                    url: '/gestions_de_grupos',
                    templateUrl: 'html/gestiongrupos.html',
                    controller: 'GestionController'                    
                })
                .state('consultaconductores', {
                    url: '/consultas',
                    templateUrl: 'html/conductores.html',
                    controller: 'ConsultasController'                    
                })
                .state('consultavehiculos', {
                    url: '/consultas',
                    templateUrl: 'html/vehiculos.html',
                    controller: 'ConsultasController'                    
                })
                .state('consultarutas', {
                    url: '/consultas',
                    templateUrl: 'html/rutas.html',
                    controller: 'ConsultasController'                    
                })
                .state('perfil', {
                    url: '/perfil',
                    templateUrl: 'html/perfil.html',
                    controller: 'PerfilController'                    
                })
                .state('salir', {
                    onEnter: function (SessionService) {                        
                        SessionService.destroy(); 
                        window.location = "login.html";
                    }                                     
                })  
                
        ;
    }]).factory('myHttpInterceptor', function($q, $injector) {
  return {
    // optional method
    'request': function(config) {      
      return config;
    },

    // optional method
   'requestError': function(rejection, response) {
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
                    return $q.reject(response);
      if (canRecover(rejection)) {
        return responseOrNewPromise;
      }
      return $q.reject(rejection);
    },
    // optional method
    'response': function(response) {
      // do something on success
      return response;
    },

    // optional method
   'responseError': function(rejection) {
      // do something on error
      if (canRecover(rejection)) {
        var error = function (response) {
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
                    return $q.reject(response);
                };
                return function (promise) {
                    return promise.then("success", error);
                };  
        return responseOrNewPromise;
      }
      return $q.reject(rejection);
    }
  };
});

$httpProvider.interceptors.push('myHttpInterceptor');


// alternatively, register the interceptor via an anonymous factory
$httpProvider.interceptors.push(function($q, dependency1, dependency2) {
  return {
   'request': function(config) {
       // same as above
    },

    'response': function(response) {
       // same as above
    }
  };
}).
        run(function ($rootScope, $state, SessionService, $http) {
            $rootScope.$state = $state;
            $rootScope.url_base = "http://localhost/ztrack/";
//          $rootScope.url_base = "http://localhost/ztrack/"; -> url del servidor - Document_Root
            SessionService.refresh();
        });
        
  