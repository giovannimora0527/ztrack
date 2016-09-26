var ufps = angular.module('ufps');
ufps.service('SessionService', function ($http, $state) {
//    localStorage.clear();
    this.isAuthenticated = false;

    this.isLoged = function () {

        if (localStorage['sigeri.authenticated']) {
            return true;
        }
        return false;
    };

    this.create = function (info) {
        this.isAuthenticated = true;
        this.user_id = info.user.id;
        this.token = info.token;
        this.username = info.user.name;
        this.userType = info.user.type;
        this.save();
    };

    this.destroy = function () {
        this.id = null;
        this.user_id = null;
        this.isAuthenticated = false;
        delete localStorage['sigeri.user_id'];
        delete localStorage['sigeri.username'];
        delete localStorage['sigeri.token'];
        delete localStorage['sigeri.userType'];
        delete localStorage['sigeri.authenticated'];
        sessionStorage.clear();
    };

    this.save = function () {

        localStorage['sigeri.user_id'] = this.user_id;
        localStorage['sigeri.username'] = this.username;
        localStorage['sigeri.token'] = this.token;
        localStorage['sigeri.userType'] = this.userType;
        localStorage['sigeri.authenticated'] = true;

        $http.defaults.headers.common['user'] = this.user_id;
        $http.defaults.headers.common['token'] = this.token;
    };

    this.refresh = function () {
        if (this.isLoged()) {
            var info = {};
            info.user = {};
            info.user.id = localStorage['sigeri.user_id'];
            info.user.name = localStorage['sigeri.username'];
            info.token = localStorage['sigeri.token'];
            info.user.type = localStorage['sigeri.userType'];
            this.create(info);
            $state.go('home');
        } else {
            
        }
    };    

    return this;

});