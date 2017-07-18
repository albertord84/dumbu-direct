<?php

$_creds = file_get_contents(__DIR__ . '/../app/application/config/instagram_credentials');
$creds = explode(':', $_creds);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$username = $creds[0];
$password = $creds[1];
$recip = 'dumbu.08';
$photoFileName = dirname(__FILE__) . '/assets/img/photo.jpg';
$debug = false;
$truncatedDebug = true;
$captionText = '';
$ig = new \InstagramAPI\Instagram(true, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";
    exit(0);
}
try {
    $pk = $ig->getUsernameId($recip);
    $resp = $ig->getUserFollowers($pk);
    $users = $resp->users;
    for ($i = 0; $i < count($users); $i++) {
        $user = $users[$i];
        echo sprintf("%s ==> profile: %s / username: %s / private: %s" . PHP_EOL, 
                $i, $user->pk, $user->username, 
                $user->is_private ? 'true' : 'false');
    }
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
