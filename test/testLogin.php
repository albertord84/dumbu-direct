<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';
$debug = false;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $username = 'dumbu.09';
    $password = 'dumbu2017';
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
