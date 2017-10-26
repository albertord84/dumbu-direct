<?php
$username = trim($argv[1]);
$password = trim($argv[2]);
$profile = trim($argv[3]);
$dest_file = trim(@$argv[4]);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$debug = FALSE;
$truncatedDebug = TRUE;
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
    $pk = $ig->getUsernameId($profile);
    $c = 0;
    $f = [];
    $resp = $ig->getUserFollowers($pk);
    $followers = $resp->users;
    $maxId = $resp->next_max_id;
    printf("Cursor: %s\n", $maxId);
    $size = count($followers);
    for ($i = $c; $i < $size; $i++) {
        $follower = $followers[$i];
        printf("%s %s\n", $follower->pk, $follower->username);
    }
    $c = $size;
    sleep(5);
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
