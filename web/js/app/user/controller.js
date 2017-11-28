jQuery(function(){
  var reducer = Redux.combineReducers({
    user: user
  });

  var store = Redux.createStore(reducer);

  var userModel = {
    userName: ko.observable(''),
    password: ko.observable(''),
    pk: ko.observable(0),
    priv: ko.observable(0),
    logging: ko.observable(false),
    canLogIn: ko.observable(false)
  };

  ko.applyBindings(userModel);

  var userNameElem = jQuery('#username');
  var userNameObservable = Rx.Observable.fromEvent(userNameElem, 'keyup')
  .map(function(event){ return jQuery(event.target).val(); });

  userNameObservable.subscribe(function(value){
    store.dispatch({
      type: UserAction.SET_NAME, payload: value
    });
    Rx.Observable.of(store)
    .map(function(s){ return s.getState().user.userName; })
    .subscribe(userModel.userName);
  });
  
  var passwordElem = jQuery('#password');
  var passwordObservable = Rx.Observable.fromEvent(passwordElem, 'keyup')
  .map(function(event){ return jQuery(event.target).val(); });

  passwordObservable.subscribe(function(value){
    store.dispatch({
      type: UserAction.SET_PASS, payload: value
    });
    Rx.Observable.of(store)
    .map(function(s){ return s.getState().user.password; })
    .subscribe(userModel.password);
  });

  userNameObservable.filter(function(val){
    return _.trim(val).length > 2;
  });
});
