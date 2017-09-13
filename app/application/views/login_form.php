<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html data-ng-app="DumbuDirectLogin">
<head>
  <title>DUMBU ::: Login</title>
  <meta charset='utf-8'>
  <meta content='IE=edge' http-equiv='X-UA-Compatible'>
  <meta content='width=device-width,initial-scale=1' name='viewport'>
  <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/icon.png'); ?>">
  <link rel='stylesheet' href='<?php echo base_url('assets/css/bootstrap.min.css'); ?>'/>
  <link rel='stylesheet' href='<?php echo base_url('assets/css/bootstrap-theme.min.css'); ?>'/>
  <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/sweetalert.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/dumbu-direct.css'); ?>?<?php echo d_guid(); ?>">
</head>
<body data-ng-controller="MainController">
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
                    action="/index.php/<?php echo d_guid(); ?>"
                    novalidate="novalidate" name="loginForm">
                <fieldset data-ng-disabled="authenticating">
                  <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="email" class="form-control" id="username" valid-email
                           name="username" value="" required="" autocomplete="off"
                           data-ng-model="username" title="Please enter you username"
                           placeholder="example@gmail.com" data-ng-keypress="inputKeypress($event)">
                    <span class="help-block"></span>
                  </div>
                  <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" class="form-control" id="password"
                           name="password" value="" required="" autocomplete="off"
                           data-ng-model="password" title="Please enter your password"
                           data-ng-keypress="inputKeypress($event)">
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
              <li><span class="fa fa-check text-success"></span> Send direct messages to other users!</li>
              <li><span class="fa fa-check text-success"></span> Post to possible customers!</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url('assets/js/lib/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/js/lib/bootstrap.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/js/lib/angular.js'); ?>"></script>
  <script src="<?php echo base_url('assets/js/lib/lodash.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/js/lib/sweetalert.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/js/lib/core.min.js'); ?>"></script> <!-- required by sweetalert -->
  <script src="<?php echo base_url('assets/js/lib/app/login.js'); ?>?<?php echo d_guid(); ?>"></script>
  <script src="<?php echo base_url('assets/js/lib/app/LoginService.js'); ?>?<?php echo d_guid(); ?>"></script>
  <?php include_once __DIR__ . '/js_globals.php'; ?>
  <img src="<?php echo base_url('assets/img/loading.gif'); ?>?<?php echo d_guid(); ?>" class="hidden loading" />
</body>
</html>
