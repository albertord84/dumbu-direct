'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectLogin', [
        'ngResource', 'ngCookies'
    ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log, $cookies) {

        AppDumbu.mainCtrlScope = $scope;

    };

    AppDumbu.controller('MainController', [
        '$scope', '$resource', '$log', '$cookies',
        AppDumbu.MainController
    ]);

//})();
