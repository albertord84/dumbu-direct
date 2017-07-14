<?php
$_creds = file_get_contents(dirname(__FILE__).'/instagram_credentials');
$creds = explode(':', $_creds);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $creds[0];
$password = $creds[1];
$recip='alberto_dreyes';
$photoFileName=dirname(__FILE__).'/assets/img/photo.jpg';
$debug = false;
$truncatedDebug = false;
$captionText = '';
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
	echo var_dump($ig->getRecentActivity());
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: '.$e->getMessage()."\n";
}
