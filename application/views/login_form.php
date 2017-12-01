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
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap-theme.min.css'); ?>'/>
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css'); ?>?<?php echo d_guid(); ?>">
    </head>
    <body>
        <div id="login-overlay" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title" id="myModalLabel">Login to your Instagram <i class="fa fa-instagram" aria-hidden="true"></i></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="well">
                                <form id="loginForm" method="POST"
                                      action="<?php echo base_url() . '?' . d_guid(); ?>"
                                      novalidate="novalidate" name="loginForm">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" id="username"
                                                   name="username" value="" required="" autocomplete="off"
                                                   title="Please, enter you username" data-bind="disable: logging"
                                                   placeholder="Instagram username...">
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control input-lg" id="password"
                                                   name="password" value="" required="" autocomplete="off"
                                                   placeholder="Password..." data-bind="disable: logging"
                                                   title="Please enter your password">
                                            <span class="help-block"></span>
                                        </div>
                                        <button type="button" class="btn btn-lg btn-success btn-block"
                                                id="btAuth" data-bind="enable: canLogIn, visible: !logging()">Log in</button>
                                        <div class="text-center">
                                            <img src="<?php echo base_url('img/loading-small.gif'); ?>"
                                                 data-bind="visible: logging">
                                        </div>
										<div class="alert alert-danger small"
											 data-bind="visible: error().length!==0">
											<p><b>Error:</b>&nbsp;<span data-bind="text: error"></span></p>
										</div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <p class="lead text-center">Sign up, <span class="text-success text-uppercase"><b>Try for free!</b></span></p>
                            <ul class="list-unstyled" style="line-height: 2">
                                <li><span class="fa fa-check text-success"></span> Increase your followers!</li>
                                <li><span class="fa fa-check text-success"></span> Text other users!</li>
                                <li><span class="fa fa-check text-success"></span> Post to possible customers!</li>
                                <li><span class="fa fa-check text-success"></span> Start a campaign!</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/jquery.blockUI.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/redux.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/knockout.js'); ?>"></script>
        <img src="<?php echo base_url('img/loading.gif') . '?' . d_guid(); ?>" class="hidden loading" />
        <script>var Dumbu = Dumbu || Object.assign({ siteUrl: "<?php echo site_url(); ?>" });</script>
        <script src="<?php echo base_url('js/app/user/reducer.js') . '?' . d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/user/controller.js') . '?' . d_guid(); ?>"></script>
    </body>
</html>
