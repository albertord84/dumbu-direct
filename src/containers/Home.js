import React, { Component } from 'react';

import { getIsAdmin, isLogged, getSelectedDirectId } from "../selectors";
import { hasDirectMessages } from "../selectors";

import DirectList from "../containers/DirectList";
import EditDirectDialog from "../components/EditDirectDialog";
import RemoveDirectDialog from "../components/RemoveDirectDialog";

import { Direct } from "../services/Direct";
import { User } from "../services/User";
import DirectToolbar from '../components/DirectToolbar';

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  editDirectDialog(id) {
    console.log(`editing the direct message ${id}`);
    Direct.select(id);
  }

  removeDirectDialog(id) {
    console.log(`removing the direct message ${id}`);
    Direct.select(id);
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
    Direct.isModifying();
    Direct.remove(getSelectedDirectId());
  }

  render() {
    return (
      <div className="home">
        <div className="mt-5 row justify-content-center">
          {
            getIsAdmin() ? 
            <div>
              <DirectToolbar />
              {
                hasDirectMessages() ?
                  <div>
                    <DirectList editDialog={this.editDirectDialog}
                                removeDialog={this.removeDirectDialog} />
                    <EditDirectDialog updateTextHandler={this.updateDirectTextHandler} />
                    <RemoveDirectDialog removeHandler={this.removeDirectMessage} />
                  </div>
                : ''
              }
            </div> : ''
          }
        </div>
      </div>
    )
  }

}