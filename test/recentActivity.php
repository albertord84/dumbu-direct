<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$debug = true;
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
    $ig->getRecentActivity();
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: '.$e->getMessage()."\n";
}
