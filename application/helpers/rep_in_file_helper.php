<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('rep_in_file')) {

	function rep_in_file($input_file, $data2replace) {
		$m = date('U');

		$data = file($input_file);
		$DELETE = $data2replace;

		$fp = fopen("./$m.out", "w+");
		flock($fp, LOCK_EX);

		foreach($data as $line) {
			if(strstr($line, $DELETE)===FALSE) {
				fwrite($fp, $line);
			}
		}
		flock($fp, LOCK_UN);
		fclose($fp);

		rename("./$m.out", $input_file);
	}

}
