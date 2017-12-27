angular.module('dumbu')

.controller('accounts', [
	'$scope', '$log', 'accountsService', 'newAccountService', 'utils',
	function ($scope, $log, accountsService, newAccountService, utils)
	{
		$scope.state = {
			accounts: [],
			count: 0,
			searchTerms: '',
			newAccount: {
				userName: '',
				password: '',
				pk: '',
				priv: 0
			}
		};

		$scope.store = {
			dispatch: function (action) {
				var domain = action.type.split(':')[0];
				var state = $scope.state[domain];
				var actionType = action.type.split(':')[1];
				var service = eval(domain + 'Service');
				var newState = service(state, { type: actionType, data: action.data });
				$scope.state[domain] = newState;
			},
			getState: function () {
				return $scope.state;
			}
		};

		window.store = $scope.store;
		store.dispatch({ type: 'accounts:LOAD_ACCOUNTS', data: $scope.state });

		utils.setupUserAutocomplete();
	}
]);
