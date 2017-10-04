angular.module('dumbu')

.service('searchService', [
  '$log', '$http', '$timeout',
  function ($log, $http, $timeout)
  {
    var self = {

      loadDefaultProfiles: function ($scope)
      {
        $http.get(Dumbu.baseUrl + '/js/app/data/profiles.json').then(function (response){
          $scope.profiles = [];
          if (response.data.length > 0) {
            for (var i = 0; i < response.data.length; i++) {
              var profile = new Profile();
              $scope.profiles.push(angular.copy(response.data[i], profile));
            }
          }
        }, function (){
          $log.log('No se pudo obtener los perfiles de muestra');
        });
      },

      initTypeahead: function ($scope)
      {
        var datasource = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: Dumbu.siteUrl + '/users/johndoe',
          remote: {
            url: Dumbu.siteUrl + '/users/%QUERY',
            wildcard: '%QUERY'
          }
        });

        $('#ref-prof').typeahead(null, {
          name: 'ig-profs',
          hint: true,
          highlight: true,
          display: 'username',
          source: datasource,
          minLength: 3
        });

        $('#ref-prof').on({
          'typeahead:selected': function (e, datum) {
            self.selectProfile($scope, datum);
          },
          'typeahead:asyncrequest': function (jq, query, dsName) {
            $('.async-loading').removeClass('hidden');
          },
          'typeahead:asyncreceive': function (jq, query, dsName) {
            $('.async-loading').addClass('hidden');
          }
        });

      },

      selectProfile: function ($scope, profile)
      {
        var pk = profile.pk;
        var alreadySelected = false;
        for (var i = 0; i < $scope.profiles.length; i++) {
          if ($scope.profiles[i].pk == pk) {
            alreadySelected = true;
            break;
          }
        }
        if (!alreadySelected && $scope.profiles.length < 5) {
          var selectedProfile = new Profile(profile.pk, profile.profile_pic_url,
            profile.username, profile.full_name, profile.byline);
          $scope.profiles.push(selectedProfile);
          $scope.$digest();
        }
      },

      removeProfile: function (profile, $scope)
      {
        _.remove($scope.profiles, function (p){
          return p.pk == profile.pk;
        });

        $log.log('profile ' + profile.pk + ' has been removed from list');
      },

      submit: function ($scope)
      {
        var followerIds = [];
        var followerNames = [];
        for (var i = 0; i < $scope.profiles.length; i++) {
          followerIds.push($scope.profiles[i].pk);
          followerNames.push($scope.profiles[i].username);
        }
        var frm = $('form[name=compose]');
        frm.find('input[name=follower_ids]').val(followerIds.join());
        $log.log(followerIds.join());
        frm.find('input[name=follower_names]').val(followerNames.join());
        $log.log(followerNames.join());
        $timeout(function (){
            frm.submit();
        }, 500);
      }

    };
    return self;
  }
]);
