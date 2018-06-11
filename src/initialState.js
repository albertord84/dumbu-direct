const initialState = {
  username: '',
  password: '',
  isLogged: false,
  logging: false,
  searching: false,
  loginError: '',
  isAdmin: false,
  session: {
    cookies: []
  },
  direct: {
    modifying: false,
    follower: null,
    text: '',
    list: []
  }
}

export default initialState;
