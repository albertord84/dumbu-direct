export const setDirectListAction = (list) => {
  return { type: 'SET_DIRECT_LIST', payload: list };
}

export const removeDirectAction = (direct) => {
  return { type: 'REMOVE_DIRECT_FROM_LIST', payload: direct };
}

export const addDirectAction = (direct) => {
  return { type: 'ADD_DIRECT_TO_LIST', payload: direct };
}
