/* global Dumbu */

angular.module('dumbu')

.service('promoService', [
    '$log', '$http', '$location', '$timeout', '$resource',
    function ($log, $http, $location, $timeout, $resource)
    {
        var self = {

            getActive: function ($scope)
            {
                var Promo = $resource(Dumbu.siteUrl + '/promo/active');
                var promos = Promo.query(function () {
                    $scope.activePromos = promos;
                });
            },

            getSent: function ($scope)
            {
                var Promo = $resource(Dumbu.siteUrl + '/promo/sent');
                var promos = Promo.query(function () {
                    $scope.sentPromos = promos;
                });
            },

            getFailed: function ($scope)
            {
                var Promo = $resource(Dumbu.siteUrl + '/promo/failed');
                var promos = Promo.query(function () {
                    $scope.failedPromos = promos;
                });
            }

        };
        return self;
    }
]);
