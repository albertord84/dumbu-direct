angular.module('dumbu')

.controller('promoBrowser', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.getActive($scope);
        promoService.getSent($scope);
        promoService.getFailed($scope);
        promoService.senderChangeTypeahead($scope);

        $scope.moreActive = function()
        {
            promoService.moreActive($scope);
        };

        $scope.changeSenderDialog = function(promo)
        {
            $scope.selectedPromo = promo;
            $('div.sender-select').modal('show');
        };
        
        $scope.replaceSender = function()
        {
            promoService.replaceSender($scope);
        };
        
        $scope.removePromo = function(promo)
        {
            promoService.removePromo(promo, $scope);
        };

		$scope.refreshActive = function() {
			Dumbu.blockUI();
			promoService.getActive($scope);
		};

		$scope.refreshFailed = function() {
			Dumbu.blockUI();
			promoService.getFailed($scope);
		};

		$scope.collectFollowers = function(pk) {
            promoService.collectFollowers(pk, $scope);
        };
        
        $scope.enqueuePromo = function(promo) {
            promoService.enqueuePromo(promo, $scope);
        };

		$scope.startPromo = function (promo) {
			promoService.startPromo(promo, $scope);
		};

		$scope.pausePromo = function (promo) {
			promoService.pausePromo(promo, $scope);
		};

		$scope.editText = function (promo) {
			promoService.editText(promo, $scope);
		};
    }
])

.controller('promoComposer', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.senderTypeahead($scope);
    }
])

.controller('promoLog', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.setScope($scope);
        promoService.getLogLines();
    }
]);
