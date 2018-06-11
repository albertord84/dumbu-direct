import axios from "axios";

import { getPassword, getUserName } from "../selectors";

import { notLoggingAction, isLoggedAction } from '../actions/user';
import { setUserIsAdminAction } from '../actions/user';
import { loginErrorAction } from '../actions/user';


export class User {

  static mustLogIn() {
    console.error('security issue: you must login first...');
    store.dispatch(loginErrorAction('You must login first...'));
    window.location.href = '#/login';
  }

  static loginSuccess(res) {
    setTimeout(() => {
      store.dispatch(notLoggingAction());
      if (res.data.success) {
        store.dispatch(isLoggedAction());
        store.dispatch(setUserIsAdminAction(res.data.isAdmin));
        console.log(`redirecting the user ${getUserName()}...`);
        window.location.href = '#/home';
      }
      else {
        console.log(res.data);
        store.dispatch(loginErrorAction('Something went wrong... You are not logged in.'));
      }
    }, 1000);
  }

  static loginError(reason) {
    setTimeout(() => {
      store.dispatch(notLoggingAction());
      store.dispatch(loginErrorAction(reason.response.data.error));
    }, 1000);
  }

  static auth() {
    axios.post('../index.php/user/auth', {
      username: getUserName(),
      password: getPassword()
    })
    .then(User.loginSuccess)
    .catch(User.loginError);
  }

}
