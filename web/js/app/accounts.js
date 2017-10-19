$(function(){

    ////////////////////////////////////////////////////////////////////////////////////
    // APPLICATION STATE
    ////////////////////////////////////////////////////////////////////////////////////

    window.appState = {
        searchTerms: ko.observable(''),
        accounts: {
            accounts: ko.observableArray(),
            count: ko.observable(0)
        },
        newAccount: {
            userName: ko.observable(''),
            password: ko.observable(''),
            pk: ko.observable(0),
            priv: ko.observable(0)
        }
    };
    
    ko.applyBindings(appState);

    ////////////////////////////////////////////////////////////////////////////////////
    // ACCOUNTS REDUCER
    ////////////////////////////////////////////////////////////////////////////////////

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
                jQuery('#new-account').modal('hide');
                Dumbu.blockUI();
                setTimeout(function(){
                    jQuery.ajax(Dumbu.siteUrl + '/account/', {
                        type: 'POST',
                        data: {
                            username: appState.newAccount.userName(),
                            pk: appState.newAccount.pk(),
                            password: appState.newAccount.password(),
                            priv: appState.newAccount.priv(),
                            created_at: moment().unix()
                        },
                        success: function(data){
                            state.accounts.push(data.newAccount);
                            var c = Math.abs(state.count()) + 1;
                            state.count(c);
                            setTimeout(function() {
                                Dumbu.unblockUI();
                            }, 1200);
                        },
                        error: function(response){
                            Dumbu.unblockUI();
                            setTimeout(function() {
                                swal({
                                    type: 'error',
                                    title: 'Account creation error!',
                                    text: response.responseJSON.message
                                });
                            }, 500);
                        }
                    });
                },2000);
                return state;
            
            case 'REMOVE_ACCOUNT':
                return state;
            
            case 'REFRESH_ACCOUNTS':
                Dumbu.blockUI('Not implemented yet...');
                setTimeout(function(){
                    Dumbu.unblockUI();
                }, 2000);
                return state;

            case 'COLLECT_FOLLOWERS':
                return state;
            
            case 'CHANGE_PRIV':
                return state;

            default:
                return state;
        }
    };

    ////////////////////////////////////////////////////////////////////////////////////
    // jQuery STUFF
    ////////////////////////////////////////////////////////////////////////////////////


    
    ////////////////////////////////////////////////////////////////////////////////////
    // REDUX STORE CREATION
    ////////////////////////////////////////////////////////////////////////////////////

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
