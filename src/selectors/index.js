import store from "../store";

export const getUserName = () => store.getState().username;
export const getPassword = () => store.getState().password;
export const isLogged = () => store.getState().isLogged;
export const isLogging = () => store.getState().logging;
export const getLoginError = () => store.getState().loginError;
