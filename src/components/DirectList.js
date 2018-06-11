import React, { Component } from 'react';
import Direct from './Direct';

import { getDirectList } from "../selectors";

const DirectList = (props) => {
  const list = () => {
    const list = getDirectList();
    return list.map(i => {
      return <Direct key={i.id} text={i.msg_text}
                     sentAt={i.sent_at} />
    });
  }
  return (
    <div className="direct-list row mb-3">{list()}</div>
  )
}

export default DirectList;
