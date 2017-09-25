<?php
$username = trim($argv[1]);
$password = trim($argv[2]);
$uid = trim($argv[3]);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$debug = false;
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
    $pk = $ig->getUsernameId($uid);
    $c = 0;
    
    $f = [];
    $resp = $ig->getUserFollowers($pk);
    $f = array_merge($f, $resp->getUsers());
    echo '[';
    do {
        $resp = $ig->getUserFollowers($pk, $maxId);
        $followers = array_merge($followers, $resp->getUsers());
        $maxId = $resp->getNextMaxId();
        $size = count($followers);
        for ($i = $c; $i < $size; $i++) {
            $follower = $followers[$i];
            echo sprintf("{ \"num\": %s, \"profile\": \"%s\", \"username\": \"%s\", \"private\": %s }%s" . PHP_EOL, 
                    $i, $follower->pk, $follower->username, 
                    ($follower->is_private ? 'true' : 'false'), 
                    ($i < $size - 1 ? ',' : ''));
        }
        $c = $size;
        sleep(5);
    } while ($maxId !== null);
    echo ']';
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
