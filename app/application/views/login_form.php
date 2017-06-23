<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$uuid = substr(md5(date('ds')), 0, 10);
?>
<!DOCTYPE html>
<html data-ng-app="DumbuDirectLogin">
<head>
  <title>DUMBU ::: Login</title>
  <meta charset='utf-8'>
  <meta content='IE=edge' http-equiv='X-UA-Compatible'>
  <meta content='width=device-width,initial-scale=1' name='viewport'>
  <link rel="icon" type="image/png" href="/assets/img/icon.png">
  <link rel='stylesheet' href='/assets/css/bootstrap.min.css'/>
  <link rel='stylesheet' href='/assets/css/bootstrap-theme.min.css'/>
  <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="/assets/css/sweetalert.css">
  <link rel="stylesheet" href="/assets/css/dumbu-direct.css?<?php echo $uuid; ?>">
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
                    action="/index.php/<?php echo $uuid; ?>"
                    novalidate="novalidate">
                <fieldset data-ng-disabled="authenticating">
                  <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" class="form-control" id="username"
                           name="username" value="" required="" autocomplete="off"
                           data-ng-model="username" title="Please enter you username"
                           placeholder="example@gmail.com">
                    <span class="help-block"></span>
                  </div>
                  <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" class="form-control" id="password"
                           name="password" value="" required="" autocomplete="off"
                           data-ng-model="password" title="Please enter your password">
                    <span class="help-block"></span>
                  </div>
                  <button type="submit" class="btn btn-success btn-block"
                          data-ng-click="auth()"
                          data-ng-disabled="!password || !username">Log in</button>
                  <div class="alert alert-error" data-ng-if="loginError"></div>
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
  <script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/angular.js"></script>
  <script src="/assets/js/lodash.min.js"></script>
  <script src="/assets/js/sweetalert.min.js"></script>
  <script src="/assets/js/core.min.js"></script> <!-- required by sweetalert -->
  <script src="/assets/js/app/login.js?<?php echo $uuid; ?>"></script> <!-- required by sweetalert -->
  <script>d_Session = <?php echo json_encode($session); ?>;</script>
</body>
</html>
