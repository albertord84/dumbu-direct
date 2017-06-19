<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url');
?>
<!DOCTYPE html>
<html data-ng-app="DumbuDirectMessage">
<head>
  <title>DUMBU ::: Direct Message</title>
  <meta charset='utf-8'>
  <meta content='IE=edge' http-equiv='X-UA-Compatible'>
  <meta content='width=device-width,initial-scale=1' name='viewport'>
  <link rel='stylesheet' href='/assets/css/bootstrap.min.css'/>
  <link rel='stylesheet' href='/assets/css/bootstrap-theme.min.css'/>
  <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="/assets/css/sweetalert.css">
  <link rel="stylesheet" href="/assets/css/dumbu-direct.css">
</head>
<body data-ng-controller="MainController">
  <div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="container">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
            <div class="span4 well">
              <form accept-charset="UTF-8" action="" method="POST">
                <div class="row">
                  <div class="col-xs-12">
                    <textarea class="form-control input-lg" 
                      id="new_message" name="new_message" 
                      placeholder="Type in your direct message..." 
                      rows="5"></textarea>
                  </div>
                </div><br>
                <div class="row text-center">
                  <div class="col-xs-12">
                    <button class="btn btn-info btn-lg btn-block" 
                            type="submit">Post Direct Message</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/js/angular.js"></script>
  <script src="/assets/js/lodash.min.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/sweetalert.min.js"></script>
  <script src="/assets/js/core.min.js"></script> <!-- required by sweetalert -->
  <script src="/assets/js/app/compose.js"></script>
</body>
</html>
