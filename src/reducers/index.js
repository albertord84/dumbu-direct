import initialState from "../initialState";
import * as _ from "lodash";

const reducer = (state = initialState, action) => {
  switch (action.type) {
    case 'SET_USERNAME': {
      return _.assign({}, state, { username: action.payload });
    }
  
    case 'SET_PASSWORD': {
      return _.assign({}, state, { password: action.payload });
    }

    case 'SET_IS_LOGGING': {
      return _.assign({}, state, { logging: action.payload });
    }

    case 'SET_IS_SEARCHING': {
      return _.assign({}, state, { searching: action.payload });
    }

    case 'SET_LOGIN_ERROR': {
      return _.assign({}, state, { loginError: action.payload });
    }
  
    case 'SET_IS_LOGGED': {
      return _.assign({}, state, { isLogged: action.payload });
    }
  
    case 'SET_SESSION_COOKIES': {
      const session = _.assign({}, state.session, { cookies: action.payload });
      return _.assign({}, state, { session: session });
    }
  
    case 'SET_USER_IS_ADMIN': {
      return _.assign({}, state, { isAdmin: action.payload });
    }
  
    case 'SET_DIRECT_LIST': {
      const direct = _.assign({}, state.direct, { list: action.payload });
      return _.assign({}, state, { direct: direct });
    }
  
    case 'REMOVE_DIRECT_FROM_LIST': {
      const list = state.direct.list;
      const id = action.payload.id;
      const condition = (direct) => direct.id !== id;
      const filtered = list.filter(condition);
      const direct = _.assign({}, state.direct, { list: filtered });
      return _.assign({}, state, { direct: direct });
    }
  
    case 'ADD_DIRECT_TO_LIST': {
      const augmented = _.concat(state.direct.list, action.payload);
      const direct = _.assign({}, state.direct, { list: augmented });
      return _.assign({}, state, { direct: direct });
    }

    case 'SET_SELECTED_DIRECT': {
      const direct = _.assign({}, state.direct, { selected: action.payload });
      return _.assign({}, state, { direct: direct });
    }

    case 'MODIFYING_DIRECT': {
      const direct = _.assign({}, state.direct, { modifying: action.payload });
      return _.assign({}, state, { direct: direct });
    }

    case 'SET_DIRECT_TEXT': {
      const direct = _.assign({}, state.direct, { text: action.payload });
      return _.assign({}, state, { direct: direct });
    }
  
    default:
      return state;
  }
}

export default reducer;
