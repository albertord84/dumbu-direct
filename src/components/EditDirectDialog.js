import React, { Component } from 'react';
import { getSelectedDirectMessage } from "../selectors";

const jq = jQuery;
const dlg = '#edit-direct-modal';

const shownHandler = (ev) => {
  const textArea = jq(dlg).find('textarea');
  textArea.val(getSelectedDirectMessage().text);
  textArea.focus();
}

const EditDirectDialog = (props) => {
  jq(dlg).off('shown.bs.modal').on('shown.bs.modal', shownHandler);
  return (
    <div className="modal fade" tabIndex="-1" id="edit-direct-modal">
      <div className="modal-dialog" role="document">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title">Edit message</h5>
            <button type="button" className="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div className="modal-body">
            <textarea className="form-control" rows="5"></textarea>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" className="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default EditDirectDialog;
