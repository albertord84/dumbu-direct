<?php

$ref_prof = $argv[1];

$output_dir = '/tmp';

$control_pk = "3670825632"; // Mi perfil para ir sabiendo cada 50 msg si todo va bien

$dumbu08 = "4492293740"; // dumbu.08
$dumbu09 = '4542814483'; // dumbu.09

$ref_profs = array(
    'dumbu08' => $dumbu08,
    'dumbu09' => $dumbu09
);


$input_file = __DIR__ . "/../tmp/$ref_prof.followers.txt";

$message = "Win thousands of followers per month, boost your profile!" . 
        PHP_EOL . PHP_EOL . 
        "50% discount the first month (use the promotional code INSTA50P)" . 
        PHP_EOL . PHP_EOL . 
        "This promotion is valid only this week!" . 
        PHP_EOL . PHP_EOL . 
        "Go to http://instadumbu.com";

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
        $data['uid'] = $ref_profs[ $ref_prof ];
        $data['pks'][] = trim($line);
        $data['message'] = $message;
        $output_file_name = sprintf("%s_%s_%s.json", $fn, $datetime, $ref_profs[ $ref_prof ]);
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
            $data['uid'] = $ref_profs[ $ref_prof ];
            $data['pks'][] = $control_pk;
            $data['message'] = sprintf("Mensaje de control. Enviados hasta aqui cerca de %s.", $c);
            $output_file_name = sprintf("%s_%s_%s_chk.json", $fn, $datetime, $ref_profs[ $ref_prof ]);
            file_put_contents($output_dir . '/' . $output_file_name, json_encode($data) . PHP_EOL);
            echo sprintf("Creado mensaje %s/%s" . PHP_EOL, $output_dir, $output_file_name);
            $datetime++;
        }
    }
    fclose($handle);
} else {
    // error opening the file...
} 

