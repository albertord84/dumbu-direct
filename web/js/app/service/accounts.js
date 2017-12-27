angular.module('dumbu')

.service('accountsService', [
	'$log', '$timeout', '$resource',
	function ($log, $timeout, $resource)
	{
		return function(state, action) {

			switch (action.type) {

				case 'LOAD_ACCOUNTS':
					action.data.count = Dumbu.accounts.count;
					return [].concat(Dumbu.accounts.accounts);

				case 'SET_TOTAL_ACCOUNTS':
					return Object.assign({}, state, { count: action.total });

				case 'ADD_ACCOUNT':
					Dumbu.hideModal('#new-account');
					Dumbu.blockUI();
					$timeout(function () {
						var Account = $resource(Dumbu.siteUrl + '/account/');
						Account.save(Object.assign({}, action.data.newAccount, {
							created_at: moment().unix()
						}), function (data) {
							action.data = Object.assign({}, action.data, {
								accounts: action.data.accounts.concat([action.data.newAccount]),
								count: action.data.count + 1
							});
							setTimeout(function() {
								Dumbu.unblockUI();
							}, 1200);
						}, function () {
							Dumbu.unblockUI();
							$timeout(function() {
								$log.log(arguments);
								swal({
									type: 'error',
									title: 'Account creation error!',
									html: 'Something happened trying to add the new account<br>' +
									      'See browser console...'
								});
							}, 500);
						});
					}, 2000);
					return state;

				case 'REMOVE_ACCOUNT':
					swal({
						title: 'Are you sure?',
						html: "You are going to remove this account:<br><br>"+
						"<b class=\"text-muted\">"+action.data.account.username+"...</b>",
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						cancelButtonText: 'Cancel',
						confirmButtonText: 'Yes, do it!'
					}).then(function () {
						Dumbu.blockUI();
						setTimeout(function(){
							var Account = $resource(Dumbu.siteUrl + '/account/' + action.data.account.id);
							Account.delete(function () {
								action.data.state.accounts = _.difference(action.state.accounts, [ action.data.account ]);
								action.data.state.count = action.data.state.count - 1;
								$timeout(function() {
									Dumbu.unblockUI();
								}, 1200);
							}, function () {
								$log.log(arguments);
								Dumbu.unblockUI();
								setTimeout(function() {
									swal({
										type: 'error',
										title: 'Account deletion error!',
										html: 'Something happened trying to delete the account<br>' +
										      'See browser console...'
									});
								}, 500);
							});
						},2000);
					}).catch(swal.noop);
					return state;

				default:
					return state;

			}

		};
	}
])

.service('newAccountService', [
	'$log',
	function ($log) {
		return function(state, action) {
			switch (action.type) {
				case 'SET_NAME':
					return Object.assign({}, action.data.state.newAccount,
						{ userName: action.data.userName });

				case 'SET_PK':
					return Object.assign({}, action.data.state.newAccount,
						{ pk: action.data.pk });

				default:
					return state;
			}
		};
	}
])

.service('utils', [
	'newAccountService',
	function(newAccountService){
		return {
			setupUserAutocomplete: function () {
				var datasource = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: Dumbu.siteUrl + '/accounts/%QUERY',
						wildcard: '%QUERY'
					}
				});

				$('#account-name').typeahead(null, {
					name: 'account-names',
					hint: true,
					highlight: true,
					display: 'username',
					source: datasource,
					minLength: 3
				});

				$('#account-name').on({
					'typeahead:selected': function (e, datum) {
						$('.async-loading').addClass('hidden');
						store.dispatch({
							type: 'newAccount:SET_PK',
							data: {
								pk: datum.pk,
								state: store.getState()
							}
						});
						store.dispatch({
							type: 'newAccount:SET_NAME',
							data: {
								userName: datum.username,
								state: store.getState()
							}
						});
					},
					'typeahead:asyncrequest': function (jq, query, dsName) {
						store.dispatch({
							type: 'newAccount:SET_PK',
							data: {
								pk: undefined,
								state: store.getState()
							}
						});
						$('.async-loading').removeClass('hidden');
					},
					'typeahead:asyncreceive': function (jq, query, dsName) {
						$('.async-loading').addClass('hidden');
					}
				});
			}
		};
	}
]);
