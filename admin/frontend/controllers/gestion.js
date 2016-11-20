/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('GestionController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {   
   $scope.title = "Gestión de Vehículos";
   $scope.activeTab = 1;
   
   $scope.setActiveTab = function (tab) {
        $scope.activeTab = tab;
        console.log(tab);
        console.log("entrooo");
    };
});


