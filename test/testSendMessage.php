<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$uid = $argv[3]; // id del perfil, no el nombre del usuario
$msg = NULL; // $argv[4];
$debug = true;
$truncatedDebug = true;
$captionText = '';

function guid() {
    $one = mt_rand(0, 65535);
    $two = mt_rand(0, 65535);
    $three = mt_rand(0, 65535);
    $four = mt_rand(0, 65535);
    $five = mt_rand(0, 65535);
    return strtolower(sprintf('%04X%04X%04X%04X%04X', $one, $two, $three, $four, $five));
}

/*$message = "%s ====== %s" .
        PHP_EOL . PHP_EOL .
        "Ganhe milhares de seguidores qualificados por área" .
        PHP_EOL . PHP_EOL .
        "de interesse ou geolocalização, turbine seu perfil!" .
        PHP_EOL . PHP_EOL .
        "============ %s ============" .
        PHP_EOL . PHP_EOL .
        "- 50%% desconto o primeiro mes (use o código promocional INSTA50P)" .
        PHP_EOL . PHP_EOL .
        "- 15 dias de teste gratis (use o código promocional INSTA15D)" .
        PHP_EOL . PHP_EOL .
        "============ %s ============" .
        PHP_EOL . PHP_EOL .
        "Esta promoção é valida apenas essa semana!" .
        PHP_EOL . PHP_EOL .
        "Acesse www.dumbu.pro." .
        PHP_EOL . PHP_EOL .
        "Tem dúvidas se a nossa ferramenta funciona?! Esta mensagem" .
        PHP_EOL . PHP_EOL .
        "foi enviada por www.dumbu.pro." .
        PHP_EOL . PHP_EOL .
        "============ %s ============";*/

$message = "Saudações para meus seguidores, tenha um ótimo dia!";

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: '.$e->getMessage()."\n";
    exit(0);
}
try {
    $ig->directMessage($uid, $msg == NULL ?
            sprintf($message, guid(), guid(), guid(), guid(), guid()) :
            $msg);
} catch (\Exception $e) {
    echo 'Something went wrong trying to text to '.$uid.': '.$e->getMessage()."\n";
}
