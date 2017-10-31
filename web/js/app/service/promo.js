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
                    $timeout(function(){
                    	Dumbu.unblockUI();
					}, 3000);
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
                var promos = Promo.get(function () {
                    $scope.failedPromos = promos.promos;
					$scope.failedCount = promos.count;
					$scope.failedPage = Math.round(promos.count / 5);
					$timeout(function(){
						Dumbu.unblockUI();
					}, 3000);
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
                        var i = _.findIndex($scope.activePromos, { id: $scope.selectedPromo.id });
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
                            if (promo.failed==="1") {
                                $scope.failedCount--;
                                for (var i = 0; i < $scope.failedPromos.length; i++) {
                                    if ($scope.failedPromos[i].id===promo.id) { break; }
                                }
                                $scope.failedPromos.splice(i, 1);
                            }
                            else if (promo.failed!=="1") {
                                $scope.activeCount--;
                                for (var i = 0; i < $scope.activePromos.length; i++) {
                                    if ($scope.activePromos[i].id===promo.id) { break; }
                                }
                                $scope.activePromos.splice(i, 1);
                            }
                            Dumbu.unblockUI();
                        }, 1000);
                    }, function () {
                        Dumbu.unblockUI();
                    });
                }).catch(swal.noop);
            },
            
            setScope: function($scope)
            {
                self.$scope = $scope;
            },
            
            lastLogLines: function()
            {
                $log.log('%1 - buscando log...'
                    .replace(/\%1/, moment(new Date()).format('H:m:s')));
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
                }, 30000 /* 1000 * 60 * 10 */);
            },
            
            collectFollowers: function(pk, $scope) {
                $log.log('collecting followers list for profile id: ' + pk);
                Dumbu.blockUI('Please, wait...');
                var Collector = $resource(Dumbu.siteUrl + '/collect/followers/:pk', {
                    pk: pk
                });
                Collector.save(function(){
                    Dumbu.unblockUI();
                    $log.log('followers list for profile ' + pk + ' was collected');
                }, function(response){
                    Dumbu.unblockUI();
                    swal({
                        type: 'error',
                        title: 'Followers list',
                        text: response.data.message
                    });
                });
            },
            
            enqueuePromo: function (promo, $scope) {
                Dumbu.blockUI('After this, go to active promos and reload...');
                var Promo = $resource(Dumbu.siteUrl + '/promo/enqueue/:id', {
                    id: promo.id
                }, {
                    update: { method: 'PUT' }
                });
                Promo.update(function(response){
                    for (var i = 0; i < $scope.failedPromos.length; i++) {
                        if ($scope.failedPromos[i].id===promo.id) { break; }
                    }
                    $timeout(function(){
                        $scope.failedPromos.splice(i, 1);
                        Dumbu.unblockUI();
                    }, 3000);
                }, function(){
                    Dumbu.unblockUI();
                });
            },

			startPromo: function (promo, $scope) {
				Dumbu.blockUI('Changing promo status...');
				var Promo = $resource(Dumbu.siteUrl + '/promo/start/:id', {
					id: promo.id
				}, {
					update: { method: 'PUT' }
				});
				Promo.update(function(response){
					for (var i = 0; i < $scope.activePromos.length; i++) {
						if ($scope.activePromos[i].id===promo.id) { break; }
					}
					$timeout(function(){
						$scope.activePromos[i] = response.promo;
						Dumbu.unblockUI();
					}, 3000);
				}, function(){
					Dumbu.unblockUI();
				});
			},

			pausePromo: function (promo, $scope) {
				Dumbu.blockUI('Pausing the promo...');
				var Promo = $resource(Dumbu.siteUrl + '/promo/pause/:id', {
					id: promo.id
				}, {
					update: { method: 'PUT' }
				});
				Promo.update(function(response){
					for (var i = 0; i < $scope.activePromos.length; i++) {
						if ($scope.activePromos[i].id===promo.id) { break; }
					}
					$timeout(function(){
						$scope.activePromos[i] = response.promo;
						Dumbu.unblockUI();
					}, 3000);
				}, function(){
					Dumbu.unblockUI();
				});
			},

			modifyText: function (promo, $scope) {
				$('div.promo-text-change').modal('hide');
				Dumbu.blockUI('Changing promo text...');
				var Promo = $resource(Dumbu.siteUrl + '/promo/text/:msgId', {
					msgId: promo.id
				}, { update: { method: 'PUT' } });
				Promo.update({
					msgId: promo.id
				}, {
					text: $scope.modifiedText
				}, function(){
					$timeout(function(){
						var index = _.findIndex($scope.activePromos, { id: promo.id });
						$log.log('changed text of promo: ' + promo.id);
						$scope.activePromos[index].msg_text = $scope.modifiedText;
						Dumbu.unblockUI();
					}, 3000);
				}, function(){
					$log.log(arguments);
					$timeout(function(){
						Dumbu.unblockUI();
					}, 3000);
				});
            },
            
            todayStat: function() {
                var Promo = $resource(Dumbu.siteUrl + '/promo/today');
				Promo.get(function(data){
                    $timeout(function(){
                        self.$scope.todayPromos = data.results;
                    }, 1000);
				}, function(){
					$log.log(arguments);
				});
            },

            lastStat: function() {
                var Promo = $resource(Dumbu.siteUrl + '/promo/last');
				Promo.get(function(data){
                    $timeout(function(){
                        self.$scope.lastPromos = data.results;
                    }, 1000);
				}, function(){
					$log.log(arguments);
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
