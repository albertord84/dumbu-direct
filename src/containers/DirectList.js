import React, { Component } from 'react';

import { getDirectList } from "../selectors";
import { Utils } from "../services/Utils";

export default class DirectList extends Component {

  constructor(props) {
    super(props);
  }

  showControls(ev) {
    let node = ev.target;
    if (Utils.hasClass(node, 'direct') === false) {
      node = Utils.parent(node, 'direct');
    }
    let bts = _.toArray(node.getElementsByClassName('btn-group'))[0];
    bts.style.display = 'inherit';
  }

  hideControls(ev) {
    let node = ev.target;
    if (Utils.hasClass(node, 'direct') === false) {
      node = Utils.parent(node, 'direct');
    }
    let bts = _.toArray(node.getElementsByClassName('btn-group'))[0];
    bts.style.display = 'none';
  }

  render() {
    const props = this.props;
    const list = getDirectList();
    return (
      <div className="direct-list row mb-3">
        {
          list.map(direct =>
            <div className="direct bg-light w-100 mt-3 card"
                onMouseEnter={this.showControls} onMouseLeave={this.hideControls}
                key={direct.id}>
              <div className="card-header">
                <div className="card-title">{Utils.shortText(direct.text)}</div>
              </div>
              <div className="card-body small text-muted text-right">
                <div className="btn-group float-left">
                  <button type="button" className="btn btn-sm btn-success"
                          data-toggle="modal" data-target="#edit-direct-modal"
                          onClick={ () => { props.editDialog(direct.id) } }>
                    <i className="fa fa-pencil" />
                  </button>
                  <button type="button" className="btn btn-sm btn-danger"
                          data-toggle="modal" data-target="#remove-direct-modal"
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
}
