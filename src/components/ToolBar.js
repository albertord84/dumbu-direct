import React, { Component } from 'react';

import { getUserName, isLogged } from "../selectors";

const ToolBar = (props) => {
  return (
    <nav id="navbar" className="navbar navbar-light bg-light">
      <a className="navbar-brand text-muted" href="#">
        {isLogged() ? getUserName() + '@DUMBU' : ''}
      </a>
      <ul className="nav nav-pills">
        <li className="nav-item">
          <a className="nav-link" href="#direct">@direct</a>
        </li>
        <li className="nav-item">
          <a className="nav-link" href="#location">@location</a>
        </li>
        <li className="nav-item dropdown tools">
          <a className="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Tools</a>
          <div className="dropdown-menu">
            <a className="dropdown-item" href="#one">Stats</a>
            <a className="dropdown-item" href="#two">Manage</a>
            <div role="separator" className="dropdown-divider"></div>
            <a className="dropdown-item" href="#three">Account</a>
          </div>
        </li>
      </ul>
    </nav>
  )
}

export default ToolBar;
