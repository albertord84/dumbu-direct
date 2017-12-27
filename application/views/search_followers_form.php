<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DUMBU ::: Search</title>
        <meta charset='utf-8'>
        <meta content='IE=edge' http-equiv='X-UA-Compatible'>
        <meta content='width=device-width,initial-scale=1' name='viewport'>
        <link rel="icon" type="image/png" href="<?php echo base_url('img/icon.png'); ?>">
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap.min.css'); ?>'/>
        <link rel='stylesheet' href='<?php echo base_url('css/bootstrap-theme.min.css'); ?>'/>
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css'); ?>?<?php echo d_guid(); ?>">
    </head>
    <body>
        <div id="search-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="form-container">
                        <div id="logo" class="text-center">
                            <h1>DUMBU</h1>
                            <p>Get more real followers</p>
                        </div>
                        <form role="form" id="search-form" method="POST"
							action="<?php echo site_url('/compose/message'); ?>">
                            <div class="form-group">
                                <img class="async-loading hidden"
                                     src="<?php echo base_url('img/loading-small.gif'); ?>">
                                <div class="input-group">
                                    <input id="ref-prof" class="form-control" type="text" name="search"
                                           placeholder="Select reference profiles..." class="typeahead"
                                           required />
                                    <span class="input-group-btn">
                                        <button class="btn btn-success text-them" type="button">
                                            <i class="glyphicon glyphicon-pencil"
                                               aria-hidden="true"></i>Text&CloseCurlyQuote; em
                                        </button>
                                    </span>
                                </div>
                            </div>
							<input type="hidden" name="follower_ids">
							<input type="hidden" name="follower_names">
                        </form>
                    </div>
                </div>
				<div class="row selected-profs">
				</div>
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/typeahead.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/handlebars.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script> <!-- required by sweetalert -->
		<script src="<?php echo base_url('js/lib/redux.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
		<script>var Dumbu = Dumbu || Object.assign({ siteUrl: "<?php echo site_url(); ?>" });</script>
		<script src="<?php echo base_url('js/app/user/reducer.js') . '?' . d_guid(); ?>"></script>
		<script src="<?php echo base_url('js/app/search/reducer.js') . '?' . d_guid(); ?>"></script>
		<script src="<?php echo base_url('js/app/search/controller.js') . '?' . d_guid(); ?>"></script>
		<script src="<?php echo base_url('js/app/search/view.js') . '?' . d_guid(); ?>"></script>
    </body>
</html>
