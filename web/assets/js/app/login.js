'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectLogin',
    [
        'ngResource', 'ngCookies'
    ]);

    AppDumbu.MainController = function _MainController($scope, $log, $service)
    {

        AppDumbu.scope = $scope;

        $scope.validMail = false;
        $scope.authenticating = false;

        $scope.auth = function _auth()
        {
            $service.auth($scope);
        };

        $scope.inputKeypress = function _inputKeypress($event)
        {
            if ($event.keyCode === 13 && $scope.loginForm.$valid)
            {
                $scope.auth();
            }
        };

        $scope.$watch('loginForm', function _watchLoginForm(newVal, oldVal)
        {
            if (_.has(newVal, 'username.newValidator')) return;
            if (_.has(newVal, 'username.$validators.email'))
            {
                $scope.loginForm.username.newValidator = true;
                $scope.loginForm.username.$validators.email = function _newValidator(val)
                {
                    return $service.validMail(val);
                }
            }
        });

    };

    AppDumbu.showLoadingOverlay = function _loadingOverlay()
    {
        // Tomado de http://jsfiddle.net/eys3d/741/
        var over = '<div id="loading-overlay">' +
            '<img id="loading-element" src="assets/img/loading.gif" />' +
            '</div>';
        $(over).appendTo('body');
    };

    AppDumbu.hideLoadingOverlay = function _hideLoadingOverlay()
    {
        $('#loading-overlay').remove();
    };

    AppDumbu.controller('MainController',
    [
        '$scope', '$log', 'LoginService',
        AppDumbu.MainController
    ]);

//})();
