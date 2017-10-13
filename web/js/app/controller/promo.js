angular.module('dumbu')

.controller('promoBrowser', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.getActive($scope);
        promoService.getSent($scope);
        promoService.getFailed($scope);

        $scope.moreActive = function()
        {
            promoService.moreActive($scope);
        }
    }
])

.controller('promoComposer', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.senderTypeahead($scope);
    }
]);
