<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$media_id = $argv[3]; // id del medio
$comment_id = $argv[4]; // id del comentario
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
    $ig->deleteComment($media_id, $comment_id);
} catch (\Exception $e) {
    echo 'Something went wrong trying delete media: '.$e->getMessage()."\n";
}
