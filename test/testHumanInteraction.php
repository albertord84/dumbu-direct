<?php

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$debug = true;
$truncatedDebug = false;

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";
    exit(0);
}
try {
    $action = mt_rand(0, 2);
    if ($action === 0) {
        printf('Commenting...\n');
        $media_id = $ig->getPopularFeed()->items[0]->id;
        $ig->comment($media_id, 'Eu gosto!');
        sleep(5);
        return;
    }
    if ($action === 1) {
        printf('Liking...\n');
        $media_id = $ig->getPopularFeed()->items[0]->id;
        $ig->like($media_id);
        sleep(5);
        return;
    }
    if ($action === 2) {
        printf('Browsing comments...\n');
        $media_id = $ig->getPopularFeed()->items[0]->id;
        $ig->getMediaComments($media_id);
        sleep(5);
        return;
    }
} catch (\Exception $e) {
    echo 'Something went wrong trying get comments' . $uid . ': ' . $e->getMessage() . "\n";
}
