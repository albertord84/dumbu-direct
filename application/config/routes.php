<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'user/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['user/login'] = 'user/login';
$route['user/auth'] = 'user/auth';
$route['logout'] = 'user/logout';

$route['search/followers'] = 'search/index';
$route['search/followers/(:any)'] = 'search/followers/$1';
$route['search'] = 'search/index';

$route['send/direct'] = 'direct/index';
$route['direct/inbox'] = 'direct/inbox';
$route['direct/messages'] = 'direct/messages';

$route['compose/message'] = 'compose';
$route['compose'] = 'compose';
$route['send/message'] = 'compose/send';

$route['promo/search/(:any)/(:any)'] = 'promo/search/$1/$2';
$route['promo/(:any)/hours/(:any)'] = 'promo/hours/$1/$2';

$route['test'] = 'test';

////////////////////////////////////////////////////////////////////

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
