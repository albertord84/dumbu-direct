<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DUMBU ::: Search</title>
        <meta charset='utf-8'>
        <meta content='IE=edge' http-equiv='X-UA-Compatible'>
        <meta content='width=device-width,initial-scale=1' name='viewport'>
        <link rel="icon" type="image/png" href="<?php echo base_url('img/icon.png'); ?>">
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap.min.css'); ?>'/>
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css'); ?>">
    </head>
    <body>
        <div id="root" class="container">
            <p class="text-muted text-center">Loading...</p>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/handlebars.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react.production.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/create-react-class.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react-dom.production.min.js'); ?>">
        </script>
        <script src="<?php echo base_url('js/lib/js.cookie.js'); ?>"></script>
        <script src="<?php echo base_url('js/app/dumbu.js'); ?>"></script>
        <script>
            var paths = {
                siteUrl: "<?php echo site_url(); ?>",
                baseUrl: "<?php echo base_url(); ?>"
            };
            var Dumbu = 'undefined' !== typeof Dumbu ?
                        Object.assign(Dumbu, paths) : Object.assign(paths);
        </script>
        <script>
            var initialState = {
                profiles: [],
                searchText: '',
                suggestions: [],
                hideSuggest: true,
                canText: false,
                searching: false,
                sending: false
            };
            var Header = createReactClass({
                render: function() {
                    return e('div', { id: "logo", className: "text-center" },
                        e('h1', null, 'DUMBU'),
                        e('p', null, 'Get more real followers')
                    );
                }
            });
            var SuggestBox = createReactClass({
                render: function() {
                    var self = this;
                    var suggestions = self.props.suggestions;
                    return e('div', { className: "tt-menu",
                        style: { width: '500px', marginTop: '-10px' } },
                        e('div', { className: "tt-dataset" },
                            _.slice(suggestions, 0, 15).map(function(suggestion){
                                return e('div', {
                                    className: 'tt-suggestion',
                                    onClick: function() {
                                        self.props.selectProfile(suggestion); 
                                    }
                                }, suggestion.username);
                            })
                        )
                    );
                }
            });
            var SearchBox = createReactClass({
                render: function() {
                    var self = this;
                    var searching = self.props.searching;
                    var sending = self.props.sending;
                    var profiles = self.props.profiles;
                    return e('div', { className: "form-group" },
                        searching ? e('img', { className: "async-loading",
                            src: Dumbu.baseUrl + 'img/loading-small.gif'}) : '',
                        e('div', { className: "input-group" },
                            e('input', { id: "ref-prof", className: "form-control typeahead",
                                type: "text", name: "search", autofocus: "on",
                                autocomplete: 'off', disabled: searching || sending,
                                placeholder: "Select reference profiles...",
                                onChange: self.props.searchTextChange,
                                onClick: self.props.showSuggestions }
                            ),
                            e('span', { className: "input-group-btn" },
                                e('button', { className: "btn btn-success text-them",
                                        type: "button",
                                        disabled: profiles.length === 0 || searching || sending,
                                        onClick: self.props.submitData },
                                    e('i', { className: "glyphicon glyphicon-pencil" }),
                                    'Text\' em'
                                )
                            )
                        )
                    )
                }
            });
            var Profile = createReactClass({
                render: function() {
                    var self = this;
                    var profile = self.props.profile;
                    return e('div', { className: "panel panel-default" },
                        e('div', { className: "panel-heading text-center" },
                            e('button', { type: "button",
                                className: "close remove-profile",
                                onClick: function(ev) {
                                    self.props.removeProfile(profile);
                                } },
                                e('span', null, 'X')
                            ),
                            e('img', { className: "card-img-top",
                                alt: "Profile photo",
                                src: profile.profile_pic_url })
                        ),
                        e('div', { className: "panel-body text-center" },
                            e('h4', null, profile.username),
                            e('div', { className: "text-muted" }, profile.full_name)
                        ),
                        e('div', { className: "panel-footer text-center text-muted small" },
                            profile.byline)
                    );
                }
            });
            var SearchFollowers = createReactClass({
                removeProfile: function(profile) {
                    this.setState({
                        profiles: _.filter(this.state.profiles, function(p) {
                            return p.pk !== profile.pk;
                        })
                    });
                },
                getInitialState: function() {
                    return initialState;
                },
                searchTextChange: function(e) {
                    this.setState({ searchText: e.target.value, hideSuggest: true });
                },
                selectProfile: function(profile) {
                    this.setState({ hideSuggest: true });
                    var profiles = this.state.profiles;
                    if (_.find(profiles, { pk: profile.pk })!==undefined){
                        return;
                    }
                    this.setState({ profiles: profiles.concat(profile) });
                },
                showSuggestions: function() {
                    if (this.state.suggestions.length > 0) {
                        this.setState({ hideSuggest: false });
                    }
                },
                hideSuggestions: function() {
                    this.setState({ hideSuggest: true });
                },
                submitData: function() {
                    var self = this;
                    var profiles = self.state.profiles;
                    self.setState({ sending: true });
                    var data = {
                        profiles: profiles.map(function(p){
                            return p.pk;
                        }).join(','),
                        usernames: profiles.map(function(p){
                            return p.username;
                        }).join(',')
                    };
                    var form = document.createElement('form');
                    _.forEach(data, function(value, key){
                        var input = document.createElement('input');
                        $(input).attr({ type: 'hidden', name: key, value: value });
                        $(form).append(input);
                    });
                    $(form).attr({
                        'method': 'POST',
                        'action': Dumbu.siteUrl + '/compose/message'
                    });
                    $(document.body).append(form);
                    setTimeout(function(){
                        $(form).submit();
                        self.setState({ sending: false });
                    }, 500);
                },
                render: function() {
                    var self = this;
                    var suggestions = self.state.suggestions;
                    var hideSuggest = self.state.hideSuggest;
                    var searching = self.state.searching;
                    var sending = self.state.sending;
                    var profiles = self.state.profiles;
                    var error = self.state.error;
                    return e('div', null,
                        e(Header),
                        e('form', { id: 'search-form' },
                            e(SearchBox, {
                                sending: sending,
                                searching: searching,
                                profiles: profiles,
                                searchTextChange: self.searchTextChange,
                                showSuggestions: self.showSuggestions,
                                submitData: self.submitData
                            }),
                            error !== '' ? e('div', { className: 'form-group' },
                                e('div', { className: 'alert alert-danger' },
                                    error
                                )
                            ) : '',
                            hideSuggest ? '' : e(SuggestBox, {
                                suggestions: suggestions,
                                selectProfile: self.selectProfile,
                                hide: self.hideSuggestions
                            })
                        ),
                        profiles.length === 0 ? '' : e('div', {
                            className: 'row selected-profs' },
                            profiles.map(function(profile) {
                                return e(Profile, {
                                    profile: profile,
                                    removeProfile: self.removeProfile
                                });
                            })
                        )
                    );
                },
                loadSuggestions: function() {
                    var self = this;
                    var url = Dumbu.siteUrl + '/search/followers/' +
                              self.state.searchText;
                    if (_.trim(self.state.searchText)==='') return;
                    self.setState({ searching: true });
                    $.get(url, function(data){
                        self.setState({
                            suggestions: data,
                            searching: false,
                            hideSuggest: false
                        });
                    }).fail(function(jqXhr) {
                        log('error getting suggestions...');
                        log(arguments);
                        self.setState({ searching: false, hideSuggest: true });
                        if ('undefined' !== jqXhr.responseJSON) {
                            self.setState({ error: jqXhr.responseJSON.message });
                        }
                    });
                },
                bindAutoCompleteEvents: function() {
                    var self = this;
                    // keystrokes input reaction
                    Rx.Observable.fromEvent($('input.typeahead'), 'keyup')
                    .map(function (e) {
                        return _.trim(e.target.value);
                    })
                    .filter(function (text) {
                        return text.length > 2;
                    })
                    .debounce(650)
                    .distinctUntilChanged()
                    .subscribe(function(text) {
                        if (self.state.searching) {
                            self.setState({ hideSuggest: true });
                            return;
                        }
                        self.loadSuggestions();
                    });
                    // hide suggestions when click outside
                    Rx.Observable.fromEvent(window, 'click')
                    .filter(function(ev) {
                        if ($(ev.target).hasClass('typeahead')) return false;
                        return true;
                    })
                    .subscribe(function(ev){
                        ev.stopPropagation();
                        self.setState({ hideSuggest: true });
                    });
                },
                componentDidMount: function() {
                    this.bindAutoCompleteEvents();
                }
            });
            setTimeout(function(){
                ReactDOM.render(e(SearchFollowers), document.getElementById('root'));
            }, 1000);
        </script>
        <img class="hidden" src="<?php echo base_url('img/loading-small.gif'); ?>" />
    </body>
</html>
