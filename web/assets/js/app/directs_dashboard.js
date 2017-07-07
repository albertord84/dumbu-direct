'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectDashboard', [
        'ngResource', 'ngCookies', 'ngSanitize'
    ]);

    AppDumbu.MainController = function _MainController($scope, $log, $service) {

        AppDumbu.scope = $scope;

    };

    AppDumbu.controller('MainController', [
        '$scope', '$log', 'DirectsService',
        AppDumbu.MainController
    ]);

//})();
