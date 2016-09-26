var ufps = angular.module('queries-service', []);

ufps.factory('QueriesService', function ($http, $rootScope, $q, toastr) {
    var queriesService = {};

    queriesService.executeRequest = function ($method, $url, $data, $params) {

        var deferred = $q.defer();
 
        $http({
            method: $method,
            url: $url,
            data: $data,
            params: $params
        }).success(function (data) {
            if ($method !== 'GET') {
                toastr.success(data.mensaje, "EXITO");
            }
            deferred.resolve(data);
        }).error(function (data) {
            if ($method !== 'GET') {
               toastr.error(data.mensaje, "ERROR");
            }

        });
        return deferred.promise;
    };

    return queriesService;
});