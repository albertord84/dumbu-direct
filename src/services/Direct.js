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

}
