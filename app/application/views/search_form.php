<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url');
?>
<!DOCTYPE html>
<html data-ng-app="DumbuDirectSearch">
<head>
	<title>DUMBU ::: Search</title>
	<meta charset='utf-8'>
	<meta content='IE=edge' http-equiv='X-UA-Compatible'>
	<meta content='width=device-width,initial-scale=1' name='viewport'>
	<link rel='stylesheet' href='/assets/css/bootstrap.min.css'/>
	<link rel='stylesheet' href='/assets/css/bootstrap-theme.min.css'/>
  <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="/assets/css/dumbu-direct.css">
</head>
<body data-ng-controller="MainController">
	<div class="container" style="margin-top: 8%;">
    <div class="col-md-6 col-md-offset-3">     
      <div class="row">
        <div id="logo" class="text-center">
          <h1>DUMBU</h1>
          <p>Get more real followers</p>
        </div>
        <form role="form" id="search-form">
          <div class="form-group">
            <div class="input-group">
              <input id="ref-prof" class="form-control" type="text" name="search" 
                     placeholder="Reference profile..." class="typeahead" 
                     required />
              <span class="input-group-btn">
                <button class="btn btn-success" type="submit">
                  <i class="glyphicon glyphicon-search" aria-hidden="true"></i> Search
                </button>
              </span>
            </div>
          </div>
        </form>
      </div>            
    </div>
  </div>
  <script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/js/angular.js"></script>
	<script src="/assets/js/lodash.min.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/typeahead.min.js"></script>
  <script src="/assets/js/handlebars.min.js"></script>
  <script src="/assets/js/app/search.js"></script>
</body>
</html>
