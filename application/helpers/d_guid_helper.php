<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('d_guid')) {

    function d_guid() {
        return 'd_' . strtolower(sprintf('%04X%04X%04X%04X%04X', 
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151)));
    }

}
