<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';
$debug = true;
$truncatedDebug = false;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $username = $argv[1];
    $password = $argv[2];
    $ig->setUser($username, $password);
    $ig->login();
    var_dump($ig->getUserInfoByName($argv[3])->user);
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
