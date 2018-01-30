<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$media_id = $argv[3]; // id del medio
$debug = true;
$truncatedDebug = false;

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    //$ig->setUser($username, $password);
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
    $comments = $ig->getMediaComments($media_id)->comments;
    foreach ($comments as $comment) {
        printf("%s: %s\n", $comment->pk, $comment->text);
    }
} catch (\Exception $e) {
    echo 'Something went wrong trying get comments'.$uid.': '.$e->getMessage()."\n";
}
