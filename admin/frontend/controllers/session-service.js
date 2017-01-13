var ztrack = angular.module('ztrack');
ztrack.service('SessionService', function ($http, $state) {
    
    this.isAuthenticated = false;
    this.isLoged = function () {       
        if (sessionStorage['ztrack.authenticated'] === false || sessionStorage['sessionId'] === undefined
            || sessionStorage['ztrack.authenticated'] === undefined || sessionStorage['sessionId'] === "undefined") {           
            return false;
        } 
        return true;
    };
    this.destroy = function () {
        this.id = null;
        this.user_id = null;
        this.isAuthenticated = false;
        delete localStorage['ztrack.user_id'];
        delete localStorage['ztrack.username'];
        delete localStorage['ztrack.token'];
        delete localStorage['ztrack.userType'];
        delete sessionStorage['ztrack.authenticated'];
        delete sessionStorage['sessionId'];       
    };

    this.save = function () {
        localStorage['ztrack.user_id'] = this.user_id;
        localStorage['ztrack.username'] = this.username;
        localStorage['ztrack.token'] = this.token;
        localStorage['ztrack.userType'] = this.userType;
        localStorage['ztrack.authenticated'] = true;
        $http.defaults.headers.common['user'] = this.user_id;
        $http.defaults.headers.common['token'] = this.token;
    };

    this.refresh = function () {
        if (this.isLoged()) {
            var info = {};
            info.user = {};
            info.user.id = localStorage['ztrack.user_id'];
            info.user.name = localStorage['ztrack.username'];
            info.token = localStorage['ztrack.token'];
            info.user.type = localStorage['ztrack.userType'];
        } else {
            
        }
    };   
    
    this.getInfo = function () {
        if (this.isLoged()) {
            var info = {};
            info.user = {};
            info.user.id = localStorage['ztrack.userid'];
            info.user.name = localStorage['ztrack.username'];
            info.token = localStorage['ztrack.token']; 
            return info;
        } else {
            
        }
    };    
    return this;

});