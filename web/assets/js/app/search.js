'use strict';

angular.module('DumbuDirectSearch', [ 'ngResource' ])

.controller('MainController', [

'$scope', '$resource', '$log',
function _MainController($scope, $resource, $log) {
  
  var igUsers = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: '/index.php/users/johndoe',
    remote: {
      url: '/index.php/users/%QUERY',
      wildcard: '%QUERY'
    }
  });

  $('#ref-prof').typeahead(null, {
    name: 'ig-profs',
    hint: true,
    highlight: true,
    display: 'username',
    source: igUsers,
    minLength: 3
  });

}

]);