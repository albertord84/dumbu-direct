<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['test'] = 'test';

$route['auth'] = 'login/auth';
$route['logout'] = 'login/logout';

$route['search'] = 'search';
$route['users/(:any)'] = 'search/users/$1';

$route['compose'] = 'compose';
$route['user/dashboard'] = 'compose/createtask';

$route['scheduler'] = 'scheduler';
$route['scheduler/special'] = 'scheduler/special';

$route['promo'] = 'promo';
$route['promo/browse'] = 'promo/browse';
