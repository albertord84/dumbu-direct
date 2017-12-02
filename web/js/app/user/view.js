jQuery(function () {
	store.subscribe(function () {
		var state = store.getState().user;
		if (state.canLogIn) {
			jQuery('#btAuth').attr('disabled', false);
		} else {
			jQuery('#btAuth').attr('disabled', true);
		}
		if (state.logging) {
			jQuery('#btAuth').addClass('hidden');
			jQuery('#loginForm img.loading').removeClass('hidden');
		} else {
			jQuery('#btAuth').removeClass('hidden');
			jQuery('#loginForm img.loading').addClass('hidden');
		}
		if (state.error==='') {
			jQuery('#loginForm div.alert-danger').addClass('hidden');
		} else {
			jQuery('#loginForm div.alert-danger').removeClass('hidden');
			jQuery('#loginForm div.alert-danger span.text').html(state.error);
		}
	});
});
