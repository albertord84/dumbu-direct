jQuery(function(){
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

  ko.applyBindings(userModel);

  // Actualizar la UI
  store.subscribe(function(){
    var state = store.getState().user;
    _.forEach(state, function(val, prop){
      _.invoke(userModel, prop, val);
    });
  });

  var userNameElem = jQuery('#username');
  var userNameObservable = Rx.Observable.fromEvent(userNameElem, 'keyup')
  .map(function(event){ return jQuery(event.target).val(); });

  userNameObservable.subscribe(function(value){
    store.dispatch({ type: UserAction.SET_NAME, payload: value });
  });
  
  var passwordElem = jQuery('#password');
  var passwordObservable = Rx.Observable.fromEvent(passwordElem, 'keyup')
  .map(function(event){ return jQuery(event.target).val(); });

  passwordObservable.subscribe(function(value){
    store.dispatch({ type: UserAction.SET_PASS, payload: value });
  });

  userNameObservable.combineLatest(passwordObservable, function(u, p){
    return _.trim(u).length > 2 && _.trim(p).length > 2;
  }).subscribe(function(canLogIn) {
    store.dispatch({ type: UserAction.SET_CAN_LOG_IN, payload: canLogIn });
  });
  
  var btnElem = jQuery('#bt-auth');
  Rx.Observable.fromEvent(btnElem, 'click')
  .subscribe(function(e){
    store.dispatch({ type: UserAction.SET_LOGGING, payload: true });
  });
});
