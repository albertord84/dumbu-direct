<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html data-ng-app="dumbu">
    <head>
        <title>DUMBU ::: Direct Message</title>
        <meta charset='utf-8'>
        <meta content='IE=edge' http-equiv='X-UA-Compatible'>
        <meta content='width=device-width,initial-scale=1' name='viewport'>
        <link rel="icon" type="image/png" href="<?php echo base_url('img/icon.png'); ?>">
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap.min.css'); ?>'/>
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap-theme.min.css'); ?>'/>
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css') . '?' . d_guid(); ?>">
    </head>
    <body data-ng-controller="promoLog">
        <div id="promo-log-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                        <p>Messages and promotions delivery log</p>
                    </div>
                    <?php if (isset($error)) { ?>
                        <div class="error text-center text-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <div class="span4 well log-lines">
                        <div class="row text-muted text-center" data-ng-if="logLines.length == 0">
                            <h4>No new log lines yet...</h4>
                        </div>
                        <div class="row" data-ng-repeat="line in logLines track by $index">
                            <p class="text-muted small" data-ng-bind="line"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/angular.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script> <!-- required by sweetalert -->
        <script src="<?php echo base_url('js/lib/typeahead.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/handlebars.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/jquery.pulsate.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/moment.js'); ?>"></script>
        <script src="<?php echo base_url('js/app/dumbu.js') . '?' . d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/controller/promo.js') . '?' . d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/service/promo.js') . '?' . d_guid(); ?>"></script>
        <img src="<?php echo base_url('img/loading.gif') . '?' . d_guid(); ?>" class="hidden loading" />
        <script>
            Dumbu.siteUrl = "<?php echo site_url(); ?>";
            Dumbu.baseUrl = "<?php echo base_url(); ?>";
        </script>
    </body>
</html>
