'use strict';

//(function(){

var AppDumbu = angular.module('DumbuDirectSearch', [
    'ngResource', 'ngCookies'
]);

AppDumbu.MainController = function _MainController($scope, $log, $service, $location)
{

    AppDumbu.scope = $scope;

    // Quitar una vez terminada la etapa de desarrollo
    $scope.selectedProfs = [{
        "pk": "4239955376", 
        "profPic": "https://ig-s-a-a.akamaihd.net/hphotos-ak-xwa1/t51.2885-19/s150x150/15251735_1768912590039156_5995529154123005952_a.jpg", 
        "username": "dumbu.02", 
        "fullName": "Dumbu", 
        "byline": "1 mutual follower"
    }, {
        "pk": "4492293740", 
        "profPic": "https://ig-s-d-a.akamaihd.net/hphotos-ak-xwa1/t51.2885-19/s150x150/16122797_1831629543759927_3831911332626563072_a.jpg", 
        "username": "dumbu.08", 
        "fullName": "Dumbu", 
        "byline": "4255 followers"
    }, {
        "pk": "4542814483", 
        "profPic": "https://ig-s-a-a.akamaihd.net/hphotos-ak-xwa1/t51.2885-19/s150x150/16123160_1795657230699712_1231154969558646784_a.jpg", 
        "username": "dumbu.09", 
        "fullName": "Dumbu", 
        "byline": "8155 followers"
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
        'typeahead:selected': function (e, datum) {
            $service.selectProfile($scope, datum);
        }
    });

};

AppDumbu.controller('MainController', [
    '$scope', '$log', 'SearchService', '$location',
    AppDumbu.MainController
]);

//})();
