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
  <link rel="stylesheet" href="/assets/css/sweetalert.css">
  <link rel="stylesheet" href="/assets/css/dumbu-direct.css">
</head>
<body data-ng-controller="MainController">
	<div class="container">
    <div class="compose-direct pull-right bg-white"
         data-ng-if="selectedProfs.length != 0">
      <a class="btn btn-default btn-lg" href role="button" 
         data-ng-click="composeDirect()">
        <span class="glyphicon glyphicon-pencil"></span>
      </a>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">     
      <div class="row">
        <div class="form-container">
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
      </div>
      <div class="row selected-profs">
        <div class="center" data-ng-repeat="profile in selectedProfs">
          <div class="col-xs-10 col-xs-offset-1 col-sm-4 col-sm-offset-1 col-md-3 col-md-offset-1 col-lg-3 col-lg-offset-1">
            <div class="panel panel-default">
              <div class="panel-heading text-center">
                <button type="button" class="close" 
                        data-ng-click="removeSelectedProfile($event)"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <img class="card-img-top" alt="Profile photo" 
                     data-ng-src="{{profile.profPic}}">
              </div>
              <div class="panel-body text-center">
                <h4 class="" data-ng-bind="profile.username"></h4>
                <div class="text-muted" 
                     data-ng-bind="profile.fullName | cutFullName">
                </div>
              </div>
              <div class="panel-footer text-center text-muted small">
                <span data-ng-bind="profile.byline"></span>
              </div>
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
  <script src="/assets/js/sweetalert.min.js"></script>
  <script src="/assets/js/core.min.js"></script> <!-- required by sweetalert -->
  <script src="/assets/js/app/search.js"></script>
</body>
</html>
