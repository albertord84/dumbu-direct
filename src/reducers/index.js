import initialState from "../initialState";

const reducer = (state = initialState, action) => {
  switch (action.type) {
    case 'SET_USERNAME':
      return Object.assign(state, { username: action.payload });
  
    case 'SET_PASSWORD':
      return Object.assign(state, { password: action.payload });

    case 'SET_IS_LOGGING':
      return Object.assign(state, { logging: action.payload });

    case 'SET_LOGIN_ERROR':
      return Object.assign(state, { loginError: action.payload });
  
    case 'SET_IS_LOGGED':
      return Object.assign(state, { isLogged: action.payload });
  
    case 'SET_SESSION_COOKIES':
      const cookies = [].concat(action.payload);
      return Object.assign(state, { session: { cookies: cookies } });
  
    default:
      return state;
  }
}

export default reducer;
