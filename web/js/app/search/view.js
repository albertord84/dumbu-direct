jQuery(function () {
	var profileTmpl = _.template('' +
		'<div class="panel panel-default">' +
		'  <div class="panel-heading text-center">' +
		'    <button type="button" class="close remove-profile"' +
		'      aria-label="Close"><span aria-hidden="true">&times;</span>' +
		'    </button>' +
		'    <img class="card-img-top" alt="Profile photo"' +
		'      src="<%= profile_pic_url %>">' +
		'  </div>' +
		'  <div class="panel-body text-center">' +
		'    <h4 class=""><%= username %></h4>' +
		'    <div class="text-muted"><%= full_name %></div>' +
		'  </div>' +
		'  <div class="panel-footer text-center text-muted small">' +
		'    <%= byline %>' +
		'  </div>' +
		'</div>');
	store.subscribe(function () {
		var state = store.getState().search;
		if (state.results.length === 0) {
			jQuery('div.selected-profs').empty();
			jQuery('button.text-them').attr('disabled', true);
		} else {
			jQuery('div.selected-profs').empty();
			var container = jQuery('div.selected-profs');
			_.forEach(state.results, function(o, i, a) {
				var compiledHtml = profileTmpl({
					profile_pic_url: o.profile_pic_url,
					full_name: _.trim(o.full_name),
					username: _.trim(o.username),
					byline: o.byline
				});
				container.append(compiledHtml);
			});
			jQuery('button.text-them').attr('disabled', false);
		}
	});
});
