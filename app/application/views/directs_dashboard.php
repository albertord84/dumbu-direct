<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html data-ng-app="DumbuDirectDashboard">
    <head>
        <title>DUMBU ::: Directs dashboard</title>
        <meta charset='utf-8'>
        <meta content='IE=edge' http-equiv='X-UA-Compatible'>
        <meta content='width=device-width,initial-scale=1' name='viewport'>
        <link rel="icon" type="image/png" href="/assets/img/icon.png">
        <link rel='stylesheet' href='/assets/css/bootstrap.min.css'/>
        <link rel='stylesheet' href='/assets/css/bootstrap-theme.min.css'/>
        <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="/assets/css/sweetalert.css">
        <link rel="stylesheet" href="/assets/css/dumbu-direct.css?<?php echo d_guid(); ?>">
    </head>
    <body data-ng-controller="MainController">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                    </div>
                    <h1 class="text-center"><?php echo $message; ?></h1>
                </div>
            </div>
        </div>
        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        <script src="/assets/js/angular.js"></script>
        <script src="/assets/js/lodash.min.js"></script>
        <script src="/assets/js/moment.js"></script>
        <script src="/assets/js/sweetalert.min.js"></script>
        <script src="/assets/js/core.min.js"></script> <!-- required by sweetalert -->
        <script src="/assets/js/app/directs_dashboard.js"></script>
        <script src="/assets/js/app/DirectsService.js"></script>
        <script>d_Session = <?php echo json_encode($session); ?>;</script>
        <img src="/assets/img/loading.gif?<?php echo d_guid(); ?>" class="hidden" />
    </body>
</html>
