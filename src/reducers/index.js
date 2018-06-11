import initialState from "../initialState";

const reducer = (state = initialState, action) => {
  switch (action.type) {
    case 'SET_USERNAME':
      return Object.assign(state, { username: action.payload });
  
    case 'SET_PASSWORD':
      return Object.assign(state, { password: action.payload });

    case 'SET_IS_LOGGING':
      return Object.assign(state, { logging: action.payload });

    case 'SET_IS_SEARCHING':
      return Object.assign(state, { searching: action.payload });

    case 'SET_LOGIN_ERROR':
      return Object.assign(state, { loginError: action.payload });
  
    case 'SET_IS_LOGGED':
      return Object.assign(state, { isLogged: action.payload });
  
    case 'SET_SESSION_COOKIES': {
      const cookies = [].concat(action.payload);
      const session = state.session;
      return Object.assign(state, {
        session: Object.assign(session, { cookies: cookies })
      });
    }
  
    case 'SET_USER_IS_ADMIN':
      return Object.assign(state, { isAdmin: action.payload });
  
    case 'SET_DIRECT_LIST': {
      const direct = state.direct;
      return Object.assign(state, {
        direct: Object.assign(direct, { list: action.payload })
      });
    }
  
    case 'REMOVE_DIRECT_FROM_LIST': {
      const direct = state.direct;
      const list = direct.list.filter(d => d.id !== action.payload.id);
      return Object.assign(state, {
        direct: Object.assign(direct, { list: list })
      });
    }
  
    case 'ADD_DIRECT_TO_LIST': {
      const direct = state.direct;
      return Object.assign(state, {
        direct: Object.assign(direct, {
          list: direct.list.concat(action.payload)
        })
      });
    }

    case 'MODIFYING_DIRECT': {

    }

    case 'MODIFY_DIRECT_TEXT': {
      
    }
  
    default:
      return state;
  }
}

export default reducer;
