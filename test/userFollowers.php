<?php
$username = trim($argv[1]);
$password = trim($argv[2]);
$recip = trim($argv[3]);
//$_creds = file_get_contents(__DIR__ . '/../app/application/config/instagram_credentials');
//$creds = explode(':', $_creds);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
//$username = trim($creds[0]);
//$password = trim($creds[1]);
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
    $pk = $ig->getUsernameId($recip);
    $c = 0;
    
    $f = [];
    $resp = $ig->getUserFollowers($pk);
    $f = array_merge($f, $resp->getUsers());
    print_r($f);
    if (TRUE) exit();
    
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
    } while ($maxId !== null);
    echo ']';
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
