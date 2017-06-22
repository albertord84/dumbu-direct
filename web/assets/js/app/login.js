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
        $scope.authenticating = true;
        $http.post('/index.php/auth', {
          username: $scope.username,
          password: $scope.password
        }).then(function _afterSendCreds(response){
          if (response.data.status == 'OK') {
            setTimeout(function _wait2Secs(){
              $scope.authenticating = false;
              $scope.$digest();
              document.location.href = '/index.php/search';
            }, 2000);
          }
          else {
            $scope.authenticating = false;
            swal('Authentication error!',
              'Something is wrong with the provided Instagram credentials',
              'error');
          }
          $log.log(response.data);
        }).catch(function _someError(response){
          setTimeout(function _wait2Secs(){
            $scope.authenticating = false;
          }, 2000);
          swal('Authentication error!',
            'Something is wrong with the server',
            'error');
          $log.log(response.data);
        });
    };

    AppDumbu.controller('MainController', [
        '$scope', '$resource', '$log', '$cookies', '$http',
        AppDumbu.MainController
    ]);

//})();
