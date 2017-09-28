<?php

$_d = __DIR__;
defined('TIMEZONE')      OR define('TIMEZONE',      'America/Sao_Paulo');
defined('ROOT_DIR')      OR define('ROOT_DIR',      $_d);
defined('ETC_DIR')       OR define('ETC_DIR',       $_d . '/etc');
defined('BASE_PATH')     OR define('BASE_PATH',     $_d . '/system');
defined('MESSAGES_LOG')  OR define('MESSAGES_LOG',  $_d . '/var/messages.log');
defined('SESSIONS_DIR')  OR define('SESSIONS_DIR',  $_d . '/vendor/mgp25/instagram-php/sessions');
