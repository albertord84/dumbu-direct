/* global Dumbu, Bloodhound */

angular.module('dumbu')

.service('promoService', [
    '$log', '$http', '$location', '$timeout', '$resource',
    function ($log, $http, $location, $timeout, $resource)
    {
        var self = {

            getActive: function ($scope)
            {
                var Promo = $resource(Dumbu.siteUrl + '/promo/active');
                var promos = Promo.get(function () {
                    $scope.activePromos = promos.promos;
                    $scope.activeCount = promos.count;
                    $scope.activePage = Math.round(promos.count / 5);
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
            },
            
            senderTypeahead: function($scope)
            {
                var datasource = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: Dumbu.siteUrl + '/promo/sender/%QUERY',
                        wildcard: '%QUERY'
                    }
                });

                $('#sender-name').typeahead(null, {
                    name: 'sender-names',
                    hint: true,
                    highlight: true,
                    display: 'username',
                    source: datasource,
                    minLength: 3
                });

                $('#sender-name').on({
                    'typeahead:selected': function (e, datum) {
                        self.selectSender($scope, datum);
                    },
                    'typeahead:asyncrequest': function (jq, query, dsName) {
                        $('.async-loading').removeClass('hidden');
                    },
                    'typeahead:asyncreceive': function (jq, query, dsName) {
                        $('.async-loading').addClass('hidden');
                    }
                });
            },
            
            selectSender: function ($scope, sender)
            {
                $scope.senderId = sender.id;
                $scope.$digest();
            },

            moreActive: function($scope)
            {
                Dumbu.blockUI();
                var Promo = $resource(Dumbu.siteUrl + '/promo/active/:page', {
                    page: $scope.activePage
                });
                var promos = Promo.get(function () {
                    $timeout(function(){
                        for (var i = 0; i < promos.promos.length; i++) {
                            var promo = promos.promos[i];
                            $scope.activePromos.push(promo);
                        }
                        $scope.activeCount = promos.count;
                        $scope.activePage++;
                        Dumbu.unblockUI();
                    }, 1000);
                }, function () {
                    Dumbu.unblockUI();
                });
            }

        };
        return self;
    }
])

.filter('ts2human', function(){
    return function(x) {
        return moment(x*1000).format('MMM DD, HH:mm:ss')
    }
});
