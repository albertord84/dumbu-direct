import initialState from "../initialState";
import * as _ from "lodash";

const reducer = (state = initialState, action) => {
  switch (action.type) {
    case 'SET_USERNAME':
      return _.assign({}, state, { username: action.payload });
  
    case 'SET_PASSWORD':
      return _.assign({}, state, { password: action.payload });

    case 'SET_IS_LOGGING':
      return _.assign({}, state, { logging: action.payload });

    case 'SET_IS_SEARCHING':
      return _.assign({}, state, { searching: action.payload });

    case 'SET_LOGIN_ERROR':
      return _.assign({}, state, { loginError: action.payload });
  
    case 'SET_IS_LOGGED':
      return _.assign({}, state, { isLogged: action.payload });
  
    case 'SET_SESSION_COOKIES': {
      return _.merge({}, state, { session: { cookies: action.payload } });
    }
  
    case 'SET_USER_IS_ADMIN':
      return _.assign({}, state, { isAdmin: action.payload });
  
    case 'SET_DIRECT_LIST': {
      let newState = _.assign({}, state);
      newState.direct.list = action.payload;
      return newState;
    }
  
    case 'REMOVE_DIRECT_FROM_LIST': {
      let newState = _.assign({}, state);
      const list = newState.direct.list;
      const id = action.payload.id;
      const condition = (d) => {
        return d.id !== id;
      }
      const newList = list.filter(condition);
      newState.direct.list = newList;
      return newState;
    }
  
    case 'ADD_DIRECT_TO_LIST': {
      let newState = _.assign({}, state);
      const newList = _.concat(newState.direct.list, action.payload);
      newState.direct.list = newList;
      return newState;
    }

    case 'SET_SELECTED_DIRECT': {
      let newState = _.assign({}, state);
      newState.direct.selected = action.payload;
      return newState;
    }

    case 'MODIFYING_DIRECT': {
      let newState = _.assign({}, state);
      newState.direct.modifying = action.payload;
      return newState;
    }

    case 'SET_DIRECT_TEXT': {
      let newState = _.assign({}, state);
      newState.direct.text = action.payload;
      return newState;
    }
  
    default:
      return state;
  }
}

export default reducer;
