import React, { Component } from 'react';

import { getIsAdmin, isLogged, getDirectList } from "../selectors";
import { loginErrorAction } from '../actions/user';

import store from "../store";
import { setDirectListAction } from '../actions/direct';
import DirectList from "../components/DirectList";
import { Direct } from "../services/Direct";

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  directListSuccess(res) {
    setTimeout(() => {
      store.dispatch(setDirectListAction(res.data.directs));
      console.log(`fetched ${res.data.directs.length} direct messages...`);
    }, 1000);
  }

  directListError(reason) {
    setTimeout(() => {
      console.error(`unable to get directs list...`);
    }, 1000);
  }

  componentDidMount() {
    if (isLogged() === false) {
      console.error('security issue: you must login first...');
      store.dispatch(loginErrorAction('You must login first...'));
      window.location.href = '#/login';
      return;
    }
    Direct.list(this.directListSuccess, this.directListError);
  }

  render() {
    return (
      <div className="mt-5 row justify-content-center">
        { getIsAdmin() ? <DirectList /> : '' }
      </div>
    )
  }

}
