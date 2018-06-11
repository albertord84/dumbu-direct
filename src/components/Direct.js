import React, { Component } from 'react';
import { Utils } from "../services/Utils";

const DirectService = require('../services/Direct').Direct;
import * as _ from 'lodash';

const showControls = (ev) => {
  ev.stopPropagation();
  const el = ev.target;
  let btnGroup = null;
  if (el.className.toString().match(/direct/)!==null ||
      el.className.toString().match(/card-body/)!==null)
  {
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
  if ('undefined' !== typeof visibleBtnGroup) {
    const cl = visibleBtnGroup.className;
    visibleBtnGroup.className = cl + ' invisible';
  }
}

const editMessage = (id) => {
  console.log(`editing the direct message ${id}`);
}

const deleteMessage = (id) => {
  console.log(`editing the direct message ${id}`);
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
          <button type="button" className="btn btn-sm btn-success"
                  onClick={() => editMessage(props.directId)}>
            <i className="fa fa-pencil" />
          </button>
          <button type="button" className="btn btn-sm btn-danger"
                  onClick={() => deleteMessage(props.directId)}>
            <i className="fa fa-trash" />
          </button>
        </div>
        {Utils.fromTs(props.sentAt)}
      </div>
    </div>
  )
}

export default Direct;
