import React, { Component } from 'react';

import { getUserName, isLogged } from "../selectors";

const DirectToolbar = (props) => {
  return (
    <nav className="navbar navbar-light m-0 p-0 direct-toolbar">
      <ul className="nav nav-pills">
        <li className="nav-item">
          <a className="btn btn-outline-secondary" href="#add-direct"><small>Add direct...</small></a>
        </li>
      </ul>
    </nav>
  )
}

export default DirectToolbar;
