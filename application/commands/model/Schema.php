<?php

use Illuminate\Database\Capsule\Manager as Schema;

$schema = new Schema;

$schema->addConnection([
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
$schema->setAsGlobal();

// Setup the Eloquent ORM
$schema->bootEloquent();
