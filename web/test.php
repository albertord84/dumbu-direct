<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = 'yordanoweb';
$password = 'XPcom01.*';
$recip='alberto_dreyes';
$debug = true;
$truncatedDebug = false;
$captionText = '';
$ig = new \InstagramAPI\Instagram($debug, $trunactedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
	$uId = $ig->getUsernameId($recip);
    $ig->directMessage($uId, "Ayer no pude loguearme porque deje el movil en casa y me pedia chequear codigo enviado por SMS...");
} catch (\Exception $e) {
    echo 'Something went wrong trying to post to '.$recip.': '.$e->getMessage()."\n";
}
