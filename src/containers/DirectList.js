import React, { Component } from 'react';

import { getDirectList } from "../selectors";
import { Utils } from "../services/Utils";

const bts = '.direct-actions';
const jq = jQuery;

const hideControls = (ev) => {
  const target = ev.target;
  if (jq(target).hasClass('direct')) {
    jq(target).find(bts).fadeOut(100);
    return;
  }
  jq(target).parents('.direct').find(bts).fadeOut(100);
}

const showControls = (ev) => {
  const target = ev.target;
  const jq = jQuery;
  if (jq(target).hasClass('direct')) {
    jq(target).find(bts).fadeIn(200);
    return;
  }
  jq(target).parents('.direct').find(bts).fadeIn(200);
}

const DirectList = (props) => {
  const list = getDirectList();
  return (
    <div className="direct-list row mb-3">
      {
        list.map(direct =>
          <div className="direct bg-light w-100 mt-3 card"
              onMouseEnter={showControls} onMouseLeave={hideControls}
              key={direct.id}>
            <div className="card-header">
              <div className="card-title">{Utils.shortText(direct.text)}</div>
            </div>
            <div className="card-body small text-muted text-right">
              <div className="btn-group float-left direct-actions">
                <button type="button" className="btn btn-sm btn-success"
                        data-toggle="modal" data-target="#edit-direct-modal"
                        onClick={ () => { props.editDialog(direct.id) } }>
                  <i className="fa fa-pencil" />
                </button>
                <button type="button" className="btn btn-sm btn-danger"
                        onClick={ () => { props.removeDialog(direct.id) } }>
                  <i className="fa fa-trash" />
                </button>
              </div>
              { Utils.fromNow(direct.sentAt) }
            </div>
          </div>
        )
      }
    </div>
  )
}

export default DirectList;
