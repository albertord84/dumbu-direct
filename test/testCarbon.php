<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("America/Havana");

printf("%s\n", \Carbon\Carbon::now()->format('d-M H:i:s'));
