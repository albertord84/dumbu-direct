import axios from "axios";

export class User {

  static auth(user, pass, successFn, errorFn) {
    axios.post('../index.php/user/auth', {
      username: user,
      password: pass
    })
    .then(successFn)
    .catch(errorFn);
  }

}
