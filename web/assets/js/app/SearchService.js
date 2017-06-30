AppDumbu.SearchService = AppDumbu.service('SearchService', [
  '$resource', '$log', '$cookies',
  function _SearchService($resource, $log, $cookies)
  {
    var self = {

      getRedirForm: function _getRedirForm(options)
      {
        var uid = _.uniqueId('frm_');
        var frm = document.createElement('FORM');
        $(frm).attr('id', uid);
        if (_.isPlainObject(options)) {
          if (!_.has(options, 'inputs')) {
            $(frm).attr(options)
          }
          else {
            var inputs = _.keys(options.inputs);
            for (var i = 0; i < inputs.length; i++) {
              var inputName = inputs[i];
              var inp = document.createElement('input');
              $(inp).attr('type', 'hidden');
              $(inp).attr('name', inputName);
              $(inp).attr('value', options.inputs[ inputName ]);
              $(frm).append(inp);
            }
            var attrs = _.keys(options);
            for (var i = 0; i < attrs.length; i++) {
              var v = _.get(options, attrs[i]);
              $(frm).attr(attrs[i], v);
            }
          }
        }
        $(document.body).append(frm);
        return $(frm);
      },

      removeSelectedProfile: function _removeSelectedProfile($event, $scope)
      {
        var selectedProfile = angular.element($event.target)
          .scope().profile;

        $log.log('removing profile ' + selectedProfile.pk);

        var panelParent = $($event.target).parents('.panel').parent();

        // Dar efecto de que se borro el perfil
        $(panelParent).fadeOut(800, function _afterFadeRemovedProfile(){
          // Eliminar del modelo de datos en el cliente
          _.remove($scope.selectedProfs, function(profile){
            return profile.pk == selectedProfile.pk;
          });
          $scope.$digest();

          // Eliminar tambien de la lista creada en el servidor
          // ...

          $log.log('profile ' + selectedProfile.pk + ' has been removed from list');
        });
      },

      saveSelectedProfileIds: function _saveSelectedProfileIds($scope)
      {
        // Salvar ids de los perfiles seleccionados
        var pks = [];
        for(var i = 0; i < $scope.selectedProfs.length; i++){
          var profile = $scope.selectedProfs[i];
          pks.push(profile.pk);
        };
        var pks = pks.join();
        $cookies.put('pks', pks);
        $scope.pks = pks;
        $log.log($cookies.get('pks'));
      },

      selectProfile: function _selectProfile($scope, profile)
      {
        var alreadySelected = _.find($scope.selectedProfs, function _findAlreadySelected(_profile)
        {
          return _profile.pk == profile.pk;
        });
        if (!_.isUndefined(alreadySelected)) {
          swal({
            title: 'Already choosen',
            html: 'You have already choosen this profile.<br>' +
                  'Choose a different one.',
            type: 'info',
            confirmButtonText: 'OK'
          });
          return;
        }
        if ($scope.selectedProfs.length > 4) {
          swal({
            title: 'Profile count reached',
            text: 'You can not choose more than 5 profiles',
            type: 'error',
            confirmButtonText: 'OK'
          });
          return;
        }
        $log.log("selected profile " + profile.username);
        $scope.selectedProfs.push({
          "pk": profile.pk,
          "profPic": profile.profile_pic_url,
          "username": profile.username,
          "fullName": profile.full_name,
          "byline": profile.byline
        });
        $scope.$digest();
        $log.log($scope.selectedProfs.length + " profiles selected");
      }

    };
    return self;
  }
]);
