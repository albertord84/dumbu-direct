'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectSearch', [
        'ngResource', 'ngCookies'
    ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log, $cookies, $service, $location)
    {

        AppDumbu.scope = $scope;

        $scope.selectedProfs = [{
            byline:"1.0m followers",
            fullName:"Gatorade",
            pk:"19933454",
            profPic:"http://ig-s-d-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/s150x150/14294715_1775264632730839_1178654801_a.jpg",
            username:"gatorade"
        },{
            byline:"142k followers",
            fullName:"IBM",
            pk:"589638973",
            profPic:"http://ig-s-b-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/11821783_1706498382903293_1516970730_a.jpg",
            username:"ibm"
        },{
            byline:"325k followers",
            fullName:"Microsoft Lumia",
            pk:"256679330",
            profPic:"http://ig-s-c-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/11313558_413215158851162_83535000_a.jpg",
            username:"microsoftlumia"
        }];

        $scope.removeSelectedProfile = function _removeSelectedProfile($event) {
            $service.removeSelectedProfile($event, $scope);
        };

        $scope.composeDirect = function _composeDirect() {
            $service.saveSelectedProfileIds($scope);
            // Redirigir hacia la pagina de componer el direct message
            var frm = $service.getRedirForm({
                action: '/index.php/compose',
                method: 'post',
                inputs: {
                    pks: $scope.pks
                }
            });
            frm.submit();
        };

        $scope.logout = function _logout() {
            $location.url('/index.php/logout');
        };

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
                $service.selectProfile($scope, datum);
            }
        });

    };

    AppDumbu.controller('MainController', [
        '$scope', '$resource', '$log', '$cookies', 'SearchService', '$location',
        AppDumbu.MainController
    ]);

//})();
