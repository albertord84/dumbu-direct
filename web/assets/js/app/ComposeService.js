/* global AppDumbu */

AppDumbu.ComposeService = AppDumbu.service('ComposeService', [
    '$resource', '$log', '$cookies', '$http', '$location', 
    function _ComposeService($resource, $log, $cookies, $http, $location)
    {
        var self = {

            startDirects: function _startDirects($scope)
            {
                self.checkUserAuth($scope);
            },
            
            checkUserAuth: function _checkUserAuth($scope)
            {
                var uid = d_Session.user_id;
                
                function successCallback(resp)
                {
                    $log.log(resp.data);
                    var frm = $('#formCompose');
                    frm.attr({
                        'action': '/index.php/ddashboard',
                        'method': 'POST'
                    }).submit();
                }
                
                function errorCallback()
                {
                    swal({
                        title: 'Authentication error!',
                        html: '<p class="text-info">Something is wrong with the credentials<br>' 
                            + 'of the intended user.</p>'
                            + '<p class="text-danger">Will be redirected to the login page<br>'
                            + 'to check your identity in 5 secs.</p>',
                        timer: 5000,
                        type: 'error'
                    }).then(function _redirectToLogin(){
                        $location.url('/index.php/logout');
                    }, function _cancelTimer(){
                        
                    });
                }
                
                $http.post('/index.php/checkid', {
                    user_id: uid
                }).then(successCallback, errorCallback);
            }

    };
    return self;
  }
  
]);
