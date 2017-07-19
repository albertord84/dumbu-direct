<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';
$debug = true;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $username = 'dumbu.08';
    $password = 'Sorvete69';
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
