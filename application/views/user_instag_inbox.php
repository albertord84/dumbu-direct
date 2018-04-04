<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DUMBU ::: Instagram Inbox</title>
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
        <div class="container">
            <div class="text-center inbox-header">
                <h1>DUMBU</h1>
                <h4 class="text-muted">Instagram Inbox</h4>
                <p class="text-muted small"><b>You will see your progress here...</b></p>
            </div>
            <div id="root" class="inbox">
                <p class="text-muted text-center">Loading...</p>
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/rx.all.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react.production.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/create-react-class.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/react-dom.production.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/app/dumbu.js'); ?>"></script>
		<script>
			var Dumbu = 'undefined' !== typeof Dumbu ? Object.assign(Dumbu, {
				siteUrl: "<?php echo site_url(); ?>",
				baseUrl: "<?php echo base_url(); ?>"
			}) : Dumbu;
		</script>
        <script type="text/javascript">
            var initialState = {
                messages: [],
                cursor: null,
                hasMore: true,
                searching: false
            };
            var UserInbox = createReactClass({
                loadMessages: function(cursor, hasMore) {
                    var self = this;
                    self.setState({ searching: true });
                    $.post(Dumbu.siteUrl + '/direct/messages', {
                        cursor: cursor,
                        hasMore: hasMore
                    }, function(data, textStatus, jqXHR) {
                        self.setState({
                            messages:   data.messages,
                            cursor:     data.cursor,
                            hasMore:    data.hasMore,
                            searching:  false
                        });
                    });
                },
                loadMoreMessages: function() {
                    var self = this;
                    self.setState({ searching: true });
                    $.post(Dumbu.siteUrl + '/direct/messages', {
                        cursor: self.state.cursor,
                        hasMore: self.state.hasMore
                    }, function(data, textStatus, jqXHR) {
                        self.setState({
                            messages:   self.state.messages.concat(data.messages),
                            cursor:     data.cursor,
                            hasMore:    data.hasMore,
                            searching:  false
                        });
                    });
                },
                getMessageList: function() {
                    return this.state.messages.map(function(message){
                        if (message !== null) {
                            return React.createElement('div', {
                                    className: 'col-xs-10 col-xs-offset-1'
                                },
                                React.createElement('h4', { className: 'text-muted bold' },
                                    message.username),
                                message.text === null ? '' : React.createElement('p', {
                                    className: 'small' },
                                    message.text.length > 119 ?
                                        message.text.replace(/\.\.\./g, '').substring(0, 120)
                                        + '...' : message.text
                                ),
                                React.createElement('hr')
                            );
                        }
                    }, this);
                },
                getInitialState: function() {
                    return initialState;
                },
                componentDidMount: function() {
                    self = this;
                    setTimeout(function(){
                        self.loadMessages(self.state.cursor, self.state.hasMore);
                    }, 500);
                },
                render: function() {
                    var state = this.state;
                    return React.createElement('div', null,
                        state.searching ? React.createElement('p', {
                            className: 'text-center small bold'
                        }, 'Searching messages...') : '',
                        this.getMessageList(),
                        state.hasMore ? React.createElement('div', {
                            className: 'text-center col-xs-12' },
                            React.createElement('button', {
                                className: 'btn btn-primary btn-xs',
                                onClick: this.loadMoreMessages,
                                disabled: state.searching },
                                'More...'
                            )
                        ) : ''
                    );
                }
            });
            setTimeout(function(){
                ReactDOM.render(e(UserInbox), document.getElementById('root'));
            }, 500);
        </script>
    </body>
</html>
