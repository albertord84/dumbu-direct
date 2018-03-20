<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DUMBU ::: Login</title>
        <meta charset='utf-8'>
        <meta content='IE=edge' http-equiv='X-UA-Compatible'>
        <meta content='width=device-width,initial-scale=1' name='viewport'>
        <link rel="icon" type="image/png" href="<?php echo base_url('img/icon.png'); ?>">
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap.min.css'); ?>'/>
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <style>
            #root {
                width: 300px;
                margin: 40px auto;
            }
            #root .login-form #bt-auth {
                width: 90%;
                margin: 10px auto 0px auto;
            }
            #root .login-form img.loading {
                margin-top: 15px;
            }
        </style>
    </head>
    <body>
        <div id="root"></div>
        <img class="hidden" src="<?php echo base_url('img/preloader.gif'); ?>" />
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react.production.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/create-react-class.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react-dom.production.min.js'); ?>"></script>
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
                userName: '',
                password: '',
                loading: false,
                error: '',
                info: ''
            };
            var UserNameField = createReactClass({
                render: function() {
                    return e('div', { className: 'row' },
                        e('div', { className: "form-group" },
                            e('input', {
                                type: "text",
                                class: "form-control input-lg",
                                id: "username",
                                name: "username",
                                autocomplete: "off",
                                title: "Please, enter you username",
                                placeholder: "Instagram username...",
                                autofocus: "on",
                                onChange: this.props.userNameChange,
                                disabled: this.props.loading }
                            )
                        )
                    );
                }
            });
            var PasswordField = createReactClass({
                render: function() {
                    return e('div', { className: 'row' },
                        e('div', { className: "form-group" },
                            e('input', {
                                type: "password",
                                class: "form-control input-lg",
                                id: "password",
                                name: "password",
                                autocomplete: "off",
                                title: "Account password",
                                placeholder: "Password...",
                                onChange: this.props.passwordChange,
                                disabled: this.props.loading }
                            )
                        )
                    );
                }
            });
            var SubmitButton = createReactClass({
                onClick: function() {
                    this.props.auth();
                },
                render: function() {
                    var loading = this.props.loading;
                    return e('div', { className: 'row text-center' },
                        e('div', { className: loading ? "hidden" : "form-group" },
                            e('button', {
                                type: "button",
                                class: "btn btn-lg btn-success btn-block",
                                id: "bt-auth", onClick: this.onClick }, 'Log In'
                            )
                        ),
                        loading ? e('img', { className: "loading", src: Dumbu.baseUrl + "img/preloader.gif" }) : ''
                    );
                }
            });
            var LoginForm = createReactClass({
                getInitialState: function() {
                    return initialState;
                },
                render: function() {
                    var userName = this.state.userName;
                    var password = this.state.password;
                    var info = this.state.info;
                    var error = this.state.error;
                    var loading = this.state.loading;
                    return e('div', { className: 'row login-form' },
                        e('div', { className: 'row form-group text-center text-muted' },
                            e('h4', null, 'Login to your Instagram ',
                                e('i', { className: "fa fa-instagram" })
                            )
                        ),
                        e(UserNameField, { userNameChange: this.userNameChange, loading: loading }),
                        e(PasswordField, { passwordChange: this.passwordChange, loading: loading }),
                        e(SubmitButton, { auth: this.auth, loading: loading }),
                        info === '' ? '' : e('div', { className: 'small alert alert-info text-center' }, info),
                        error === '' ? '' : e('div', { className: 'small alert alert-danger text-center' }, error)
                    );
                },
                userNameChange: function(ev) {
                    this.setState({
                        info: '', error: '',
                        userName: _.trim(ev.target.value)
                    });
                },
                passwordChange: function(ev) {
                    this.setState({
                        info: '', error: '',
                        password: _.trim(ev.target.value)
                    });
                },
                validate: function() {
                    var userName = _.trim(this.state.userName);
                    var password = _.trim(this.state.password);
                    if (userName === '') {
                        this.setState({ error: 'You must enter the username...' });
                        $('#username').focus();
                        return false;
                    }
                    if (password === '') {
                        this.setState({ error: 'You must enter the password...' });
                        $('#password').focus();
                        return false;
                    }
                    return true;
                },
                auth: function() {
                    var self = this;
                    var isValid = self.validate();
                    if (!isValid) return;
                    self.setState({ loading: true });
                    $.ajax({
                        type: 'POST',
                        url: Dumbu.siteUrl + '/user/auth',
                        data: {
                            username: self.state.userName,
                            password: self.state.password
                        },
                        success: function() {
                            window.location = Dumbu.siteUrl + '/search';
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            log(arguments);
                            self.setState({
                                error: jqXHR.responseJSON.message,
                                loading: false
                            })
                        }
                    });
                },
                componentDidMount() {
                    window['cmp'] = this;
                    var self = this;
                    Rx.Observable.fromEvent(document, 'keyup')
                    .filter(function(ev) {
                        var isOurForm = ev.target.id === 'username' ||
                                        ev.target.id === 'password';
                        return ev.keyCode === 13 && isOurForm;
                    }).subscribe(self.auth);
                }
            });
            ReactDOM.render(e(LoginForm), document.getElementById('root'));
        </script>
    </body>
</html>
