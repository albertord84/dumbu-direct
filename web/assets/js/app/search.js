'use strict';

(function(){


    var AppDumbu = angular.module('DumbuDirectSearch', [ 'ngResource' ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log) {

        $scope.selectedProfs = [];
        
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

        $('#ref-prof').on({
            'typeahead:selected': function(e, datum) {
                AppDumbu.selectProfile($scope, datum);
            }
        });
        
    };

    AppDumbu.selectProfile = function _selectProfile($scope, profile) {
        if (console) console.log("selected profile " + profile.username);
        $scope.selectedProfs.push({
            "profPic": profile.profile_pic_url,
            "username": profile.username,
            "fullName": profile.full_name,
            "byline": profile.byline
        });
        $scope.$digest();
        if (console) console.log($scope.selectedProfs.length + " profiles selected");
    };

    AppDumbu.controller('MainController', [ 
        '$scope', '$resource', '$log', 
        AppDumbu.MainController 
    ]);
    
})();
