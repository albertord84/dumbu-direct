<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$recip = $argv[3]; // nombre del perfil, no el id del perfil
$msg = $argv[4];
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
    $ig->directMessage($recip, $msg);
} catch (\Exception $e) {
    echo 'Something went wrong trying to text to '.$recip.': '.$e->getMessage()."\n";
}
