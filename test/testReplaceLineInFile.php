<?php

$m = date('U');

$data = file($argv[1]);
$DELETE = $argv[2];

$fp = fopen("./$m.out", "w+");
flock($fp, LOCK_EX);

foreach($data as $line) {
	if(strstr($line, $DELETE)===FALSE) {
		fwrite($fp, $line);
	}
}
flock($fp, LOCK_UN);
fclose($fp);

rename("./$m.out", $argv[1]);
