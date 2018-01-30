<?php
$username = $argv[1];
$password = $argv[2];
require __DIR__.'/../vendor/autoload.php';
$debug = true;
$truncatedDebug = false;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
$ig->setVerifySSL(false);
//$ig->setProxy('http://70.39.250.32:33128');
//$ig->setUser($username, $password);
$ig->login($username, $password);
