const initialState = {
  username: '',
  password: '',
  isLogged: false,
  logging: false,
  loginError: '',
  isAdmin: false,
  session: {
    cookies: []
  },
  direct: {
    follower: null,
    text: '',
    list: []
  }
}

export default initialState;
