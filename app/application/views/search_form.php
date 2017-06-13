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
    <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">     
      <div class="row">
        <div id="logo" class="text-center">
          <h1>DUMBU</h1>
          <p>Get more real followers</p>
        </div>
        <form role="form" id="search-form">
          <div class="form-group">
            <div class="input-group">
              <input id="ref-prof" class="form-control" type="text" name="search" 
                     placeholder="Select reference profiles..." class="typeahead" 
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
      <div class="row selected-profs">
        <div class="col-xs-12 col-sm-6 col-md-6"
             data-ng-repeat="profile in selectedProfs">
          <div class="card">
            <div class="text-center">
              <img class="card-img-top img-circle" 
                   data-ng-src="{{profile.profPic}}"
                   alt="Profile photo">
            </div>
            <div class="card-block">
              <h4 class="card-title" data-ng-bind="profile.username"></h4>
              <div class="card-text text-muted" data-ng-bind="profile.fullName">
              </div>
            </div>
            <div class="card-footer">
              <span class="float-right" data-ng-bind="profile.byline"></span>
              <span><i class=""></i></span>
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
  <script src="/assets/js/typeahead.min.js"></script>
  <script src="/assets/js/handlebars.min.js"></script>
  <script src="/assets/js/app/search.js"></script>
</body>
</html>
