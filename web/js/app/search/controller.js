jQuery(function () {

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
			var selectedProfiles = store.getState().search.results;
			var profileIds = [], profileNames = [];
			Rx.Observable.from(selectedProfiles).pluck('pk')
				.subscribe(function (id) { profileIds.push(id); });
			Rx.Observable.from(selectedProfiles).pluck('username')
				.subscribe(function (username) { profileNames.push(username); });
			jQuery('input[name=follower_ids]').val(profileIds.join(','));
			jQuery('input[name=follower_names]').val(profileNames.join(','));
			jQuery('#search-form').submit();
		});

	initAutocomplete();
});
