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
    <body data-ng-controller="promo">
        <div id="promos-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                        <p>Manage the scheduled promotions...</p>
                    </div>
                    <?php if (isset($error)) { ?>
                        <div class="error text-center text-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading text-muted">Promotions</div>
                        <div class="panel-body">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#active" data-toggle="tab">Active</a></li>
                                <li><a href="#sent" data-toggle="tab">Sent</a></li>
                                <li><a href="#failed" data-toggle="tab">Failed</a></li>
                            </ul>
                            <div class="promo-tabs tab-content">
                                <div id="active" class="tab-pane fade in active">
                                    <div class="row text-center" data-ng-if="!activePromos">
                                        <div class="well-lg text-muted"><h4><b>Loading...</b></h4></div>
                                    </div>
                                    <div class="row text-center" data-ng-if="activePromos.length==0">
                                        <div class="well-lg text-muted"><h4><b>No data...</b></h4></div>
                                    </div>
                                    <table class="table table-striped table-hover small"
                                           data-ng-if="activePromos.length>0">
                                        <tr>
                                            <th>Sender</th>
                                            <th>Promotional text</th>
                                            <th></th>
                                        </tr>
                                        <tr data-ng-repeat="activePromo in activePromos">
                                            <td data-ng-bind="activePromo.sender.username"></td>
                                            <td class="text-muted">
                                                <span data-ng-bind="activePromo.msg_text | limitTo: 50"></span>
                                                <span>...</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="sent" class="tab-pane fade">
                                    <div class="row text-center" data-ng-if="!sentPromos">
                                        <div class="well-lg text-muted"><h4><b>Loading...</b></h4></div>
                                    </div>
                                    <div class="row text-center" data-ng-if="sentPromos.length==0">
                                        <div class="well-lg text-muted"><h4><b>No data...</b></h4></div>
                                    </div>
                                    <table class="table table-striped table-hover small"
                                           data-ng-if="sentPromos.length>0">
                                        <tr>
                                            <th>Sender</th>
                                            <th>Promotional text</th>
                                            <th></th>
                                        </tr>
                                        <tr data-ng-repeat="sentPromo in sentPromos">
                                            <td data-ng-bind="sentPromo.sender.username"></td>
                                            <td class="text-muted">
                                                <span data-ng-bind="sentPromo.msg_text | limitTo: 50"></span>
                                                <span>...</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="failed" class="tab-pane fade">
                                    <div class="row text-center" data-ng-if="!failedPromos">
                                        <div class="well-lg text-muted"><h4><b>Loading...</b></h4></div>
                                    </div>
                                    <div class="row text-center" data-ng-if="failedPromos.length==0">
                                        <div class="well-lg text-muted"><h4><b>No data...</b></h4></div>
                                    </div>
                                    <table class="table table-striped table-hover small"
                                           data-ng-if="failedPromos.length>0">
                                        <tr>
                                            <th>Sender</th>
                                            <th>Promotional text</th>
                                            <th></th>
                                        </tr>
                                        <tr data-ng-repeat="failedPromo in failedPromos">
                                            <td data-ng-bind="failedPromo.sender.username"></td>
                                            <td class="text-muted">
                                                <span data-ng-bind="failedPromo.msg_text | limitTo: 50"></span>
                                                <span>...</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
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
        <script src="<?php echo base_url('js/lib/jquery.pulsate.min.js'); ?>"></script>
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
