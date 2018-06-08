import React from 'react';
import ReactDOM from 'react-dom';
import App from './containers/App';

import './index.css';
import store from "./store";

const render = () => {
  ReactDOM.render(<App />, document.getElementById('app'));
}
render();

store.subscribe(render);
