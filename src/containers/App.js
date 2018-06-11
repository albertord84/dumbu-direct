import React, { Component } from 'react';
import { HashRouter, Route, Link, Redirect } from 'react-router-dom';
import { browserHistory } from 'react-router';

import ToolBar from '../components/ToolBar';
import LoginForm from '../containers/LoginForm';
import Home from '../containers/Home';

import { isLogged, getDirectList, isLogging } from "../selectors";
import { getLoginError, getUserName } from "../selectors";

class App extends Component {

  constructor(props) {
    super(props);
  }

  render() {
    return (
      <HashRouter>
        <div>
          <ToolBar />
          <Route exact path='/' component={LoginForm} />
          <Route path='/login' component={LoginForm} />
          <Route path='/home' component={Home} />
        </div>
      </HashRouter>
    )
  }

  shouldComponentUpdate(nextProps, nextState) {
    if (isLogged() || isLogging() || getLoginError() !== '' ||
        getUserName() !== '')
    {
      return true;
    }
    if (getDirectList().length > 0) {
      return true;
    }
    return false;
  }

}

export default App;
