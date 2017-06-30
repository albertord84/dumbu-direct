'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectLogin', [
        'ngResource', 'ngCookies'
    ]);

    AppDumbu.MainController = function _MainController($scope, $log, $service) {

        AppDumbu.scope = $scope;

        $scope.authenticating = false;

        $scope.auth = function _auth() {
            $service.auth($scope);
        };

    };

    AppDumbu.showLoadingOverlay = function _loadingOverlay()
    {
        // Tomado de http://jsfiddle.net/eys3d/741/
        var over = '<div id="loading-overlay">' +
            '<img id="loading-element" src="/assets/img/loading.gif" />' +
            '</div>';
        $(over).appendTo('body');
    };

    AppDumbu.hideLoadingOverlay = function _hideLoadingOverlay()
    {
        $('#loading-overlay').remove();
    };

    AppDumbu.controller('MainController', [
        '$scope', '$log', 'LoginService',
        AppDumbu.MainController
    ]);

//})();
