import React, { Component } from 'react';
import * as _ from 'lodash';

const Direct = (props) => {
  const shortText = (text) => {
    const t = text.substr(0, 100);
    if (t.match(/\.{1}$/)!==null || t.match(/\.{3}$/)!==null) {
      return t;
    }
    else if (t.match(/\.{2}$/)!==null) {
      return t + '.';
    }
    else if (t.match(/[^\.]$/)!==null && text.length > 100) {
      return t + '...';
    }
    else return text;
  }
  return (
    <div className="direct bg-light w-100 mt-3 card">
      <div className="card-header">
        <div className="card-title">{shortText(props.text)}</div>
      </div>
    </div>
  )
}

export default Direct;
