jQuery(function () {
	var reducer = Redux.combineReducers({
		user: user
	});

	var store = Redux.createStore(reducer);

	window.userModel = {
		userName: ko.observable(''),
		password: ko.observable(''),
		pk: ko.observable(0),
		priv: ko.observable(0),
		logging: ko.observable(false),
		canLogIn: ko.observable(false)
	};

	function auth() {
		jQuery.post(Dumbu.siteUrl + '/user/auth', {
			username: store.getState().user.userName,
			password: store.getState().user.password
		}, function (data) {
			document.location.href = Dumbu.siteUrl + '/search/followers';
		}).fail(function (res) {
			if (typeof console !== 'undefined') {console.log(arguments);}
			store.dispatch({type: UserAction.SET_LOGGING, payload: false});
		});
	}

	ko.applyBindings(userModel);

	// Actualizar la UI
	store.subscribe(function () {
		var state = store.getState().user;
		_.forEach(state, function (val, prop) {
			_.invoke(userModel, prop, val);
		});
	});

	var userNameElem = jQuery('#username');
	var userNameObservable = Rx.Observable.fromEvent(userNameElem, 'keyup')
		.map(function (event) {
			return jQuery(event.target).val();
		});

	userNameObservable.subscribe(function (value) {
		store.dispatch({type: UserAction.SET_NAME, payload: value});
	});

	var passwordElem = jQuery('#password');
	var passwordObservable = Rx.Observable.fromEvent(passwordElem, 'keyup')
		.map(function (event) {
			return jQuery(event.target).val();
		});

	passwordObservable.subscribe(function (value) {
		store.dispatch({type: UserAction.SET_PASS, payload: value});
	});

	userNameObservable.combineLatest(passwordObservable, function (u, p) {
		return _.trim(u).length > 2 && _.trim(p).length > 2;
	}).subscribe(function (canLogIn) {
		store.dispatch({type: UserAction.SET_CAN_LOG_IN, payload: canLogIn});
	});

	var btnElem = jQuery('#btAuth');
	Rx.Observable.fromEvent(btnElem, 'click')
		.subscribe(function (e) {
			store.dispatch({type: UserAction.SET_LOGGING, payload: true});
			auth();
		});

	Rx.Observable.fromEvent(userNameElem, 'keyup')
		.filter(function (e) {
			return e.keyCode === 13 && store.getState().user.canLogIn;
		})
		.subscribe(function (e) {
			btnElem.click();
		});

	Rx.Observable.fromEvent(passwordElem, 'keyup')
		.filter(function (e) {
			return e.keyCode === 13 && store.getState().user.canLogIn;
		})
		.subscribe(function (e) {
			btnElem.click();
		});
});
