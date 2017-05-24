<?php

if (file_exists(dirname(__FILE__).'/net_proxy')) {
  $netProxy = file_get_contents(dirname(__FILE__).'/net_proxy');
}

if (empty($netProxy) || trim($netProxy)=='') {
  $netProxy = false;
}

$_creds = file_get_contents(dirname(__FILE__).'/instagram_credentials');
$creds = explode(':', $_creds);

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';

$username = $creds[0];
$password = $creds[1];

$debug = false;
$truncatedDebug = false;
$captionText = '';

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
  if ($netProxy) {
    $ig->client->setProxy($netProxy);
  }
  $ig->setUser($username, $password);
  $ig->login();
} catch (\Exception $e) {
  echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
  exit(1);
}

try {
 	echo json_encode($ig->searchUsers('yordano'));
} catch (\Exception $e) {
  echo 'Something went wrong trying to get users list: '.$e->getMessage()."\n";
  exit(1);
}
