<?php

defined('TIMEZONE')        OR define('TIMEZONE',        'America/Sao_Paulo');
defined('ROOT_DIR')        OR define('ROOT_DIR',        __DIR__);
defined('ETC_DIR')         OR define('ETC_DIR',         __DIR__ . '/etc');
defined('BASE_PATH')       OR define('BASE_PATH',       __DIR__ . '/system');
defined('APP_PATH')        OR define('APP_PATH',        __DIR__ . '/application');
defined('APP_LOG')         OR define('APP_LOGS',        __DIR__ . '/application/logs');
defined('MESSAGES_LOG')    OR define('MESSAGES_LOG',    __DIR__ . '/var/messages.log');
defined('QUEUE_PATH')      OR define('QUEUE_PATH',      __DIR__ . '/var/queue');
defined('OLD_QUEUE_PATH')  OR define('OLD_QUEUE_PATH',  __DIR__ . '/var/old');
defined('STATS_DIR')       OR define('STATS_DIR',       __DIR__ . '/var/stats');
defined('FOLLOWERS_DIR')   OR define('FOLLOWERS_DIR',   __DIR__ . '/var/followers');
defined('TASKS_DIR')       OR define('TASKS_DIR',       __DIR__ . '/var/tasks');
