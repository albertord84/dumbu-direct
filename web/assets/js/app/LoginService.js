AppDumbu.LoginService = AppDumbu.service('LoginService', [
  '$http', '$log',
  function _LoginService($http, $log)
  {
    var self = {

      auth: function _auth($scope) {
        AppDumbu.showLoadingOverlay();
        $scope.authenticating = true;
        $http.post(AppDumbu.ajaxUrl + '/auth', {
          username: $scope.username,
          password: $scope.password
        }).then(function _authSuccess(response){
          if (response.data.status == 'OK') {
            setTimeout(function _wait(){
              $scope.authenticating = false;
              $scope.$digest();
              $('#loginForm').attr('action', AppDumbu.ajaxUrl + '/search').submit();
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
        }, function _authError(response){
          setTimeout(function _wait2Secs(){
            $scope.authenticating = false;
          }, 2000);
          swal('Authentication error!',
            'Something is wrong with the server',
            'error');
          $log.log(response.data);
          AppDumbu.hideLoadingOverlay();
        });
      },

      validMail: function _validMail(mail) {
        mail = new String(mail).toLowerCase();
        // RegExp tomada de http://stackoverflow.com/questions/46155/
        //   validate-email-address-in-javascript
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(mail);
      }

    }
    return self;
  }
]);
