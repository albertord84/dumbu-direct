angular.module('dumbu')

.controller('search', [
    '$scope', '$log', 'searchService',
    function ($scope, $log, searchService)
    {
        searchService.loadDefaultProfiles($scope);
        searchService.initTypeahead($scope);

        $scope.removeProfile = function (profile)
        {
            searchService.removeProfile(profile, $scope);
        };

        $scope.submit = function ()
        {
            $log.log(arguments);
            searchService.submit($scope);
        };

    }
]);
