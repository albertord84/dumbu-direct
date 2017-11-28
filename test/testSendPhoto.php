<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$destUser=$argv[3];
$photoFileName=$argv[4];
$debug = true;
$truncatedDebug = true;
$captionText = '';
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
    var_dump($ig->getUserInfoById(3670825632));
    //printf("Se enviará al perfil: %s\n", $pk);
    //$ig->directPhoto($pk, "$photoFileName", "Esto es probando para alternar saludos y fotos...");
} catch (\Exception $e) {
    echo 'Something went wrong trying to post photo to '.$destUser.': '.$e->getMessage()."\n";
}
