export const isLoggedAction = () => {
  return { type: 'SET_IS_LOGGED', payload: true };
}

export const notLoggedAction = () => {
  return { type: 'SET_IS_LOGGED', payload: false };
}

export const isLogginAction = () => {
  return { type: 'SET_IS_LOGGING', payload: true };
}

export const notLogginAction = () => {
  return { type: 'SET_IS_LOGGING', payload: false };
}

export const loginErrorAction = (error) => {
  return { type: 'SET_LOGIN_ERROR', payload: error };
}

export const setUserNameAction = (username) => {
  return { type: 'SET_USERNAME', payload: username };
}

export const setPasswordAction = (password) => {
  return { type: 'SET_PASSWORD', payload: password };
}
