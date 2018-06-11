import axios from "axios";
import { setDirectListAction } from '../actions/direct';

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

}
