jQuery(function () {
	store.subscribe(function () {
		var state = store.getState().compose;
		if (state.ready) {
			jQuery('button[type=submit]').attr('disabled', false);
			jQuery('input[name=message]').val(state.message);
		} else {
			jQuery('button[type=submit]').attr('disabled', true);
		}
	});
});
