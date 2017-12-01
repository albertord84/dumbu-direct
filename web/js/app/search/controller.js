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
		//
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

	function prepareSubmitForm() {
		var frm = jQuery('#search-form');
		frm.attr({
			'action': Dumbu.siteUrl + '/compose/message',
			'method': 'POST'
		});
		//
		var hiddenNames = document.createElement('input');
		jQuery(hiddenNames).attr({
			'type': 'hidden',
			'name': 'follower_names'
		});
		//
		var hiddenIds = document.createElement('input');
		jQuery(hiddenIds).attr({
			'type': 'hidden',
			'name': 'follower_ids'
		});
		//
		frm.append(hiddenIds);
		frm.append(hiddenNames);
		jQuery('body').append(frm);
		//
		return frm;
	}

	Rx.Observable.fromEvent(jQuery(document), 'click')
		.filter(function (e) {
			return jQuery(e.target).hasClass('close remove-profile')
		})
		.map(function (e) {
			return jQuery('button.remove-profile').index(e.target);
		})
		.subscribe(function (index) {
			var profile = store.getState().search.results[index];
			store.dispatch({type: SearchAction.REMOVE_RESULT, payload: profile});
		});

	Rx.Observable.fromEvent(jQuery(document), 'click')
		.filter(function (e) {
			return jQuery(e.target).hasClass('text-them')
		})
		.subscribe(function (e) {
			var frm = prepareSubmitForm();
			var selectedProfiles = store.getState().search.results;
			var profileIds = _.reduce(selectedProfiles, function(_concat, prof, i) {
				return prof.pk + ( i > 0 ? "," : "" ) + _concat;
			}, "");
			var profileNames = _.reduce(selectedProfiles, function(_concat, prof, i) {
				return prof.username + ( i > 0 ? "," : "" ) + _concat;
			}, "");
			frm.find('input[name=follower_ids]').val(profileIds);
			frm.find('input[name=follower_names]').val(profileNames);
			frm.submit();
		});

	initAutocomplete();
});
