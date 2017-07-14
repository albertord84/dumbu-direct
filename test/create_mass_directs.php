<?php

$output_dir = '/tmp';

$uid = "4492293740"; // dumbu.08
$input_file = "/home/yordano/Projects/dumbu-direct/insta_ids";

$message = "Ganhe milhares de seguidores, turbine seu negócio, "
        . "teste 7 dias de graça sem compromisso algum usando "
        . "o código promocional INSTA-DIRECT. Esta promoção é exclusiva "
        . "e por tempo limitado, acesse www.dumbu.pro";

// {"datetime":"20170710_150238","uid":"3670825632","pks":["4239955376"],"message":""}

$handle = fopen($input_file, "r");
if ($handle) {
    $datetime = date("U");
    while (($line = fgets($handle)) !== false) {
        $data = array(
            "datetime" => NULL,
            "uid" => NULL,
            "pks" => [],
            "message" => NULL
        );
        $fn = date("Ymd");
        $data['datetime'] = sprintf("%s_%s", $fn, $datetime);
        $data['uid'] = $uid;
        $data['pks'][] = trim($line);
        $data['message'] = $message;
        $output_file_name = sprintf("%s_%s_%s.json", $fn, $datetime, $uid);
        file_put_contents($output_dir . '/' . $output_file_name, json_encode($data) . PHP_EOL);
        echo sprintf("Creado mensaje %s/%s" . PHP_EOL, $output_dir, $output_file_name);
        $datetime++;
    }
    fclose($handle);
} else {
    // error opening the file...
} 

