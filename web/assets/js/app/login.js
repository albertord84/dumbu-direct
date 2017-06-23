'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectLogin', [
        'ngResource', 'ngCookies'
    ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log, $cookies, $http) {

        AppDumbu.mainCtrlScope = $scope;

        $scope.authenticating = false;

        $scope.auth = function _auth() {
            AppDumbu.auth($scope, $http, $log);
        };

    };

    AppDumbu.auth = function _auth($scope, $http, $log) {
        AppDumbu.showLoadingOverlay();
        $scope.authenticating = true;
        $http.post('/index.php/auth', {
          username: $scope.username,
          password: $scope.password
        }).then(function _afterSendCreds(response){
          if (response.data.status == 'OK') {
            setTimeout(function _wait(){
              $scope.authenticating = false;
              $scope.$digest();
              $('#loginForm').attr('action', '/index.php/search').submit();
            }, 1000);
          }
          else {
            $scope.authenticating = false;
            swal('Authentication error!',
              'Something is wrong with the provided Instagram credentials',
              'error');
          }
          $log.log(response.data);
          AppDumbu.hideLoadingOverlay();
        }).catch(function _someError(response){
          setTimeout(function _wait2Secs(){
            $scope.authenticating = false;
          }, 2000);
          swal('Authentication error!',
            'Something is wrong with the server',
            'error');
          $log.log(response.data);
          AppDumbu.hideLoadingOverlay();
        });
    };

    AppDumbu.showLoadingOverlay = function _loadingOverlay()
    {
        // Tomado de http://jsfiddle.net/eys3d/741/
        var over = '<div id="loading-overlay">' +
            '<i id="loading-element" class="fa fa-hourglass" aria-hidden="true"></i>' +
            '</div>';
        $(over).appendTo('body');
    };

    AppDumbu.hideLoadingOverlay = function _hideLoadingOverlay()
    {
        $('#loading-overlay').remove();
    };

    AppDumbu.controller('MainController', [
        '$scope', '$resource', '$log', '$cookies', '$http',
        AppDumbu.MainController
    ]);

//})();
