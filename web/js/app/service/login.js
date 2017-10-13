/* global Dumbu */

angular.module('dumbu')

.service('loginService', [
    '$log', '$http', '$location', '$timeout',
    function ($log, $http, $location, $timeout)
    {
        var self = {
            
            auth: function ($scope)
            {
                $log.log('login user ' + $scope.username);
                Dumbu.blockUI();
                $http.post(Dumbu.siteUrl + '/auth', {
                    username: $scope.username,
                    password: $scope.password
                }).then(function (response){
                    $log.log(response.data);
                    Dumbu.unblockUI();
                    $timeout(function (){
                        window.location = Dumbu.siteUrl + '/search';
                    }, 1000);
                }, function (){
                    $log.log(arguments);
                    Dumbu.unblockUI();
                    swal('Authentication error!',
                    'Something is wrong with the provided Instagram credentials',
                    'error');
                });
            },

            bindFormKeys: function()
            {
                $('#loginForm input.input-lg').keypress(function(eventData){
                    if (eventData.keyCode !== 13) return;
                    if ($('#loginForm').hasClass('ng-valid')) {
                        $('#bt-auth').click();
                    }
                });
            }
            
        };
        return self;
    }
]);
