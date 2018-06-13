import React, { Component } from 'react';

import { getIsAdmin, isLogged, getSelectedDirectId } from "../selectors";

import DirectList from "../containers/DirectList";
import EditDirectDialog from "../components/EditDirectDialog";
import RemoveDirectDialog from "../components/RemoveDirectDialog";

import { Direct } from "../services/Direct";
import { User } from "../services/User";

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  editDirectDialog(id) {
    console.log(`editing the direct message ${id}`);
    Direct.selectDirectMessage(id);
    
  }

  removeDirectDialog(id) {
    console.log(`removing the direct message ${id}`);
    Direct.selectDirectMessage(id);
  }

  componentDidMount() {
    if (isLogged() === false) {
      User.mustLogIn();
      return;
    }
    Direct.fetchList();
  }

  updateDirectTextHandler() {

  }

  removeDirectMessage(ev) {
    Direct.isModifyingDirectMessage();
    Direct.removeDirectFromDb(getSelectedDirectId());
  }

  render() {
    return (
      <div>
        <div className="mt-5 row justify-content-center">
          {
            getIsAdmin() ? 
            <div>
              <DirectList editDialog={this.editDirectDialog}
                          removeDialog={this.removeDirectDialog} />
              <EditDirectDialog updateTextHandler={this.updateDirectTextHandler} />
              <RemoveDirectDialog removeHandler={this.removeDirectMessage} />
            </div> : ''
          }
        </div>
      </div>
    )
  }

}
