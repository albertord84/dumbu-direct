<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("America/Havana");

$now = new \Carbon\Carbon();
$later = \Carbon\Carbon::createFromTimestamp(($now->timestamp - 7200));

echo $now->toTimeString() . PHP_EOL;
echo $later->toTimeString() . PHP_EOL;
echo abs($now->diffInHours($later, false)) . PHP_EOL;

echo PHP_EOL;

