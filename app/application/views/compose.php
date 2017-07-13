<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$uuid = substr(md5(date('ds')), 0, 10);
?>
<!DOCTYPE html>
<html data-ng-app="DumbuDirectMessage">
    <head>
        <title>DUMBU ::: Direct Message</title>
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
        <div id="compose-container" class="container">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="logout"><i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
            </nav>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                        <p><a href="<?php echo site_url('search') ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;&nbsp;</a>Appeal the attention of previously selected profiles...</p>
                    </div>
                    <div class="span4 well">
                        <form id="formCompose" accept-charset="UTF-8" action="" method="POST">
                            <fieldset data-ng-disabled="processing">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <textarea class="form-control input-lg"
                                                  id="message" name="message"
                                                  placeholder="Type in your direct message..."
                                                  rows="5" form="formCompose"></textarea>
                                        <input type="hidden" name="_m" id="_m" />
                                    </div>
                                </div><br>
                                <div class="row text-center">
                                    <div class="col-xs-12">
                                        <button class="btn btn-info btn-lg btn-block"
                                                type="button"
                                                data-ng-click="startDirects()">Post Direct Message</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/angular.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/core.min.js'); ?>"></script> <!-- required by sweetalert -->
        <script src="<?php echo base_url('assets/js/jquery.pulsate.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/app/compose.js'); ?>?<?php echo d_guid(); ?>"></script>
        <script src="<?php echo base_url('assets/js/app/ComposeService.js'); ?>?<?php echo d_guid(); ?>"></script>
        <?php include_once __DIR__ . '/js_globals.php'; ?>
    </body>
</html>
