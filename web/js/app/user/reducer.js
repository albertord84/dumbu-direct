var initialUserState = {
	userName: '',
	password: '',
	pk: 0,
	priv: 0,
	logging: false,
	canLogIn: false,
	error: ''
};

var UserAction = {
	UPDATE: '[User] UPDATE',
	SET_NAME: '[User] SET_NAME',
	SET_PASS: '[User] SET_PASS',
	SET_PK: '[User] SET_PK',
	SET_PRIV: '[User] SET_PRIV',
	SET_LOGGING: '[User] SET_LOGGING',
	SET_CAN_LOG_IN: '[User] SET_CAN_LOG_IN',
	SET_ERROR: '[User] SET_ERROR'
};

function user(state, action) {
	if (typeof state === 'undefined') {
		return initialUserState;
	}
	switch (action.type) {
		case UserAction.UPDATE: {
			return Object.assign({}, state, action.payload);
		}
		case UserAction.SET_NAME: {
			return Object.assign({}, state, {userName: action.payload});
		}
		case UserAction.SET_PASS: {
			return Object.assign({}, state, {password: action.payload});
		}
		case UserAction.SET_PK: {
			return Object.assign({}, state, {pk: action.payload});
		}
		case UserAction.SET_PRIV: {
			return Object.assign({}, state, {priv: action.payload});
		}
		case UserAction.SET_LOGGING: {
			return Object.assign({}, state, {logging: action.payload});
		}
		case UserAction.SET_CAN_LOG_IN: {
			return Object.assign({}, state, {canLogIn: action.payload});
		}
		case UserAction.SET_ERROR: {
			return Object.assign({}, state, {error: action.payload});
		}
		default: {
			return state;
		}
	}
}

var reducer = Redux.combineReducers({
	user: user
});

var store = Redux.createStore(reducer);
