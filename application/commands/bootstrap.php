<?php

set_time_limit(0);

// Esto debe apuntar a la raiz del proyecto
define('ROOT_DIR', __DIR__ . '/../..');

// Requerido para reusar el codigo de CodeIgniter
define('BASEPATH', ROOT_DIR . '/system');

// Reusando las constantes de CodeIgniter
require ROOT_DIR . '/application/config/constants.php';

require ROOT_DIR . '/vendor/autoload.php';

// Esto se require porque la misma configuracion de acceso
// a datos se comparte con CodeIgniter que hace uso de esta
// constante
defined('ENVIRONMENT') OR define('ENVIRONMENT', 'devel');

// Configuracion de acceso a la base de datos compartida
// con CodeIgniter
require ROOT_DIR . '/etc/database';

// $db proviene del archivo ROOT_DIR . '/etc/database'
// que define el acceso a la base de datos compartida
// con CodeIgniter. Aqui lo convierto en objeto para
// hacer mas bonito su uso. Un objeto gusta mas que un
// arreglo.
$dbConfig = json_decode(json_encode($db['default']));

// Proviene de 'constants.php'
date_default_timezone_set(TIME_ZONE);

include_once __DIR__ . '/model/Schema.php';
include_once __DIR__ . '/Command.php';
include_once __DIR__ . '/MessageQueue.php';
include_once __DIR__ . '/PromotionQueue.php';

include_once __DIR__ . '/Start.php';
