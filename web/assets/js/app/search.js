'use strict';

(function(){

    var AppDumbu = angular.module('DumbuDirectSearch', [ 'ngResource' ]);

    AppDumbu.MainController = function MainController($scope, $resource, $log) {
        
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
        
    };

    AppDumbu.controller('MainController', [ 
        '$scope', '$resource', '$log', 
        AppDumbu.MainController 
    ]);
    
})();
