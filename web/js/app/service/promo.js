/* global Dumbu, Bloodhound, _, swal, moment */

angular.module('dumbu')

.service('promoService', [
    '$log', '$http', '$location', '$timeout', '$resource', '$interval',
    function ($log, $http, $location, $timeout, $resource, $interval)
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

            senderChangeTypeahead: function($scope)
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
                        $scope.newSender = datum;
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

            replaceSender: function ($scope)
            {
                $('div.sender-select').modal('hide');
                Dumbu.blockUI();
                var Promo = $resource(Dumbu.siteUrl + '/promo/:msgId/:userId', {
                    msgId: $scope.selectedPromo.id,
                    userId: $scope.newSender.id
                });
                Promo.save(function(){
                    $timeout(function(){
                        var i = _.findIndex($scope.activePromos, function(o){
                            return o.id === $scope.selectedPromo.id;
                        });
                        $scope.activePromos[i].sender = $scope.newSender;
                        Dumbu.unblockUI();
                    }, 1000);
                }, function(){
                    Dumbu.unblockUI();
                });
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
            },
            
            removePromo: function(promo, $scope)
            {
                swal({
                    title: 'Are you sure?',
                    html: "You are going to remove this promotion:<br><br>"+
                            "<b class=\"text-muted\">"+promo.msg_text.substring(0,30)+"...</b>",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Yes, do it!'
                }).then(function () {
                    Dumbu.blockUI();
                    var Promo = $resource(Dumbu.siteUrl + '/promo/:id', {
                        id: promo.id
                    });
                    Promo.delete(function () {
                        $timeout(function(){
                            $scope.activeCount--;
                            _.remove($scope.activePromos, function(o){
                                return o.id === promo.id;
                            });
                            Dumbu.unblockUI();
                        }, 1000);
                    }, function () {
                        Dumbu.unblockUI();
                    });
                });
            },
            
            setScope: function($scope)
            {
                self.$scope = $scope;
            },
            
            lastLogLines: function()
            {
                $log.log('buscando log...');
                if (angular.isUndefined(self.$scope.logLines)) {
                    self.$scope.logLines = [];
                }
                if (self.$scope.logLines.length >= 1000) {
                    self.$scope.logLines = [];
                }
                var LogLines = $resource(Dumbu.siteUrl + '/promo/status', {
                    log: new Date().getTime()
                });
                LogLines.get(function(response){
                    for (var i = 0; i < response.data.length; i++) {
                        self.$scope.logLines.push(response.data[i]);
                    }
                    var logLinesEl = $('#promo-log-container div.well.log-lines');
                    logLinesEl[0].scrollTop = logLinesEl[0].scrollHeight;
                });
            },
            
            getLogLines: function()
            {
                if (angular.isUndefined(self.$scope.logLines)) {
                    self.lastLogLines();
                }
                $interval(function(){
                    self.lastLogLines();
                }, 10000 /*1000 * 60 * 10*/);
            },
            
            collectFollowers: function(pk, $scope) {
                $log.log('collecting followers list for profile id: ' + pk);
                Dumbu.blockUI('This might take some time... Please, wait.');
                var Collector = $resource(Dumbu.siteUrl + '/collect/followers/:pk', {
                    pk: pk
                });
                Collector.save(function(){
                    Dumbu.unblockUI();
                }, function(response){
                    Dumbu.unblockUI();
                    swal({
                        type: 'error',
                        title: 'Lista de seguidores',
                        text: response.data.message
                    });
                });
            }

        };
        return self;
    }
])

.filter('ts2human', function(){
    return function(x) {
        return moment(x*1000).format('MMM DD, HH:mm:ss');
    };
});
