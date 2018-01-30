<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../backend/vendor/autoload.php';

$username = $argv[1];
$password = $argv[2];
$dest = $argv[3];
$msg = $argv[4];

$debug = true;
$truncatedDebug = true;

$captionText = '';

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->login($username, $password);
    $uid = $ig->people->getUserIdForName($dest);
    echo "Got $dest = $uid" . PHP_EOL . PHP_EOL;
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
    $ig->direct->sendText([ 'users' => [$uid] ], $msg);
} catch (\Exception $e) {
    echo 'Something went wrong trying to text to '.$dest.': '.$e->getMessage()."\n";
    exit(0);
}
