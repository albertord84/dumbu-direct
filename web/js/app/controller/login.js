angular.module('dumbu')

.controller('login', [
    '$scope', '$log', 'loginService',
    function ($scope, $log, loginService) {
        $scope.auth = function () {
            loginService.auth($scope);
        };
        loginService.bindFormKeys();
    }
]);
