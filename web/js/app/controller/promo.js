angular.module('dumbu')

.controller('promoBrowser', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.getActive($scope);
        promoService.getSent($scope);
        promoService.getFailed($scope);
    }
])

.controller('promoComposer', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.senderTypeahead($scope);
    }
]);
