if (typeof angular !== 'undefined'){
    angular.module('dumbu', [
        'ngResource', 'ngCookies', 'ngSanitize'
    ]);
}

var Dumbu = {

    blockUI: function (msg) {
        var src = $('img.hidden.loading').attr('src');
        var tmpl = _.template('<img width="100" height="100" src="<%= src %>">'+
                '<h4><%= text %></h4>');
        $.blockUI({
            message: tmpl({
                src: src,
                text: typeof msg === 'undefined' ? '' : msg
            }),
            css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: 'none'
            }
        });
    },

    unblockUI: function () {
        $.unblockUI();
    },

	hideModal: function (selector) {
		jQuery(selector).modal('hide');
	},

	showModal: function (selector) {
		jQuery(selector).modal('show');
	}

};
