import moment from 'moment/src/moment';

export class Utils {

  static shortText = (text, defLength = 100) => {
    const t = text.substr(0, defLength);
    if (t.match(/\.{1}$/)!==null || t.match(/\.{3}$/)!==null) {
      return t;
    }
    else if (t.match(/\.{2}$/)!==null) {
      return t + '.';
    }
    else if (t.match(/[^\.]$/)!==null && text.length > defLength) {
      return t + '...';
    }
    else return text;
  }

  static fromNow(timestamp) {
    return moment(timestamp * 1000).fromNow();
  }

  static hasClass(el, cl) {
    return el.getAttribute('class').toString().split(' ').includes(cl);
  }

  static parent(fromEl, parentClassName) {
    if (Utils.hasClass(fromEl.parentElement, parentClassName)) {
      return fromEl.parentElement
    }
    return Utils.parent(fromEl.parentElement, parentClassName)
  }
}
