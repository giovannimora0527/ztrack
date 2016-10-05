var ztrack = angular.module('ztrack');
ztrack.factory('AuthService', function ($http, SessionService, $rootScope) {
    var authService = {};

    authService.login = function (credentials) {
        return $http({
            method: 'POST',
            url: 'laravel/public/session/login',
            data: credentials
        });
    };

    authService.logout = function(){        
        return $http({
            method: 'POST',
            url: 'laravel/public/session/logout'
        });
    };
    
    authService.isAuthenticated = function () {
        return !!SessionService.userId;
    };

    authService.isAuthorized = function (authorizedRoles) {
        if (!angular.isArray(authorizedRoles)) {
            authorizedRoles = [authorizedRoles];
        }
        return (authService.isAuthenticated() &&
            authorizedRoles.indexOf(SessionService.userRole) !== -1);
    };    
   
    return authService;
});