'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectMessage', [
        'ngResource', 'ngCookies', 'ngSanitize'
    ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log) {

        AppDumbu.scope = $scope;

        $('#logo p i.fa').pulsate({
            color: '#09f',
            reach: 20,
            speed: 1000,
            pause: 0,
            glow: true,
            repeat: 3,
            onHover: false
        });

        $scope.startDirects = function _startDirects() {
            AppDumbu.startDirects($scope, $log);
        };

    };

    AppDumbu.startDirects = function _startDirects($scope, $log) {
        $scope.processing = true;
    };

    AppDumbu.controller('MainController', [
        '$scope', '$resource', '$log',
        AppDumbu.MainController
    ]);

//})();
