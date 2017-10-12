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
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css').'?'.d_guid(); ?>">
    </head>
    <body data-ng-controller="compose">
        <div id="compose-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                        <p><a href="<?php echo site_url('search') ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;&nbsp;</a>Appeal the attention of previously selected profiles...</p>
                    </div>
                    <?php if (isset($error)) { ?>
                    <div class="error text-center text-danger">
                        <?php echo $error; ?>
                    </div>
                    <?php } ?>
                    <div class="span4 well">
                        <form id="formCompose" accept-charset="UTF-8"
                              action="<?php echo site_url('user/dashboard'); ?>" method="POST">
                            <fieldset data-ng-disabled="processing">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <textarea class="form-control input-lg"
                                                  id="message" name="_m"
                                                  placeholder="Type in your direct message..."
                                                  rows="5" form="formCompose"
                                                  data-ng-model="msgText"></textarea>
                                        <input type="hidden" name="message"
                                               value="{{msgText}}">
                                    </div>
                                </div><br>
                                <div class="row text-center">
                                    <div class="col-xs-12">
                                        <button class="btn btn-info btn-lg btn-block"
                                                type="submit" 
                                                data-ng-disabled="!msgText">Post Direct Message</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
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
        <script src="<?php echo base_url('js/app/dumbu.js').'?'.d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/controller/compose.js').'?'.d_guid(); ?>"></script>
        <script src="<?php echo base_url('js/app/service/compose.js').'?'.d_guid(); ?>"></script>
        <script>
          Dumbu.siteUrl = "<?php echo site_url(); ?>";
          Dumbu.baseUrl = "<?php echo base_url(); ?>";
        </script>
    </body>
</html>
