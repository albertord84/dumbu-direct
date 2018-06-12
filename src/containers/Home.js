import React, { Component } from 'react';

import { getIsAdmin, isLogged } from "../selectors";
import store from "../store";

import DirectList from "../containers/DirectList";
import EditDirectDialog from "../components/EditDirectDialog";

import { Direct } from "../services/Direct";
import { User } from "../services/User";

export default class Home extends Component {

  constructor(props) {
    super(props);
  }

  showEditDirectDialog(id) {
    console.log(`editing the direct message ${id}`);
  }

  showRemoveDirectDialog(id) {
    console.log(`removing the direct message ${id}`);
  }

  storeSubscription() {

  }

  componentDidMount() {
    if (isLogged() === false) {
      User.mustLogIn();
      return;
    }
    Direct.fetchList();
    store.subscribe(this.storeSubscription);
  }

  render() {
    return (
      <div>
        <div className="mt-5 row justify-content-center">
          {
            getIsAdmin() ? 
            <div>
              <DirectList showEditDialog={this.showEditDirectDialog}
                          showRemoveDialog={this.showRemoveDirectDialog} />
              <EditDirectDialog />
            </div> : ''
          }
        </div>
      </div>
    )
  }

}
