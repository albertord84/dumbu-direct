<?php
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/../vendor/autoload.php';
$username = $argv[1];
$password = $argv[2];
$media_id = $argv[3]; // id del medio a comentar
$msg = NULL; // $argv[4];
$debug = true;
$truncatedDebug = false;

// 
// https://api.instagram.com/oembed/?url=https://www.instagram.com/p/BWiczYKFKbt/?taken-by=dumbu.08
// 
// El parametro url = URL de la barra de direcciones del navegador
// cuando se hace clic sobre la imagen en Instagram. Esto entonces
// devolvera un muy buen JSON donde hay una propiedad que se
// llama "media_id".
// 

function guid() {
    $one = mt_rand(0, 65535);
    $two = mt_rand(0, 65535);
    $three = mt_rand(0, 65535);
    $four = mt_rand(0, 65535);
    $five = mt_rand(0, 65535);
    return strtolower(sprintf('%04X%04X%04X%04X%04X', $one, $two, $three, $four, $five));
}

$message = "Ganhe milhares de seguidores qualificados por área " .
        "de interesse ou geolocalização, turbine seu perfil!" .
        PHP_EOL . 
        "- 50% desconto o primeiro mes (use o código promocional INSTA50P)" .
        PHP_EOL . 
        "- 15 dias de teste gratis (use o código promocional INSTA15D)" .
        PHP_EOL . 
        "Esta promoção é valida apenas essa semana! " .
        "Acesse www.dumbu.pro. " .
        "Tem dúvidas se a nossa ferramenta funciona?! Esta mensagem " .
        "foi enviada por www.dumbu.pro.";

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    printf('Something went wrong trying to login: %s\n', $e->getMessage());
    exit(0);
}
try {
    $ig->comment($media_id, $msg == NULL ? $message : $msg);
} catch (\Exception $e) {
    printf('Something went wrong trying to text to %s: %s\n',
            $media_id, $e->getMessage());
}
