<?php

// Asegurarme de que no se interrumpa la conexion.
set_time_limit(0);

// Esto debe apuntar a la raiz del proyecto. De esto dependen
// muchas cosas.
define('ROOT_DIR', __DIR__ . '/../..');

// Constantes requeridas para reusar codigo de CodeIgniter.
define('BASEPATH', ROOT_DIR . '/system');
defined('ENVIRONMENT') OR define('ENVIRONMENT', 'devel');

// Reusando constantes de CodeIgniter
require ROOT_DIR . '/application/config/constants.php';

// Para acceso a las bibliotecas en vendor.
require ROOT_DIR . '/vendor/autoload.php';

// Configuracion de acceso a la base de datos compartida
// con CodeIgniter
require ROOT_DIR . '/etc/database';

// Convertir a objeto el arreglo que contiene los
// datos de la conexion a la base de datos.
$dbConfig = json_decode(json_encode($db['default']));

// Proviene de 'constants.php'
date_default_timezone_set(TIME_ZONE);

include_once __DIR__ . '/model/Schema.php';
include_once __DIR__ . '/Command.php';
include_once __DIR__ . '/MessageQueue.php';
include_once __DIR__ . '/PromotionQueue.php';

include_once __DIR__ . '/Start.php';
