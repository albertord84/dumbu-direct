<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DUMBU ::: Inbox</title>
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
            <div id="logo" class="text-center inbox-header">
                <h1>DUMBU</h1>
                <p class="text-muted small"><b>See your progress here...</b></p>
            </div>
            <div id="root" class="inbox"></div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/moment.js'); ?>"></script>
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
                messagesError: null,
                cursor: null,
                hasMore: true,
                searching: true,
                campaigns: [],
                moreCampaigns: false,
                campaignStatus: null
            };
            var ProgressBar = createReactClass({
                render: function() {
                    return createElement('div', { className: "load-bar" },
                        createElement('div', { className: "bar" }),
                        createElement('div', { className: "bar" }),
                        createElement('div', { className: "bar" }),
                    );
                }
            });
            var Message = createReactClass({
                render: function() {
                    var message = this.props.message;
                    var textNull = message.text === null;
                    var text = textNull ? '[empty message]' :
                        (message.text.length > 119 ?
                            message.text.replace(/\.\.\./g, '').substring(0, 120) +
                            '...' :
                        message.text);
                    return createElement('li', { className: 'list-group-item thread' },
                        createElement('h4', { className: 'text-muted bold sender' },
                            message.username
                        ),
                        createElement('span', { className: 'badge small datetime' },
                            moment(message.timestamp*1000).fromNow()
                        ),
                        createElement('p', { className: 'small' }, text)
                    );
                }
            });
            var Campaign = createReactClass({
                render: function() {
                    var campaign = this.props.campaign;
                    return createElement('li', { className: 'list-group-item' },
                        campaign.msg_text,
                        createElement('span', { className: 'badge' },
                            createElement('small', null,
                                'Status: ' + campaign.status
                            )
                        )
                    );
                }
            });
            var MoreMsgButton = createReactClass({
                render: function() {
                    return createElement('div', {
                        className: 'text-center col-xs-12 btn-more' },
                        createElement('button', {
                            className: 'btn btn-primary btn-xs',
                            onClick: this.props.loadMoreMessages,
                            disabled: this.props.searching }, 'More...'
                        )
                    )
                }
            })
            var RefreshMsgListButton = createReactClass({
                render: function() {
                    return createElement('a', {
                        className: 'btn btn-default btn-xs btn-refresh',
                        onClick: this.props.refreshMessageList,
                        title: 'Refresh the message list' },
                        createElement('span', {
                            className: 'glyphicon glyphicon-refresh'
                        })
                    );
                }
            });
            var TabPane = createReactClass({
                render: function() {
                    return createElement('div', null,
                        createElement('ul', { className: "nav nav-tabs small" },
                            createElement('li', { className: "active" },
                                createElement('a', { href: "#inbox",
                                'data-toggle': "tab" },
                                'Inbox')
                            ),
                            createElement('li', null,
                                createElement('a', { href: "#directs",
                                'data-toggle': "tab" },
                                    'Campaigns')
                            )
                        ),
                        createElement('div', { className: "tab-content" },
                            createElement('div', {
                                id: "inbox", className: "tab-pane active" },
                                this.props.messageList()),
                            createElement('div', {
                                id: "directs", className: "tab-pane" },
                                this.props.campaignList())
                        )
                    );
                }
            });
            var ErrorMsg = createReactClass({
                render: function() {
                    return createElement('div', { className: 'alert alert-danger small' },
                        this.props.message
                    );
                }
            });
            var UserInbox = createReactClass({
                loadMessages: function(cursor, hasMore) {
                    var self = this;
                    self.setState({ searching: true, messagesError: null });
                    $.post(Dumbu.siteUrl + '/messages', {
                        cursor: cursor,
                        hasMore: hasMore
                    }, function(data, textStatus, jqXHR) {
                        setTimeout(function() {
                            self.setState({
                                messages:   data.messages,
                                cursor:     data.cursor,
                                hasMore:    data.hasMore,
                                searching:  false
                            });
                        }, 700);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        log(jqXHR);
                        self.setState({
                            messages:   [],
                            searching:  false,
                            hasMore: false,
                            messagesError: 'Error trying to access your inbox: ' + errorThrown
                        });
                    });
                },
                loadMoreMessages: function() {
                    var self = this;
                    self.setState({ searching: true, messagesError: null });
                    $.post(Dumbu.siteUrl + '/messages', {
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
                refreshMessageList: function() {
                    this.loadMessages(null, true);
                },
                getMessageList: function() {
                    var state = this.state;
                    var messages = state.messages;
                    return createElement('div', null,
                        state.messagesError !== null ? createElement(ErrorMsg, {
                            message: state.messagesError
                        }) : '',
                        createElement('ul', { className: 'list-group' },
                            messages.map(function(message){
                                if (message !== null) {
                                    return createElement(Message, { message: message });
                                }
                            })
                        ),
                        state.hasMore ? createElement(MoreMsgButton, {
                            loadMoreMessages: this.loadMoreMessages,
                            searching: state.searching }, 'More...') : ''
                    );
                },
                getCampaignList: function() {
                    var state = this.state;
                    var messages = state.messages;
                    return createElement('ul', { className: 'list-group' },
                        state.campaigns.map(function(campaign){
                            return createElement(Campaign, { campaign: campaign });
                        })
                    );
                },
                getInitialState: function() {
                    return initialState;
                },
                componentDidMount: function() {
                    self = this;
                    setTimeout(function(){
                        self.loadMessages(self.state.cursor, self.state.hasMore);
                        self.loadCampaigns();
                    }, 500);
                },
                loadCampaigns: function() {
                    var self = this;
                    self.setState({ searching: true });
                    $.post(Dumbu.siteUrl + '/campaigns', {
                    }, function(data, textStatus, jqXHR) {
                        setTimeout(function() {
                            self.setState({ campaigns:  data.campaigns });
                        }, 700);
                    });
                },
                render: function() {
                    var state = this.state;
                    return createElement('div', null,
                        state.searching ? createElement(ProgressBar) : '',
                        state.searching ? '' : createElement(RefreshMsgListButton, {refreshMessageList: this.refreshMessageList }),
                        createElement(TabPane, {
                            messageList: this.getMessageList,
                            campaignList: this.getCampaignList
                        })
                    );
                }
            });
            setTimeout(function(){
                ReactDOM.render(e(UserInbox), document.getElementById('root'));
            }, 500);
        </script>
    </body>
</html>
