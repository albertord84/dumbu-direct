import React, { Component } from 'react';

import { getIsAdmin, isLogged } from "../selectors";
import { loginErrorAction } from '../actions/user';

import store from "../store";
import { Direct } from "../services/Direct";

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  directListSuccess(res) {

  }

  directListError(reason) {

  }

  componentDidMount() {
    Direct.list()
  }

  render() {
    if (isLogged() === false) {
      window.location.href = '#/login';
      console.error('security issue: must login first');
      store.dispatch(loginErrorAction('You must login first...'));
    }
    return (
      <div className="mt-5 row justify-content-center">
        { getIsAdmin() ? <DirectList /> : '' }
      </div>
    )
  }

}
