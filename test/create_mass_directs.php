<?php

$output_dir = '/tmp';

$control_pk = "3670825632"; // Mi perfil para ir sabiendo cada 50 msg si todo va bien

$uid = "4492293740"; // dumbu.08
$input_file = __DIR__ . "/../insta_ids";

$message = "Ganhe milhares de seguidores por áreas de interesse, turbine seu perfil!" . 
        PHP_EOL . PHP_EOL . 
        "Ganhe 50% de desconto usando o código promocional BACKTODUMBU. " . 
        "Esta promoção é exclusiva e por tempo limitado, acesse: www.dumbu.pro";

// {"datetime":"20170710_150238","uid":"3670825632","pks":["4239955376"],"message":""}

$handle = fopen($input_file, "r");
if ($handle) {
    $c = 0;
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
        $c++;
        if ($c % 50 == 0) {
            $data = array(
                "datetime" => NULL,
                "uid" => NULL,
                "pks" => [],
                "message" => NULL
            );
            $data['datetime'] = sprintf("%s_%s", $fn, $datetime);
            $data['uid'] = $uid;
            $data['pks'][] = $control_pk;
            $data['message'] = sprintf("Mensaje de control. Enviados hasta aqui cerca de %s.", $c);
            $output_file_name = sprintf("%s_%s_%s_chk.json", $fn, $datetime, $uid);
            file_put_contents($output_dir . '/' . $output_file_name, json_encode($data) . PHP_EOL);
            echo sprintf("Creado mensaje %s/%s" . PHP_EOL, $output_dir, $output_file_name);
            $datetime++;
        }
    }
    fclose($handle);
} else {
    // error opening the file...
} 

