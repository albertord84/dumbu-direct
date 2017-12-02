jQuery(function () {
	var reducer = Redux.combineReducers({
		compose: composeReducer
	});

	var store = Redux.createStore(reducer);

	window.composeModel = {
		ready: ko.observable(false),
		message: ko.observable('')
	};

	ko.applyBindings(composeModel);

	store.subscribe(function () {
		_.forEach(store.getState().compose, function (val, prop) {
			_.invoke(composeModel, prop, val);
		});
	});

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
