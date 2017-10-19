$(function(){

    window.appState = {
        searchTerms: ko.observable(''),
        accounts: {
            accounts: ko.observableArray(),
            count: ko.observable(0)
        },
        newAccount: {
            username: ko.observable(''),
            password: ko.observable(''),
            pk: ko.observable(0),
            priv: ko.observable(0)
        }
    };
    
    ko.applyBindings(appState);

    function accounts(state, action) {
        if (typeof state === 'undefined') { return appState.accounts; }
        
        switch (action.type) {
            case 'LOAD_ACCOUNTS':
                _.forEach(Dumbu.accounts.accounts, function(o){
                    state.accounts.push(o);
                });
                return state;
            
            case 'SET_TOTAL_ACCOUNTS':
                state.count(action.total);
                return state;

            case 'ADD_ACCOUNT':
                return state;
            
            case 'REMOVE_ACCOUNT':
                return state;
            
            case 'REFRESH_ACCOUNTS':
                return state;

            case 'COLLECT_FOLLOWERS':
                return state;
            
            case 'CHANGE_PRIV':
                return state;

            default:
                return state;
        }
    };
    
    var reducer = Redux.combineReducers({
        accounts: accounts
    });
    
    window.store = Redux.createStore(reducer);

    store.dispatch({ type: 'LOAD_ACCOUNTS' });
    store.dispatch({
        type: 'SET_TOTAL_ACCOUNTS',
        total: Dumbu.accounts.count
    });
});
