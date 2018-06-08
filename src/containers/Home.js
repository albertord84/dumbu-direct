import React, { Component } from 'react';

import { isLogging, getLoginError } from "../selectors";
import { getPassword, getUserName } from "../selectors";

import { notLogginAction, isLogginAction, isLoggedAction } from '../actions/user';
import { setUserNameAction, setPasswordAction } from '../actions/user';
import { loginErrorAction } from '../actions/user';

import store from "../store";

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="mt-5 row justify-content-center">
        
      </div>
    )
  }

}
