import React, { Component } from 'react';

import { isModifyingDirectMessage } from "../selectors";

const RemoveDirectDialog = (props) => {
  const text = `Are you sure about deleting this message...`;
  return (
    <div className="modal fade" tabIndex="-1" id="remove-direct-modal">
      <div className="modal-dialog" role="document">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title">Remove message</h5>
            <button type="button" className="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div className="modal-body">{text}</div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary"
                    data-dismiss="modal"
                    disabled={isModifyingDirectMessage()}>Cancel</button>
            <button type="button" className="btn btn-danger"
                    disabled={isModifyingDirectMessage()}
                    onClick={props.removeHandler}>Delete It!</button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default RemoveDirectDialog;
