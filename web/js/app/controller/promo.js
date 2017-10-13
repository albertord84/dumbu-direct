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
    }
])

.controller('promoComposer', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.senderTypeahead($scope);
    }
]);
