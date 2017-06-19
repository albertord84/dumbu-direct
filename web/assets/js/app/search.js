'use strict';

//(function(){

    var AppDumbu = angular.module('DumbuDirectSearch', [ 'ngResource' ]);

    AppDumbu.MainController = function _MainController($scope, $resource, $log) {

        AppDumbu.mainCtrlScope = $scope;

        $scope.selectedProfs = [{
            byline:"1.0m followers",
            fullName:"Gatorade",
            pk:"19933454",
            profPic:"http://ig-s-d-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/s150x150/14294715_1775264632730839_1178654801_a.jpg",
            username:"gatorade"
        },{
            byline:"142k followers",
            fullName:"IBM",
            pk:"589638973",
            profPic:"http://ig-s-b-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/11821783_1706498382903293_1516970730_a.jpg",
            username:"ibm"
        },{
            byline:"325k followers",
            fullName:"Microsoft Lumia",
            pk:"256679330",
            profPic:"http://ig-s-c-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/11313558_413215158851162_83535000_a.jpg",
            username:"microsoftlumia"
        }];

        $scope.removeSelectedProfile = function _removeSelectedProfile($event) {
            AppDumbu.removeSelectedProfile($event, $scope);
        };

        $scope.composeDirect = function _composeDirect() {
            AppDumbu.composeDirect($scope);
        };
        
        var igUsers = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: '/index.php/users/johndoe',
            remote: {
                url: '/index.php/users/%QUERY',
                wildcard: '%QUERY'
            }
        });
        
        $('#ref-prof').typeahead(null, {
            name: 'ig-profs',
            hint: true,
            highlight: true,
            display: 'username',
            source: igUsers,
            minLength: 3
        });

        $('#ref-prof').on({
            'typeahead:selected': function(e, datum) {
                AppDumbu.selectProfile($scope, datum);
            }
        });
        
    };

    AppDumbu.selectProfile = function _selectProfile($scope, profile) {
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
        if (console) console.log("selected profile " + profile.username);
        $scope.selectedProfs.push({
            "pk": profile.pk,
            "profPic": profile.profile_pic_url,
            "username": profile.username,
            "fullName": profile.full_name,
            "byline": profile.byline
        });
        $scope.$digest();
        if (console) console.log($scope.selectedProfs.length + " profiles selected");
    };

    AppDumbu.removeSelectedProfile = function _removeSelectedProfile($event, $scope) {
        var selectedProfile = angular.element($event.target)
            .scope().profile;
        
        if (console) console.log('removing profile ' + selectedProfile.pk);

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

            if (console) console.log('profile ' + selectedProfile.pk + ' has been removed from list');
        });
    };

    AppDumbu.composeDirect = function _composeDirect($scope) {

    };

    AppDumbu.filter('cutFullName', function() {
        return function(name) {
            if ("" == name) {
                return '...';
            }
            if (new String(name).length > 20)
                return new String(name).substring(0, 19) + '...';
            else
                return name;
        };
    });

    AppDumbu.controller('MainController', [ 
        '$scope', '$resource', '$log', 
        AppDumbu.MainController 
    ]);
    
//})();
