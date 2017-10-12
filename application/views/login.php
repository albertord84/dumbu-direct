<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html data-ng-app="dumbu">
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
<body data-ng-controller="login">
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
                <fieldset data-ng-disabled="authenticating">
                  <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" class="form-control" id="username" 
                           name="username" value="" required="" autocomplete="off"
                           data-ng-model="username" title="Please enter you username"
                           placeholder="my_instagram_usr_01">
                    <span class="help-block"></span>
                  </div>
                  <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" class="form-control" id="password"
                           name="password" value="" required="" autocomplete="off"
                           placeholder="Some secret..."
                           data-ng-model="password" title="Please enter your password">
                    <span class="help-block"></span>
                  </div>
                  <button type="button" class="btn btn-success btn-block"
                          data-ng-click="auth()" id="bt-auth"
                          data-ng-disabled="loginForm.$invalid">Log in</button>
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
  <script src="<?php echo base_url('js/lib/angular.js'); ?>"></script>
  <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
  <script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
	<script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
	<script src="<?php echo base_url('js/lib/jquery.blockUI.js'); ?>"></script>
	<script src="<?php echo base_url('js/app/dumbu.js').'?'.d_guid(); ?>"></script>
	<script src="<?php echo base_url('js/app/controller/login.js').'?'.d_guid(); ?>"></script>
	<script src="<?php echo base_url('js/app/service/login.js').'?'.d_guid(); ?>"></script>
  <img src="<?php echo base_url('img/loading.gif').'?'.d_guid(); ?>" class="hidden loading" />
	<script>Dumbu.siteUrl = "<?php echo site_url(); ?>";</script>
</body>
</html>
