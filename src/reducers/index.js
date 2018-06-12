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
      return Object.assign({}, state, { isAdmin: action.payload });
  
    case 'SET_DIRECT_LIST': {
      return _.merge({}, state, { direct: { list: action.payload } });
    }
  
    case 'REMOVE_DIRECT_FROM_LIST': {
      const list = state.direct.list;
      const condition = (d) => {
        return d.id !== action.payload.id;
      }
      const newList = list.filter(condition);
      return _.merge({}, state, { direct: { list: newList } });
    }
  
    case 'ADD_DIRECT_TO_LIST': {
      const newList = _.concat(state.direct.list, action.payload);
      return _.merge({}, state, { direct: { list: newList } });
    }

    case 'SET_SELECTED_DIRECT': {
      return _.merge({}, state, { direct: { selected: action.payload } });
    }

    case 'MODIFYING_DIRECT': {
      return _.merge({}, state, { direct: { modifying: action.payload } });
    }

    case 'SET_DIRECT_TEXT': {
      return _.merge({}, state, { direct: { text: action.payload } });
    }
  
    default:
      return state;
  }
}

export default reducer;
