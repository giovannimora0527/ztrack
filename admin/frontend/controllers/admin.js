/**
 * Archivo principal aplicación angular
 * Actualizado para el funcionamiento con AngularJS v1.3.4
 * Dependencias de la aplicación y estados (ui.router)
 *
 * @author Giovanni Mora
 * @email giovannimora0527@gmail.com
 * @version 1.1
 */
var ufps = angular.module('ufps', ['ui.bootstrap', 'ngCookies', 'ui.router', 'kubesoft-directives', 'queries-service', 'formValidationService', 'ngAnimate', 'toastr', 'anchorScrollOffset']);
ufps.config(['$stateProvider', '$urlRouterProvider', '$httpProvider',
    function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise('home');
        $stateProvider                 
                .state('home', {
                    url: '/home',
                    templateUrl: 'frontend/html/home.html',
                    controller: 'HomeController'                    
                })
                .state('admin', {                    
                    url: '/administrador',
                    templateUrl: 'frontend/administrador/pages/index.html',
                    controller: 'AdminController'
                })
                .state('admin.home', {
                    url: '/admin_home',
                    templateUrl: 'frontend/administrador/pages/home.html',
                    controller: 'AppController',
                    onEnter: function (SessionService, $state) {
                        if (!SessionService.isLoged()) {
                           $state.go('home');
                        }
                    }
                })
                .state('admin.home.inicio', {
                    url: '/inicio',
                    templateUrl: 'frontend/administrador/pages/inicio.html',
                    controller: 'InicioController'                    
                })
                .state('admin.home.userprofile', {
                    url: '/user_profile',
                    templateUrl: 'frontend/administrador/pages/userprofile.html',
                    controller: 'InicioController'                    
                })
                .state('admin.home.configprofile', {
                    url: '/config_profile',
                    templateUrl: 'frontend/administrador/pages/configprofile.html',
                    controller: 'InicioController'                    
                })
                .state('admin.home.nofound', {
                    url: '/no_found_projects',
                    templateUrl: 'frontend/administrador/paginas/nofoundprojects.html',
                    controller: 'NoFoundController'                    
                })
                .state('admin.home.crearproyecto', {
                    url: '/crear_proyecto',
                    templateUrl: 'frontend/administrador/paginas/crearproyecto.html',
                    controller: 'ProyectoController'                    
                })
                .state('admin.home.eliminarproyecto', {
                    url: '/eliminar_proyecto',
                    templateUrl: 'frontend/administrador/paginas/eliminarproyecto.html',
                    controller: 'DeleteController'                    
                })
                .state('admin.home.crearcronograma', {
                    url: '/crear_cronograma',
                    templateUrl: 'frontend/administrador/paginas/crearcronograma.html',
                    controller: 'CronogramaController'                    
                })
                .state('admin.home.editarcronograma', {
                    url: '/editar_cronograma',
                    templateUrl: 'frontend/administrador/paginas/editarcronograma.html',
                    controller: 'CronogramaController'                    
                })
                .state('admin.home.vercronograma', {
                    url: '/ver_cronograma',
                    templateUrl: 'frontend/administrador/paginas/vercronograma.html',
                    controller: 'CronogramaController'                    
                })
        ;
    }]).
//        config(function ($httpProvider) {
//            var interceptor = function ($q, $injector) {
//                var success = function (response) {
//                    return response;
//                };
//                var error = function (response) {
//                    if (response.status === 401) {
//                        $injector.invoke(function ($http, SessionService) {
//                            SessionService.destroy();
//                            SessionService.redirectToLogin();
//                        });
//                    } else if (response.status === 403) {
//                        $injector.invoke(function ($http, SessionService) {
//                            SessionService.unauthorized();
//                        });
//                    }
//                    return $q.reject(response);
//                };
//                return function (promise) {
//                    return promise.then(success, error);
//                };
//            };
//            $httpProvider.responseInterceptors.push(interceptor);
//        }).
factory('myHttpInterceptor', function($q, $injector) {
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
                    return promise.then(success, error);
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
            $rootScope.url_base = "http://localhost/sigeri/";
            SessionService.refresh();
        });
        
  