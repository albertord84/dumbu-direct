<?php

$_creds = file_get_contents(__DIR__ . '/../app/application/config/instagram_credentials');
$creds = explode(':', $_creds);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$username = trim($creds[0]);
$password = trim($creds[1]);
$debug = true;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";
    exit(0);
}
try {
    $maxId = null;
    $followers = [];
    $pk = $ig->getUsernameId($username);
    do {
        $resp = $ig->getSelfUsersFollowing($pk, $maxId);
        $followers = array_merge($followers, $resp->getUsers());
        $maxId = $resp->getNextMaxId();
    } while ($maxId !== null);
    for ($i = 0; $i < count($followers); $i++) {
        $follower = $followers[$i];
        echo sprintf("%s ==> profile: %s / username: %s / private: %s" . PHP_EOL, 
                $i, $follower->pk, $follower->username, 
                $follower->is_private ? 'true' : 'false');
    }
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
