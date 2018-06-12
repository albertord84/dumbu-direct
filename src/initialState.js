const initialState = {
  username: '',
  password: '',
  isLogged: true,
  logging: false,
  searching: false,
  loginError: '',
  isAdmin: true,
  session: {
    cookies: []
  },
  direct: {
    modifying: false,
    follower: null,
    text: '',
    selected: null,
    list: []
  }
}

export default initialState;
