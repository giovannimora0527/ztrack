var ufps = angular.module('ufps');
ufps.controller('UserController', function ($scope, $rootScope, AuthService, SessionService, $state, $http, $window, FormValidationService) {

$scope.usuario = {};
$scope.formValidation = FormValidationService;

});