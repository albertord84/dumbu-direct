<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$recip = $argv[3]; // id del perfil, no el nombre del usuario
$msg = $argv[4];
$debug = true;
$truncatedDebug = true;
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
    $uid = $ig->getUsernameId($recip);
    $ig->directMessage($uid, $msg);
} catch (\Exception $e) {
    echo 'Something went wrong trying to text to '.$recip.': '.$e->getMessage()."\n";
}
