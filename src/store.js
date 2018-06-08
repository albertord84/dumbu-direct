import reducer from "./reducers/index";
import { createStore } from "redux";

const store = createStore(reducer);
window.store = store;

export default store;
