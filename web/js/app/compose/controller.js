jQuery(function () {
	Rx.Observable.fromEvent(jQuery('#message'), 'keyup')
		.map(function (e) {
			return e.target.value;
		})
		.subscribe(function (message) {
			store.dispatch({type: ComposeAction.SET_MESSAGE, payload: message});
			store.dispatch({
				type: ComposeAction.SET_READY,
				payload: _.trim(message).length > 3
			});
		});

});
