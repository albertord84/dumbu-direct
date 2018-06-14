import axios from "axios";
import { setDirectListAction, setSelectedDirectAction } from '../actions/direct';
import { modifyingDirectAction, removeDirectAction } from '../actions/direct';
import { getDirectList, getSelectedDirectId, getSelectedDirectMessage } from "../selectors";
import store from "../store";

export class Direct {

  static listSuccess(res) {
    setTimeout(() => {
      store.dispatch(setDirectListAction(res.data.directs));
      console.log(`fetched ${res.data.directs.length} direct messages...`);
    }, 1000);
  }

  static listError(reason) {
    setTimeout(() => {
      console.error(`unable to get directs list...`);
    }, 1000);
  }

  static fetchList() {
    axios.get('../index.php/direct/list')
    .then(Direct.listSuccess)
    .catch(Direct.listError);
  }

  static isModifying(modifying = true) {
    store.dispatch(modifyingDirectAction(modifying));
  }

  static select(id) {
    const predicate = (o) => {
      return o.id === id;
    }
    const selected = _.find(getDirectList(), predicate);
    store.dispatch(setSelectedDirectAction(selected));
  }

  static removeSuccess() {
    const jq = jQuery;
    setTimeout(() => {
      Direct.isModifying(false);
      const direct = getSelectedDirectMessage();
      store.dispatch(removeDirectAction(direct));
      console.log(`message ${getSelectedDirectId()} successfully deleted...`);
      jq('#remove-direct-modal').modal('hide');
    }, 1000);
  }

  static removeError() {
    const id = getSelectedDirectId();
    setTimeout(() => {
      Direct.isModifying(false);
      console.error(`error deleting the direct message ${id}...`);
    }, 1000);
  }

  static remove(id) {
    axios.get(`../index.php/direct/delete/${id}`)
    .then(Direct.removeSuccess)
    .catch(Direct.removeError);
  }

}
