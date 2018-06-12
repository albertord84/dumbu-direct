export const setDirectListAction = (list) => {
  return { type: 'SET_DIRECT_LIST', payload: list };
}

export const setSelectedDirectAction = (id) => {
  return { type: 'SET_SELECTED_DIRECT', payload: id };
}

export const removeDirectAction = (direct) => {
  return { type: 'REMOVE_DIRECT_FROM_LIST', payload: direct };
}

export const addDirectAction = (direct) => {
  return { type: 'ADD_DIRECT_TO_LIST', payload: direct };
}

export const modifyingDirectAction = (modifying = true) => {
  return { type: 'MODIFYING_DIRECT', payload: modifying };
}

export const modifyDirectTextAction = (text) => {
  return { type: 'SET_DIRECT_TEXT', payload: text };
}
