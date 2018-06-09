import axios from "axios";

export class Direct {

  static list(successFn, errorFn) {
    axios.get('../index.php/direct/list')
    .then(successFn)
    .catch(errorFn);
  }

}
