<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';
$debug = true;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $username = $argv[1];
    $password = $argv[2];
    //$ig->setUser($username, $password);
    $ig->login($username, $password);
} catch (\Exception $e) {
    printf("Something went wrong trying to login: MESSAGE: \"%s\"\n",
            $e->getMessage());
    exit(0);
}
