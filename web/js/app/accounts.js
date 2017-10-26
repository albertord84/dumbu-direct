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
            pk: ko.observable(''),
            priv: ko.observable(0)
        }
    };
    
    ko.applyBindings(appState);

    ////////////////////////////////////////////////////////////////////////////////////
    // ACCOUNTS REDUCER
    ////////////////////////////////////////////////////////////////////////////////////

    function newAccount(state, action) {
        if (typeof state === 'undefined') { return appState.newAccount; }

        switch (action.type) {
            case 'SET_NEW_ACCOUNT_NAME':
                state.userName(action.userName);
                return state;
                
            case 'SET_NEW_ACCOUNT_PK':
                state.pk(action.pk);
                return state;

            default:
                return state;
        }
    }

    function accounts(state, action) {
        if (typeof state === 'undefined') { return appState.accounts; }
        
        switch (action.type) {
            case 'LOAD_ACCOUNTS':
                state(Array.concat(Dumbu.accounts.accounts));
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
                swal({
                    title: 'Are you sure?',
                    html: "You are going to remove this account:<br><br>"+
                            "<b class=\"text-muted\">"+action.payload.data.username+"...</b>",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Yes, do it!'
                }).then(function () {
                    Dumbu.blockUI();
                    setTimeout(function(){
                        jQuery.ajax(Dumbu.siteUrl + '/account/' + action.payload.data.id, {
                            type: 'DELETE',
                            success: function(data){
                                state.accounts.remove(action.payload.data);
                                var c = Math.abs(state.count()) - 1;
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
                                        title: 'Account deletion error!',
                                        text: response.responseJSON.message
                                    });
                                }, 500);
                            }
                        });
                    },2000);
                }).catch(swal.noop);
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
    }

    ////////////////////////////////////////////////////////////////////////////////////
    // jQuery STUFF
    ////////////////////////////////////////////////////////////////////////////////////
    
    var datasource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: Dumbu.siteUrl + '/accounts/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#account-name').typeahead(null, {
        name: 'account-names',
        hint: true,
        highlight: true,
        display: 'username',
        source: datasource,
        minLength: 3
    });

    $('#account-name').on({
        'typeahead:selected': function (e, datum) {
            store.dispatch({
                type: 'SET_NEW_ACCOUNT_NAME',
                userName: datum.username
            });
            store.dispatch({
                type: 'SET_NEW_ACCOUNT_PK',
                pk: datum.pk
            });
        },
        'typeahead:asyncrequest': function (jq, query, dsName) {
            store.dispatch({
                type: 'SET_NEW_ACCOUNT_PK',
                pk: undefined
            });
            $('.async-loading').removeClass('hidden');
        },
        'typeahead:asyncreceive': function (jq, query, dsName) {
            $('.async-loading').addClass('hidden');
        }
    });
    
    ////////////////////////////////////////////////////////////////////////////////////
    // REDUX STORE CREATION
    ////////////////////////////////////////////////////////////////////////////////////

    var reducer = Redux.combineReducers({
        accounts: accounts,
        newAccount: newAccount
    });
    
    window.store = Redux.createStore(reducer);

    store.dispatch({ type: 'LOAD_ACCOUNTS' });
    store.dispatch({
        type: 'SET_TOTAL_ACCOUNTS',
        total: Dumbu.accounts.count
    });
});
