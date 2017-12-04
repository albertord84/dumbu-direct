var initialComposeState = {
	ready: false,
	message: ''
};

var ComposeAction = {
	UPDATE: '[Compose] UPDATE',
	SET_MESSAGE: '[Compose] SET_MESSAGE',
	SET_READY: '[Compose] SET_READY'
};

function composeReducer(state, action) {
	if (typeof state === 'undefined') {
		return initialComposeState;
	}
	switch (action.type) {
		case ComposeAction.UPDATE: {
			return Object.assign({}, state, action.payload);
		}
		case ComposeAction.SET_MESSAGE: {
			return Object.assign({}, state, {message: action.payload});
		}
		case ComposeAction.SET_READY: {
			return Object.assign({}, state, {ready: action.payload});
		}
		default: {
			return state;
		}
	}
}

var reducer = Redux.combineReducers({
	compose: composeReducer
});

store = Redux.createStore(reducer);
