angular.module('dumbu')

.controller('promo', [
    '$scope', '$log', 'promoService',
    function ($scope, $log, promoService)
    {
        promoService.getActive($scope);
        promoService.getSent($scope);
        promoService.getFailed($scope);
    }
]);
