jQuery(function () {
	var reducer = Redux.combineReducers({
		user: user,
		search: search
	});

	window.store = Redux.createStore(reducer);

	window.searchModel = {
		userName: ko.observable(''),
		pk: ko.observable(0),
		priv: ko.observable(0),

		query: ko.observable(''),
		results: ko.observableArray()
	};

	ko.applyBindings(searchModel);

	// Actualizar la UI
	store.subscribe(function () {
		var userState = store.getState().user;
		var searchState = store.getState().search;
		_.forEach(userState, function (val, prop) {
			_.invoke(searchModel, prop, val);
		});
		_.forEach(searchState, function (val, prop) {
			_.invoke(searchModel, prop, val);
		});
	});

	function initAutocomplete() {
		var datasource = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: Dumbu.siteUrl + '/search/followers/%QUERY',
				wildcard: '%QUERY'
			}
		});

		$('#ref-prof').typeahead(null, {
			name: 'profiles',
			hint: true,
			highlight: true,
			display: 'username',
			source: datasource,
			minLength: 3
		});

		$('#ref-prof').on({
			'typeahead:selected': function (e, datum) {
				store.dispatch({type: SearchAction.ADD_RESULT, payload: datum});
			},
			'typeahead:asyncrequest': function (jq, query, dsName) {
				$('.async-loading').removeClass('hidden');
			},
			'typeahead:asyncreceive': function (jq, query, dsName) {
				$('.async-loading').addClass('hidden');
			}
		});
	}

	Rx.Observable.fromEvent(jQuery(document), 'click')
		.filter(function (e) {
			return jQuery(e.target).hasClass('close remove-profile')
		})
		.subscribe(function (e) {
			var userName = jQuery(e.target).parent()
				.siblings('.panel-body').find('h4').text();
			var profile = _.find(store.getState().search.results, { username: userName });
			store.dispatch({type: SearchAction.REMOVE_RESULT, payload: profile});
		});

	initAutocomplete();
});
