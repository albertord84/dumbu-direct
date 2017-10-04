<?php

defined('ENVIRONMENT') OR define('ENVIRONMENT', 'devel');

require __DIR__ . '/../etc/database';
require __DIR__ . '/../vendor/autoload.php';

$dbConfig = json_decode(json_encode($db['default']));

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $dbConfig->hostname,
    'database'  => $dbConfig->database,
    'username'  => $dbConfig->username,
    'password'  => $dbConfig->password,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

$messages = Capsule::table('message')->get();

echo "ID - Text".PHP_EOL;
foreach ($messages as $message) {
	printf("%s - %s\n",
		$message->id,
		$message->msg_text);
}
