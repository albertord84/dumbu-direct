import React, { Component } from 'react';

import { getIsAdmin, isLogged } from "../selectors";

import DirectList from "../components/DirectList";
import { Direct } from "../services/Direct";
import { User } from "../services/User";

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  componentDidMount() {
    if (isLogged() === false) {
      User.mustLogIn();
      return;
    }
    Direct.fetchList();
  }

  render() {
    return (
      <div className="mt-5 row justify-content-center">
        { getIsAdmin() ? <DirectList /> : '' }
      </div>
    )
  }

}
