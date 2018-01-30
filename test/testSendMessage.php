<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$uid = $argv[3];
$msg = $argv[4];
$debug = true;
$truncatedDebug = true;
$captionText = '';

function guid() {
    $one = mt_rand(0, 65535);
    $two = mt_rand(0, 65535);
    $three = mt_rand(0, 65535);
    $four = mt_rand(0, 65535);
    $five = mt_rand(0, 65535);
    return strtolower(sprintf('%04X%04X%04X%04X%04X', $one, $two, $three, $four, $five));
}

$message = "Saudações para meus seguidores, tenha um ótimo dia!";

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    //$ig->setUser($username, $password);
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
    $ig->direct->sendText([ 'users' => [$uid] ], $msg == NULL ?
            sprintf($message, guid(), guid(), guid(), guid(), guid()) :
            $msg);
} catch (\Exception $e) {
    echo 'Something went wrong trying to text to '.$uid.': '.$e->getMessage()."\n";
}
