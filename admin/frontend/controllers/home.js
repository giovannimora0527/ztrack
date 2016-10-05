/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ztrack = angular.module('ztrack');
ztrack.controller('HomeController', function ($rootScope, $scope, AuthService, SessionService, $state, QueriesService, toastr) {   
  
  if(!SessionService.isLoged()){
    $state.go('salir');
  }
  
});


