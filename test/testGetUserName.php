<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$uid = $argv[3]; // id del perfil, no el nombre del usuario
$debug = true;
$truncatedDebug = true;

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
    $user = $ig->getUserInfoById($uid)->user->username;
    echo sprintf("Username for id %s is %s\n", $uid, $user);
} catch (\Exception $e) {
    echo 'Something went wrong trying to text to '.$uid.': '.$e->getMessage()."\n";
}
