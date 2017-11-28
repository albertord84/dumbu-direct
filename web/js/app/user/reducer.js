var initialUserState = {
  userName: '',
  password: '',
  pk: 0,
  priv: 0
}

var UserAction = {
  UPDATE: '[User] UPDATE',
  SET_NAME: '[User] SET_NAME',
  SET_PASS: '[User] SET_PASS',
  SET_PK: '[User] SET_PK',
  SET_PRIV: '[User] SET_PRIV'
}

function user(state, action) {
  if (typeof state === 'undefined') {
    return initialUserState;
  }
  switch (action.type) {
    case UserAction.UPDATE: {
      return Object.assign({}, state, action.payload);
    }
    case UserAction.SET_NAME: {
      return Object.assign({}, state, { userName: action.payload });
    }
    case UserAction.SET_PASS: {
      return Object.assign({}, state, { password: action.payload });
    }
    case UserAction.SET_PK: {
      return Object.assign({}, state, { pk: action.payload });
    }
    case UserAction.SET_PRIV: {
      return Object.assign({}, state, { priv: action.payload });
    }
    default: {
      return state;
    }

  }
}
