AppDumbu.service('ComposeService', [
  '$resource', '$log', '$cookies',
  function _ComposeService($resource, $log, $cookies)
  {
    var self {

      startDirects: function _startDirects($scope)
      {
        $scope.processing = true;
      }

    };
    return self;
  }
])
