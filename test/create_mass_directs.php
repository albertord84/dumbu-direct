<?php

/////////////////////////////////////////////////////
// Ahora todo es suplido desde la linea de comandos
/////////////////////////////////////////////////////

// Perfil remitente de los mensajes
$ref_prof = $argv[1];

// Direccion del archivo local que contiene, uno por linea,
// los ids de los perfiles a los que se enviara el mensaje
$dest_prof_list = $argv[2];

$output_dir = '/tmp';

$control_pk = "3670825632"; // Mi perfil para ir sabiendo cada 50 msg si todo va bien

$pbpetti = "236116119";
$pedropetti = "5787797919";
$dumbu08 = "4492293740"; // dumbu.08
$dumbu09 = '4542814483'; // dumbu.09

$ref_profs = array(
    'dumbu08' => $dumbu08,
    'dumbu09' => $dumbu09,
    'pedropetti' => $pedropetti,
    'pbpetti' => $pbpetti
);

$input_file = "$dest_prof_list";

function guid()
    {
        return strtolower( sprintf('%04X%04X%04X%04X%04X',
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(16384, 20479), mt_rand(32768, 49151)) );
    }

$message = "Ganhe milhares de seguidores qualificados por área".
    PHP_EOL . PHP_EOL .
    "de interesse ou geolocalização, turbine seu perfil!".
    PHP_EOL . PHP_EOL .
    "============ %s ============".
    PHP_EOL . PHP_EOL .
    "- 50% desconto o primeiro mes (use o código promocional INSTA50P)".
    PHP_EOL . PHP_EOL .
    "- 15 dias de teste gratis (use o código promocional INSTA15D)".
    PHP_EOL . PHP_EOL .
    "============ %s ============".
    PHP_EOL . PHP_EOL .
    "Esta promoção é valida apenas essa semana!".
    PHP_EOL . PHP_EOL .
    "Acesse www.dumbu.pro.".
    PHP_EOL . PHP_EOL .
    "Tem dúvidas se a nossa ferramenta funciona?! Esta mensagem".
    PHP_EOL . PHP_EOL .
    "foi enviada por www.dumbu.pro.".
    PHP_EOL . PHP_EOL .
    "============ %s ============";

// {"datetime":"20170710_150238","uid":"3670825632","pks":["4239955376"],"message":""}

$handle = fopen($input_file, "r");
if ($handle) {
    $c = 0;
    $datetime = date("U");
    while (($line = fgets($handle)) !== false) {
        $guid = 
        $data = array(
            "datetime" => NULL,
            "uid" => NULL,
            "pks" => [],
            "message" => NULL
        );
        $fn = date("Ymd");
        $data['datetime'] = sprintf("%s_%s", $fn, $datetime);
        $dest = trim($line);
        $data['uid'] = $ref_prof;
        $data['pks'][] = $dest;
        $data['message'] = sprintf($message, guid(), guid(), guid());
        $output_file_name = sprintf("%s_%s_%s_%s.json", 
            $fn, $datetime, $ref_prof, $dest);
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
            $data['uid'] = $ref_prof;
            $data['pks'][] = $control_pk;
            $data['message'] = sprintf("Mensaje de control. Enviados hasta aqui cerca de %s.", $c);
            $output_file_name = sprintf("%s_%s_%s_chk.json", $fn, $datetime, $ref_prof);
            file_put_contents($output_dir . '/' . $output_file_name, json_encode($data) . PHP_EOL);
            echo sprintf("Creado mensaje %s/%s" . PHP_EOL, $output_dir, $output_file_name);
            $datetime++;
        }
        if ($c === 2000) {
            break;
        }
    }
    fclose($handle);
} else {
    // error opening the file...
} 

