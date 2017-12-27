<?php
set_time_limit(0);

require __DIR__.'/../config.php';
require __DIR__.'/../vendor/autoload.php';

$debug = true;
$truncatedDebug = true;

// Ultima tarea generada desde la web por la interaccion del cliente
$taskFile = trim(shell_exec(sprintf("ls %s/*.json | tail -n 1", TASKS_DIR)));
$taskObj = json_decode(file_get_contents($taskFile));

// Id de la session con que se logueo en CodeIgniter
$session_id = $taskObj->ci_last_regenerate;

// Datos en crudo de la sesion CodeIgniter
$session_data = trim(shell_exec(sprintf("grep %s %s/ci_session* | tail -n 1", 
        $session_id, APP_LOGS)));

$passData = array();

// Extrayendo datos de la contraseña almacenada en la sesion
preg_match('/password\|s\:[0-9]*\:\"(\w+)\"/', $session_data, $passData);

// Asignando la contraseña ya extraida al objeto bonito
// que se usara con la API de Instagram
$taskObj->password = $passData[1];

var_dump($taskObj);
