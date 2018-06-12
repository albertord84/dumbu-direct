import React, { Component } from 'react';

import { getDirectList } from "../selectors";
import { Utils } from "../services/Utils";

export default class DirectList extends Component {

  constructor(props) {
    super(props);
  }

  showControls(ev) {
    const target = jQuery(ev.target);
    if (target.hasClass('direct') || target.hasClass('card-body')) {
      target.find('.btn-group').fadeIn(200);
    }
    else if (target.hasClass('card-header') || target.hasClass('card-title')) {
      const parent = target.parents('.direct');
      parent.find('.btn-group').fadeIn(200);
    }
  }

  hideControls(ev) {
    jQuery('.btn-group').fadeOut(100);
  }

  render() {
    const props = this.props;
    const self = this;
    const list = getDirectList();
    return (
      <div className="direct-list row mb-3">
        {
          list.map(direct =>
            <div className="direct bg-light w-100 mt-3 card"
                onMouseEnter={self.showControls}
                onMouseLeave={self.hideControls}
                key={direct.id}>
              <div className="card-header">
                <div className="card-title">{Utils.shortText(direct.msg_text)}</div>
              </div>
              <div className="card-body small text-muted text-right">
                <div className="btn-group float-left">
                  <button type="button" className="btn btn-sm btn-success"
                          onClick={() => props.showEditDialog(direct.id)}>
                    <i className="fa fa-pencil" />
                  </button>
                  <button type="button" className="btn btn-sm btn-danger"
                          onClick={() => props.showRemoveDialog(direct.id)}>
                    <i className="fa fa-trash" />
                  </button>
                </div>
                { Utils.fromTs(direct.sent_at) }
              </div>
            </div>
          )
        }
      </div>
    )
  }

}
