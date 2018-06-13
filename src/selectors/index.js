import store from "../store";

export const getUserName = () => store.getState().username;
export const getPassword = () => store.getState().password;
export const isLogged = () => store.getState().isLogged;
export const isLogging = () => store.getState().logging;
export const getLoginError = () => store.getState().loginError;
export const getIsAdmin = () => store.getState().isAdmin;
export const getDirectList = () => store.getState().direct.list;
export const getSelectedDirectId = () => store.getState().direct.selected.id;
export const getSelectedDirectText = () => store.getState().direct.selected.text;
export const getSelectedDirectMessage = () => store.getState().direct.selected;
export const isModifyingDirectMessage = () => store.getState().direct.modifying;
