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
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/sweetalert.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/dumbu.css'); ?>?<?php echo d_guid(); ?>">
    </head>
    <body>
        <div id="search-container" class="container">
            <?php include __DIR__ . '/navbar.php'; ?>
            <div class="col-xs-12 col-sm-12 col-md-12" id="root">
            </div>
        </div>
        <script src="<?php echo base_url('js/lib/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/lodash.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/typeahead.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/handlebars.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/lib/core.min.js'); ?>"></script> <!-- required by sweetalert -->
		<script>var Dumbu = Dumbu || Object.assign({ siteUrl: "<?php echo site_url(); ?>" });</script>
		<script src="<?php echo base_url('js/lib/babel.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/react.production.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/lib/react-dom.production.min.js'); ?>"></script>

        <script type="text/babel">
            class SearchFollowers extends React.Component {
                constructor(props) {
                    super(props);
                    this.state = {
                        followers: [],
                        searchText: '',
                        sending: false,
                        followerIds: '',
                        followerNames: ''
                    };
                    window.searchFollowers = this;
                }
                removeProfile(follower) {
                    this.setState({
                        followers: _.filter(this.state.followers, (o) => {
                            return o.pk !== follower.pk;
                        })
                    });
                }
                submitData() {
                    var followerIds = _.map(this.state.followers, 'pk');
                    var followerNames = _.map(this.state.followers, 'username');
                    this.setState({
                        sending: true,
                        followerIds: followerIds.join(','),
                        followerNames: followerNames.join(',')
                    });
                    setTimeout(() => {
                        jQuery('#search-form').submit();
                    }, 2000);
                }
                render() {
                    return (
                        <div>
                            <div className="row">
                                <div id="logo" className="text-center">
                                    <h1>DUMBU</h1>
                                    <p>Get more real followers</p>
                                </div>
                                <form role="form" id="search-form" method="POST"
                                    action="<?php echo site_url('/compose/message'); ?>">
                                    <div className="form-group">
                                        <img className="async-loading hidden"
                                            src="<?php echo base_url('img/loading-small.gif'); ?>"/>
                                        <div className="input-group">
                                            <input id="ref-prof" className="form-control typeahead" type="text" name="search"
                                                placeholder="Select reference profiles..." autofocus="on"
                                                required disabled={this.state.sending}/>
                                            <span className="input-group-btn">
                                                <button className="btn btn-success text-them" type="button"
                                                        disabled={this.state.followers.length===0||this.state.sending}
                                                        onClick={() => this.submitData()}>
                                                    <i className="glyphicon glyphicon-pencil" aria-hidden="true"></i>Text' em
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="follower_ids" value={this.state.followerIds}/>
                                    <input type="hidden" name="follower_names" value={this.state.followerNames}/>
                                </form>
                            </div>
                            <div className="row selected-profs">
                                {
                                    this.state.followers.map((follower) => {
                                        return (
                                            <div className="panel panel-default">
                                                <div className="panel-heading text-center">
                                                    <button type="button" className="close remove-profile"
                                                        aria-label="Close" onClick={(event) => this.removeProfile(follower)}><span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <img className="card-img-top" alt="Profile photo"
                                                        src={follower.profile_pic_url} />
                                                </div>
                                                <div className="panel-body text-center">
                                                    <h4 className="">{follower.username}</h4>
                                                    <div className="text-muted">{follower.full_name}</div>
                                                </div>
                                                <div className="panel-footer text-center text-muted small">{follower.byline}</div>
                                            </div>
                                        );
                                    })
                                }
                            </div>
                        </div>
                    );
                }
            }
            setTimeout(() => {
                var datasource = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('username'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: Dumbu.siteUrl + '/search/followers/%QUERY',
                        wildcard: '%QUERY'
                    }
                });
                var asyncLoadElem = jQuery('.async-loading');
                var refProfElem = jQuery('#ref-prof');
                refProfElem.typeahead(null, {
                    name: 'profiles',
                    hint: true,
                    highlight: true,
                    display: 'username',
                    source: datasource,
                    minLength: 3
                });
                refProfElem.on({
                    'typeahead:selected': function (e, datum) {
                        var followers = searchFollowers.state.followers;
                        if (_.find(followers, { pk: datum.pk })!==undefined){
                            // already selected, do not go ahead
                            return;
                        }
                        followers.push(datum);
                        searchFollowers.setState({
                            followers: followers
                        });
                    },
                    'typeahead:asyncrequest': function (jq, query, dsName) {
                        asyncLoadElem.removeClass('hidden');
                    },
                    'typeahead:asyncreceive': function (jq, query, dsName) {
                        asyncLoadElem.addClass('hidden');
                    }
        		});
            }, 500);
            ReactDOM.render(<SearchFollowers/>, document.getElementById('root'));
		</script>
    </body>
</html>
