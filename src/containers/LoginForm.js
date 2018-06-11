import React, { Component } from 'react';
import { User } from "../services/User";

import * as _ from 'lodash';

import { isLogging, getLoginError } from "../selectors";
import { getPassword, getUserName } from "../selectors";

import { notLoggingAction, isLoggingAction, isLoggedAction } from '../actions/user';
import { setUserNameAction, setPasswordAction } from '../actions/user';
import { loginErrorAction } from '../actions/user';

import store from "../store";

export default class LoginForm extends Component {

  constructor(props) {
    super(props);
    this.onSubmit = this.onSubmit.bind(this);
    this.inputChange = this.inputChange.bind(this);
  }

  inputChange(ev) {
    const el = ev.target;
    const value = _.trim(el.value);
    store.dispatch(loginErrorAction(''));
    if (el.type === 'text') {
      store.dispatch(setUserNameAction(value));
    }
    if (el.type === 'password') {
      store.dispatch(setPasswordAction(value));
    }
  }

  onSubmit(ev) {
    ev.preventDefault();
    store.dispatch(isLoggingAction());
    if (getUserName().trim() === '' || getPassword().trim() === '') {
      store.dispatch(loginErrorAction('User name and/or password must not be empty'));
      setTimeout(() => {
        store.dispatch(notLoggingAction());
      }, 1000);
      return;
    }
    User.auth();
  }

  render() {
    return (
      <div className="mt-5 row justify-content-center">
        <div className="form-login">
          <form onSubmit={this.onSubmit} onInput={this.inputChange}>
            <div className="form-group">
              <input type="text" className="form-control username"
                placeholder="Instagram username" disabled={isLogging()} />
            </div>
            <div className="form-group">
              <input type="password" className="form-control password"
                placeholder="Password..." disabled={isLogging()} />
            </div>
            <div className="form-group d-flex justify-content-center submit">
              <button type="submit" className="btn btn-primary" disabled={isLogging()}>
                {isLogging() ? 'Logging...' : 'Log In'}
              </button>
            </div>
            {
              getLoginError() === '' ? '' :
              <div className="mt-3 alert alert-danger small">{getLoginError()}</div>
            }
          </form>
        </div>
      </div>
    )
  }

}
