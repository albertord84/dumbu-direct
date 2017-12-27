<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html data-ng-app="dumbu">
    <head>
        <title>DUMBU ::: Directs dashboard</title>
        <meta charset='utf-8'>
        <meta content='IE=edge' http-equiv='X-UA-Compatible'>
        <meta content='width=device-width,initial-scale=1' name='viewport'>
        <link rel="icon" type="image/png" href="<?php echo base_url('img/icon.png'); ?>">
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap.min.css'); ?>'/>
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap-theme.min.css'); ?>'/>
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css').'?'.d_guid(); ?>">
    </head>
    <body data-ng-controller="dashboard">
        <div class="directs-dashboard container">
            <div class="row">
                <?php include __DIR__ . '/navbar.php'; ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                    </div>
                    <?php if ($message!==NULL) { ?>
                    <p class="text-center text-muted"><b>You sent this message...</b></p>
                    <h3 class="text-center"><?php echo $message; ?></h3>
                    <?php } ?>
                    <?php if ($follower_names !== NULL) { ?>
                    <p class="text-center text-muted"><b>to the followers:</b></p>
                    <div class="row text-center followers-list">
                        <?php foreach ($follower_names as $follower_name) { ?>
                        <span class="follower-name panel panel-default"><?php echo $follower_name; ?></span>
                        <?php } ?>
                    </div>
                    <p class="text-center text-muted"><b>Your request will be processed soon...</b></p>
                    <p class="text-center text-muted small">If you keep this page opened, you will be notified</p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/angular.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/moment.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/app/dumbu.js').'?'.d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/controller/dashboard.js').'?'.d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/service/dashboard.js').'?'.d_guid(); ?>"></script>
        <img src="<?php echo base_url('img/loading.gif').'?'.d_guid(); ?>" class="hidden" />
    </body>
</html>
