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
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css'); ?>?<?php echo d_guid(); ?>">
    </head>
    <body>
        <div id="root"></div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/babel.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react.production.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react-dom.production.min.js'); ?>"></script>
        <script>var Dumbu = Dumbu || Object.assign({ siteUrl: "<?php echo site_url(); ?>" });</script>
        <script type="text/babel">
            class LoginForm extends React.Component {
                constructor(props) {
                    super(props);
                    this.state = {
                        username: '',
                        password: '',
                        canLogIn: false,
                        logging: false,
                        error: '',
                        message: ''
                    };
                }
                keyUp(event) {
                    this.setState({ message: '', error: '' });
                    if (event.keyCode === 13 && this.state.canLogIn) {
                        this.checkAuth();
                        return;
                    }
                    if (event.target.id === 'username') {
                        this.setState({ username: _.trim(event.target.value) });
                    } else if (event.target.id === 'password') {
                        this.setState({ password: event.target.value });
                    }
                    this.setState({
                        canLogIn: (this.state.username.length > 2 && this.state.password.length > 2)
                    });
                }
                checkAuth() {
                    this.setState({logging: true});
                    $.post(Dumbu.siteUrl + '/user/auth',{
                        username: this.state.username,
                        password: this.state.password
                    }, (response) => {
                        this.setState({message: 'Redirecting...'});
                        setTimeout(() => {
                            window.location = Dumbu.siteUrl + '/search/index';
                        }, 2000);
                    }).fail((response) => {
                        if ('undefined' !== typeof console) { console.log(arguments); }
                        this.setState({ error: 'Login error. See console...' });
                    }).always(() => {
                        this.setState({logging: false});
                    });
                }
                componentDidMount() {
                    Rx.Observable.fromEvent(window, 'keyup')
                        .filter((event) => {
                            return event.target.id === 'username' ||
                                event.target.id === 'password'
                        })
                        .subscribe((event) => this.keyUp(event));
                }
                render() {
                    const canLogIn = this.state.canLogIn;
                    const logging = this.state.logging;
                    const message = this.state.message;
                    const error = this.state.error;
                    return (
                        <div id="login-overlay" className="modal-dialog">
                            <div className="modal-content">
                                <div className="modal-header text-center">
                                    <h4 className="modal-title" id="myModalLabel">Login to your Instagram <i className="fa fa-instagram" aria-hidden="true"></i></h4>
                                </div>
                                <div className="modal-body">
                                    <div className="row">
                                        <div className="col-xs-12 col-sm-6">
                                            <div className="well">
                                                <fieldset>
                                                    <div className="form-group">
                                                        <input type="text" className="form-control input-lg" id="username"
                                                            name="username" autocomplete="off"
                                                            title="Please, enter you username"
                                                            placeholder="Instagram username..."
                                                            autofocus="on" disabled={logging}/>
                                                        <span className="help-block"></span>
                                                    </div>
                                                    <div className="form-group">
                                                        <input type="password" className="form-control input-lg" id="password"
                                                            name="password" autocomplete="off"
                                                            placeholder="Password..." title="Please enter your password"
                                                            disabled={logging}/>
                                                        <span className="help-block"></span>
                                                    </div>
                                                    <button type="button" className={logging?"hidden":"btn btn-lg btn-success btn-block"}
                                                            id="btAuth" disabled={!canLogIn || logging}
                                                            onClick={() => this.checkAuth()}>Log in</button>
                                                    <div className={logging?"text-center":"hidden"}>
                                                        <img src="<?php echo base_url('img/loading-small.gif'); ?>"
                                                            className="loading"/>
                                                    </div>
                                                    <div className={error===''?"hidden":"alert alert-danger small"}>
                                                        <p><b>Error:</b>&nbsp;<span className="text">{error}</span></p>
                                                    </div>
                                                    <div className={message===''?"hidden":"alert alert-info small"}>
                                                        <p className="text">{message}</p>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div className="col-xs-12 col-sm-6">
                                            <p className="lead text-center">Sign up, <span className="text-success text-uppercase"><b>Try for free!</b></span></p>
                                            <ul className="list-unstyled">
                                                <li><span className="fa fa-check text-success"></span> Increase your followers!</li>
                                                <li><span className="fa fa-check text-success"></span> Text other users!</li>
                                                <li><span className="fa fa-check text-success"></span> Post to possible customers!</li>
                                                <li><span className="fa fa-check text-success"></span> Start a campaign!</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                }
            }
            ReactDOM.render(<LoginForm/>, document.getElementById('root'));
        </script>
    </body>
</html>
