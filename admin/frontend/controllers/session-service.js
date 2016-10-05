var ztrack = angular.module('ztrack');
ztrack.service('SessionService', function ($http, $state) {
//    localStorage.clear();
    this.isAuthenticated = false;
    this.isLoged = function () {
        if (localStorage['ztrack.authenticated'] === 'true') {            
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
        delete localStorage['ztrack.user_id'];
        delete localStorage['ztrack.username'];
        delete localStorage['ztrack.token'];
        delete localStorage['ztrack.userType'];
        delete localStorage['ztrack.authenticated'];        
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
            this.create(info);
            //$state.go('home');
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