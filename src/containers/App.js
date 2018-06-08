import React, { Component } from 'react';
import { HashRouter, Route, Link, Redirect } from 'react-router-dom';
import { browserHistory } from 'react-router';

import ToolBar from '../components/ToolBar';
import LoginForm from '../containers/LoginForm';
import Home from '../containers/Home';

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

}

export default App;
