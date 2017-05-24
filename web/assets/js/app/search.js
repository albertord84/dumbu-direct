'use strict';

angular.module('DumbuDirectSearch', [ 'ngResource' ])

.controller('MainController', [

'$scope', '$resource', '$log',
function _MainController($scope, $resource, $log) {
  
  var igUsers = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: 'users/johndoe',
    remote: {
      url: 'users/%QUERY',
      wildcard: '%QUERY'
    }
  });

  $('#ref-prof').typeahead(null, {
    name: 'ig-profs',
    display: 'name',
    source: igUsers
  });

}

]);