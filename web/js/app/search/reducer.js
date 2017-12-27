var initialSearchState = {
	query: '',
	results: []
};

var SearchAction = {
	UPDATE: '[Search] UPDATE',
	SET_QUERY: '[Search] SET_QUERY',
	SET_RESULTS: '[Search] SET_RESULTS',
	REMOVE_RESULT: '[Search] REMOVE_RESULT',
	ADD_RESULT: '[Search] ADD_RESULT'
};

function search(state, action) {
	if (typeof state === 'undefined') {
		return initialSearchState;
	}
	switch (action.type) {
		case SearchAction.UPDATE: {
			return Object.assign({}, state, action.payload);
		}
		case SearchAction.SET_QUERY: {
			return Object.assign({}, state, {query: action.payload});
		}
		case SearchAction.SET_RESULTS: {
			return Object.assign({}, state, {results: action.payload});
		}
		// Espera en el payload el objeto a eliminar de la lista,
		// no el id de dicho objeto
		case SearchAction.REMOVE_RESULT: {
			return Object.assign({}, state, {
				results: _.without(state.results, action.payload)
			});
		}
		case SearchAction.ADD_RESULT: {
			return Object.assign({}, state, {
				results: state.results.concat([action.payload])
			});
		}
		default: {
			return state;
		}
	}
}

var reducer = Redux.combineReducers({
	user: user,
	search: search
});

window.store = Redux.createStore(reducer);
