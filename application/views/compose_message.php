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
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css'); ?>">
    </head>
    <body>
        <div id="root" class="container">
            <p class="text-muted text-center">Loading...</p>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/typeahead.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/jquery.pulsate.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/handlebars.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/sweetalert.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react.production.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/create-react-class.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react-dom.production.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/app/dumbu.js'); ?>"></script>
		<script>
			var Dumbu = 'undefined' !== typeof Dumbu ? Object.assign(Dumbu, {
				siteUrl: "<?php echo site_url(); ?>",
				baseUrl: "<?php echo base_url(); ?>",
                profiles: "<?php echo $profiles; ?>",
                usernames: "<?php echo $usernames; ?>"
			}) : Dumbu;
		</script>
        <script type="text/javascript">
            var initialState = {
                profiles: [].concat(Dumbu.profiles.split(',')),
                usernames: [].concat(Dumbu.usernames.split(',')),
                message: '',
                canSend: false
            };
            var Header = createReactClass({
                render: function() {
                    return e('div', { id:"logo", className: "text-center" },
                        e('h1', null, 'DUMBU'),
                        e('p', null,
                            e('a', { href: Dumbu.siteUrl + '/search' },
                                e('i', { className: "fa fa-angle-double-left" })
                            ),
                            '  Appeal the attention of selected profile followers...'
                        )
                    );
                }
            })
            var ComposeMessage = createReactClass({
                getInitialState: function() {
                    return initialState;
                },
                render: function() {
                    return e('div', null,
                        e(Header)
                    );
                }
            })
            setTimeout(function(){
                ReactDOM.render(e(ComposeMessage), document.getElementById('root'));
            }, 500)
        </script>
    </body>
</html>
