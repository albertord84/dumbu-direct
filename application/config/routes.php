<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['user/login'] = 'user/login';
$route['user/auth'] = 'user/auth';

$route['search/followers'] = 'search/index';
$route['search/followers/(:any)'] = 'search/followers/$1';

$route['test'] = 'test';

////////////////////////////////////////////////////////////////////

$route['auth'] = 'login/auth';
$route['logout'] = 'login/logout';
$route['logged'] = 'login/logged';
$route['is_admin'] = 'login/admin';

$route['search'] = 'search';
$route['users/(:any)'] = 'search/users/$1';

$route['compose'] = 'compose';
$route['user/dashboard'] = 'compose/createtask';
$route['send/message'] = 'compose/send';

$route['scheduler'] = 'scheduler';
$route['scheduler/special'] = 'scheduler/special';

$route['promo'] = 'promo';
$route['promo/browse'] = 'promo/browse';

$route['promo/new'] = 'promo/add';
$route['promo/active/(:any)'] = 'promo/active/$1';
$route['promo/sent'] = 'promo/sent';
$route['promo/failed'] = 'promo/failed';
$route['promo/sender/(:any)'] = 'promo/sender/$1';
$route['promo/(:num)/(:num)'] = 'promo/change_sender/$1/$2';

$route['promo/(:num)'] = 'promo/rest/$1';
$route['collect/followers/(:num)'] = 'promo/collectfollowers/$1';

$route['promo/enqueue/(:num)'] = 'promo/enqueue/$1';
$route['promo/start/(:num)'] = 'promo/start/$1';
$route['promo/pause/(:num)'] = 'promo/pause/$1';
$route['promo/text/(:num)'] = 'promo/text/$1';

$route['promo/stats'] = 'promo/stats';
$route['promo/today'] = 'promo/todaypromostats';
$route['promo/last'] = 'promo/lastpromostats';

$route['accounts'] = 'accounts/index';
$route['accounts/(:any)'] = 'accounts/search/$1';
$route['account/(:any)'] = 'accounts/rest/$1';
$route['account'] = 'accounts/rest';
