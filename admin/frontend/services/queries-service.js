var ztrack = angular.module('queries-service', []);

ztrack.factory('QueriesService', function ($http, $rootScope, $q, toastr) {
    var queriesService = {};

    queriesService.executeRequest = function ($method, $url, $data, $params) {

        var deferred = $q.defer();

        $http({
            method: $method,
            url: $url,
            data: $data,
            params: $params
        }).success(function (data) {
            if ($method === 'POST') {
                if (data.success) {
                   //toastr.success(data.mensaje, "INFO");
                }
                else{
                  toastr.error(data.mensaje, "ERROR");  
                }               
            }
            deferred.resolve(data);
        }).error(function (data) {
            if ($method === 'POST') {
                toastr.error(data.mensaje, "ERROR");
            }

        });
        return deferred.promise;
    };

    return queriesService;
});