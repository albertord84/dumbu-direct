<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url');
?>
<!DOCTYPE html>
<html>
<head>
  <title>DUMBU ::: Login</title>
  <meta charset='utf-8'>
  <meta content='IE=edge' http-equiv='X-UA-Compatible'>
  <meta content='width=device-width,initial-scale=1' name='viewport'>
  <link rel='stylesheet' href='/assets/css/bootstrap.min.css'/>
  <link rel='stylesheet' href='/assets/css/bootstrap-theme.min.css'/>
  <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
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
              <form id="loginForm" method="POST" action="/login/" novalidate="novalidate">
                <div class="form-group">
                  <label for="username" class="control-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" value="" required="" title="Please enter you username" placeholder="example@gmail.com">
                  <span class="help-block"></span>
                </div>
                <div class="form-group">
                  <label for="password" class="control-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" value="" required="" title="Please enter your password">
                  <span class="help-block"></span>
                </div>
                <div id="loginErrorMsg" class="alert alert-error hide">Username/password incorrect!</div>
                <button type="submit" class="btn btn-success btn-block">Log in</button>
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
</body>
</html>
