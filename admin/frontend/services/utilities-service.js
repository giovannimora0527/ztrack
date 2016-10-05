var ztrack = angular.module('utilities-service', []);

ztrack.factory('util', function ($http, $rootScope, $q) {
    var utilService = {};

    utilService.getItem = function (array, item) {
        for (i = 0; i < array.length; i++) {
            if (parseInt(array[i].id) === parseInt(item.id)) {
                return array[i];
            }
        }

        return null;
    };

    utilService.setItem = function (array, item) {
        for (i = 0; i < array.length; i++) {
            if (parseInt(array[i].id) == parseInt(item.id)) {
                array[i] = item;
                return true;
            }
        }
        
        return false;
    };

    utilService.getIndex = function (array, item) {
        for (i = 0; i < array.length; i++) {
            if (parseInt(array[i].id) == parseInt(item.id)) {
                return i;
            }
        }

        return -1;
    };

    return utilService;
});