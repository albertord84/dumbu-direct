angular.module('dumbu', [
  'ngResource', 'ngCookies', 'ngSanitize'
]);

var Dumbu = {

  blockUI: function ()
  {
    var src = $('img.hidden.loading').attr('src');
    var tmpl = _.template('<img width="100" height="100" src="<%= src %>">');
    $.blockUI({
      message: tmpl({ src: src }),
      css: {
        backgroundColor: 'transparent',
        color: '#fff',
        border: 'none'
      }
    });
  },

  unblockUI: function ()
  {
    $.unblockUI();
  }

};
