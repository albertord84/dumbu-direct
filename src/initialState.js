const initialState = {
  username: '',
  password: '',
  isLogged: false,
  logging: false,
  loginError: '',
  session: {
    cookies: []
  },
  direct: {
    follower: null,
    text: ''
  }
}

export default initialState;
