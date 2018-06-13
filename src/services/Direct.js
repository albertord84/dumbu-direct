import axios from "axios";
import { setDirectListAction, setSelectedDirectAction } from '../actions/direct';
import { modifyingDirectAction } from '../actions/direct';
import { getDirectList } from "../selectors";
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

  static selectDirectMessage(id) {
    const predicate = (o) => {
      return o.id === id;
    }
    const selected = _.find(getDirectList(), predicate);
    store.dispatch(setSelectedDirectAction(selected));
  }

}
