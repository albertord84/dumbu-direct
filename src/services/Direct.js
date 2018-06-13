import axios from "axios";
import { setDirectListAction, setSelectedDirectAction } from '../actions/direct';
import { modifyingDirectAction, removeDirectAction } from '../actions/direct';
import { getDirectList, getSelectedDirectId, getSelectedDirectMessage } from "../selectors";
import store from "../store";

export class Direct {

  static directListSuccess(res) {
    setTimeout(() => {
      store.dispatch(setDirectListAction(res.data.directs));
      console.log(`fetched ${res.data.directs.length} direct messages...`);
    }, 1000);
  }

  static directListError(reason) {
    setTimeout(() => {
      console.error(`unable to get directs list...`);
    }, 1000);
  }

  static fetchList() {
    axios.get('../index.php/direct/list')
    .then(Direct.directListSuccess)
    .catch(Direct.directListError);
  }

  static directDeleteSuccess(id) {
    setTimeout(() => {
      console.log(`deleted the direct message ${id}...`);
    }, 1000);
  }

  static directDeleteError(reason) {
    setTimeout(() => {
      console.error(`unable to delete direct message...`);
    }, 1000);
  }

  static deleteDirect(id) {
    axios.get('../index.php/direct/delete/' + id)
    .then(Direct.directDeleteSuccess)
    .catch(Direct.directDeleteError);
  }

  static isModifyingDirectMessage(modifying = true) {
    store.dispatch(modifyingDirectAction(modifying));
  }

  static selectDirectMessage(id) {
    const predicate = (o) => {
      return o.id === id;
    }
    const selected = _.find(getDirectList(), predicate);
    store.dispatch(setSelectedDirectAction(selected));
  }

  static removeDirectSuccess() {
    const jq = jQuery;
    setTimeout(() => {
      Direct.isModifyingDirectMessage(false);
      const direct = getSelectedDirectMessage();
      store.dispatch(removeDirectAction(direct));
      console.log(`message ${getSelectedDirectId()} successfully deleted...`);
      jq('#remove-direct-modal').modal('hide');
    }, 1000);
  }

  static removeDirectError() {
    const id = getSelectedDirectId();
    setTimeout(() => {
      Direct.isModifyingDirectMessage(false);
      console.error(`error deleting the direct message ${id}...`);
    }, 1000);
  }

  static removeDirectFromDb(id) {
    axios.get(`../index.php/direct/delete/${id}`)
    .then(Direct.removeDirectSuccess)
    .catch(Direct.removeDirectError);
  }

}
