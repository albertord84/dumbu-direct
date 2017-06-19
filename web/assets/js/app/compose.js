'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectMessage', [ 
        'ngResource', 'ngCookies', 'ngSanitize'
    ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log) {

        AppDumbu.mainCtrlScope = $scope;

    };

    AppDumbu.controller('MainController', [ 
        '$scope', '$resource', '$log', 
        AppDumbu.MainController 
    ]);
    
//})();
