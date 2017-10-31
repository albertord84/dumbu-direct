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
    <body data-ng-controller="promoStats">
        <div id="promos-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                        <p>Promotions delivery stats</p>
                    </div>
                    <?php if (isset($error)) { ?>
                        <div class="error text-center text-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading text-muted">Stats</div>
                        <div class="panel-body">
                            <ul class="nav nav-tabs">
                                <li class="today"><a href="#today" data-toggle="tab">Today</a></li>
                                <li class="last"><a href="#last" data-toggle="tab">Last sent</a></li>
                            </ul>
                            <div class="promo-tabs tab-content">
                                <div id="today" class="tab-pane fade in active">
                                    <div class="row text-center" data-ng-if="!todayPromos">
                                        <h4>Loading...</h4>
                                    </div>
                                    <div class="row text-center" data-ng-if="todayPromos.length===0">
                                        <h4>No data...</h4>
                                    </div>
                                    <div class="row" data-ng-if="todayPromos.length>0">
                                        <br>
                                        <div class="col-xs-4 col-xs-offset-1 text-muted"><b>Client / User</b></div>
                                        <div class="col-xs-3 col-xs-offset-2 text-muted text-center"><b>Sent messages</b></div>
                                    </div>
                                    <div data-ng-repeat="todaySent in todayPromos" class="row" data-ng-if="todayPromos.length>0">
                                        <div class="col-xs-4 col-xs-offset-1" data-ng-bind="todaySent.client"></div>
                                        <div class="col-xs-3 col-xs-offset-2 text-center" data-ng-bind="todaySent.sent"></div>
                                    </div>
                                </div>
                                <div id="last" class="tab-pane fade in">
                                    <div class="row text-center" data-ng-if="!lastPromos">
                                        <h4>Loading...</h4>
                                    </div>
                                    <div class="row text-center" data-ng-if="lastPromos.length===0">
                                        <h4>No data...</h4>
                                    </div>
                                    <div class="row" data-ng-if="lastPromos.length>0">
                                        <br>
                                        <div class="col-xs-4 col-xs-offset-1 text-muted"><b>Client / User</b></div>
                                        <div class="col-xs-3 col-xs-offset-2 text-muted text-center"><b>Sent messages</b></div>
                                    </div>
                                    <div data-ng-repeat="lastSent in lastPromos" class="row" data-ng-if="lastPromos.length>0">
                                        <div class="col-xs-4 col-xs-offset-1" data-ng-bind="lastSent.client"></div>
                                        <div class="col-xs-3 col-xs-offset-2 text-center" data-ng-bind="lastSent.sent"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/knockout.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/angular.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script> <!-- required by sweetalert -->
        <script src="<?php echo base_url('js/lib/jquery.pulsate.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/jquery.blockUI.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/typeahead.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/handlebars.min.js'); ?>"></script>
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
