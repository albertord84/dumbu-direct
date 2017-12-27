<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';
$debug = true;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $username = $argv[1];
    $password = $argv[2];
    $proxy = $argv[3];
    $ig->setUser($username, $password);
    $ig->setProxy($proxy);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
