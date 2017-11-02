<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("America/Havana");

$now = new \Carbon\Carbon();

echo $now->subHours(14)->timestamp;
echo PHP_EOL;

