<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
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
    <body>
        <div id="promos-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                    <div id="logo" class="text-center">
                        <h1>DUMBU</h1>
                        <p>Manage the user accounts...</p>
                    </div>
                    <?php if (isset($error)) { ?>
                        <div class="error text-center text-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading text-muted">User Accounts</div>
                        <div class="panel-body">
							<input type="text" data-bind="textInput: searchTerms" class="small pull-right"
								placeholder="Search terms...">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#active" data-toggle="tab">Active</a></li>
                            </ul>
                            <div class="promo-tabs tab-content">
                                <div id="active" class="tab-pane fade in active">
                                    <div class="row text-center" data-bind="if: accounts.accounts().length===0">
                                        <div class="well-lg text-muted">
                                            <h4><b>No data...</b></h4>
                                            <a href data-bind="click: function(data, ev){ store.dispatch({ type: 'REFRESH_ACCOUNTS', payload: { data: data, event: ev } }); }" class="promo-action" title="Reload active promos"><i class="fa fa-retweet"></i></a>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-hover small"
                                           data-bind="if: accounts.accounts().length > 0">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Instagram ID</th>
                                                <th>Privileges</th>
                                                <th class="text-right">
                                                    <a href data-bind="click: function(data, ev){ store.dispatch({ type: 'REFRESH_ACCOUNTS', payload: { data: data, event: ev } }); }"
                                                       class="promo-action text-info" title="Refresh list"><i class="fa fa-refresh"></i></a>&nbsp;&nbsp;
                                                    <a href class="promo-action text-info" data-toggle="modal" data-target="#new-account"
                                                       title="Add new user account"><i class="fa fa-user-plus"></i></a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody data-bind="foreach: accounts.accounts()">
                                            <tr class="account">
                                                <td>
                                                    <span data-bind="text: username"></span>
                                                </td>
                                                <td class="text-muted">
                                                    <span data-bind="text: pk"></span>
                                                </td>
                                                <td>
                                                    <span data-bind="if: priv === '1'" class="text-muted"><b>Admin</b></span>
                                                    <span data-bind="if: priv !== '1'" class="text-muted">User</span>
                                                </td>
                                                <td>
                                                    <span class="pull-right">
                                                        <a href data-bind="click: function(data, ev){ store.dispatch({ type: 'REMOVE_ACCOUNT', payload: { data: data, event: ev } }); }" class="promo-action text-danger" title="Remove account"><i class="fa fa-remove"></i></a>
                                                    </span>
                                                    <span class="pull-right">
                                                        <a href data-bind="click: function(data, ev){ store.dispatch({ type: 'CHANGE_PRIV', payload: { data: data, event: ev } }); }" class="promo-action text-success" title="Change privileges"><i class="fa fa-user-secret"></i></a>
                                                        &nbsp;
                                                    </span>
                                                    <span class="pull-right">
                                                        <a href data-bind="click: function(data, ev){ store.dispatch({ type: 'COLLECT_FOLLOWERS', payload: { data: data, event: ev } }); }"
                                                        class="promo-action text-info" title="Collect followers list"><i class="fa fa-users"></i></a>
                                                        &nbsp;
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="text-right small text-muted">Total accounts:&nbsp;<span data-bind="text: accounts.count()"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="new-account" class="modal fade">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h4 class="modal-title"><b>New account data</b></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="text" class="form-control input-lg typeahead"
                                        id="account-name" data-bind="textInput: newAccount.userName"
                                        placeholder="Instagram account name...">
                                        <img class="async-loading hidden"
                                             src="<?php echo base_url('img/loading-small.gif'); ?>">
                                </div><br>
                                <div class="form-group">
                                    <input type="password" class="form-control input-lg"
                                        id="password" data-bind="textInput: newAccount.password"
                                        name="password" placeholder="Instagram password...">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control input-lg"
                                        id="password" data-bind="textInput: newAccount.pk, disable: true"
                                        name="pk" placeholder="Instagram ID...">
                                </div>
                                <div class="form-group privs">
                                    <div class="col-xs-6 text-right">
                                        <label for="priv-admin">Administrator</label>
                                    </div>
                                    <div class="col-xs-6 text-left">
                                        <input type="checkbox" class="checkbox"
                                            id="priv-admin" data-bind="checked: newAccount.priv"
                                            name="privAdmin">
                                    </div>
                                </div>
                            </div>
                        </div><br>
                        <div class="row text-center">
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <button class="btn btn-success btn-lg btn-block"
                                        onclick="store.dispatch({ type: 'ADD_ACCOUNT' })">Create</button>
                                </div>
                                <div class="col-xs-6">
                                    <button class="btn btn-danger btn-lg btn-block"
                                        data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="modal fade promo-text-change">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header text-center">
						<h4 class="modal-title"><b>Modify the promo text</b></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
									<textarea type="text" class="form-control input-lg"
										   data-ng-model="modifiedText" rows="6"
										   placeholder="New promo text...">
									</textarea>
								</div>
							</div>
						</div><br>
						<div class="row text-center">
							<div class="form-group">
								<div class="col-xs-6">
									<button class="btn btn-success btn-lg btn-block"
											data-ng-click="modifyText()">Accept</button>
								</div>
								<div class="col-xs-6">
									<button class="btn btn-danger btn-lg btn-block"
											data-dismiss="modal">Cancel</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/knockout.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/redux.js'); ?>"></script>
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
        <script src="<?php echo base_url('js/app/accounts.js') . '?' . d_guid(); ?>"></script>
        <img src="<?php echo base_url('img/loading.gif') . '?' . d_guid(); ?>" class="hidden loading" />
        <script>
            Dumbu.siteUrl = "<?php echo site_url(); ?>";
            Dumbu.baseUrl = "<?php echo base_url(); ?>";
            Dumbu.accounts = <?php echo json_encode($accounts); ?>;
        </script>
    </body>
</html>
