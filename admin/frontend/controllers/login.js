var ufps = angular.module('ufps');
ufps.controller('LoginController', function ($scope, $rootScope, AuthService, SessionService, $state, $http, $window, FormValidationService, QueriesService, toastr) {

    $scope.credentials = {};
    $scope.session = SessionService;
    $scope.formValidation = FormValidationService;
    $scope.usuario = {};
    $scope.active = true;
    $scope.formLogin = {};
    
    $scope.login = function () {        
        if ($scope.formLogin.$invalid) {
            return;
        }

        if (!$scope.credentials.password) {
            $scope.credentials.password = $("#password").val();
        }

        AuthService.login($scope.credentials)
                .success(function (data) {
                    SessionService.create(data.data.info);
                    toastr.success(data.data.info.user.name, "BIENVENIDO");                    
                    $state.go('admin');
                }).
                error(function (data) {
                    toastr.error(data.data.mensaje, "ERROR");
                });
    };

    $scope.logout = function () {        
        AuthService.logout().
                success(function (data) {
                    SessionService.destroy();
                    $state.go('home');
                    toastr.success(data.data.mensaje, "EXITO");
                }).
                error(function () {

                });
    };


    $scope.cambiarpass = function () {
        if ($scope.usuario.pass1 === $scope.usuario.pass2)
        {
            $params = {
                pass1: $scope.usuario.pass1,
                pass2: $scope.usuario.pass2
            };

            QueriesService.executeRequest('POST', $rootScope.url_base + 'laravel/public/users/changepassword', null, $params)
                    .then(function (result) {
                        if (result.success) {
                            $scope.usuario = {};
                            $state.go('login');
                        }
                    });
        } else {
            alert("Las contraseñas no coinciden");
        }
    };

});