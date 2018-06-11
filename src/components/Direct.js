import React, { Component } from 'react';
import { Utils } from "../services/Utils";

import * as _ from 'lodash';

const showControls = (ev) => {
  ev.stopPropagation();
  const el = ev.target;
  let btnGroup = null;
  if (el.className.toString().match(/direct/)!==null || el.className.toString().match(/card-body/)!==null) {
    btnGroup = _.toArray(el.getElementsByClassName('btn-group'))[0];
  }
  if (el.className.toString().match(/card-header/)!==null) {
    btnGroup = _.toArray(el.parentNode.getElementsByClassName('btn-group'))[0];
  }
  if (btnGroup !== null) {
    const cl = btnGroup.className.toString().replace('invisible', '');
    btnGroup.className = cl;
  }
}

const hideControls = (ev) => {
  const el = ev.target;
  const predicate = (btnGroup) => {
    return btnGroup.className.toString().indexOf('invisible') === -1;
  }
  const btnGroups = _.toArray(document.getElementsByClassName('direct-actions'));
  const visibleBtnGroup = _.find(btnGroups, predicate);
  const cl = visibleBtnGroup.className;
  visibleBtnGroup.className = cl + ' invisible';
}

const Direct = (props) => {
  return (
    <div className="direct bg-light w-100 mt-3 card"
         onMouseEnter={showControls} onMouseLeave={hideControls}>
      <div className="card-header">
        <div className="card-title">{Utils.shortText(props.text)}</div>
      </div>
      <div className="card-body small text-muted text-right">
        <div className="btn-group invisible float-left direct-actions">
          <button type="button" className="btn btn-sm btn-success">
            <i className="fa fa-pencil" />
          </button>
          <button type="button" className="btn btn-sm btn-danger">
            <i className="fa fa-trash" />
          </button>
        </div>
        {Utils.fromTs(props.sentAt)}
      </div>
    </div>
  )
}

export default Direct;
