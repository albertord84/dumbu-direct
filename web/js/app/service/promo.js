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
                var user_id = sender.id;
                $scope.sender = user_id;
                $scope.$digest();
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
