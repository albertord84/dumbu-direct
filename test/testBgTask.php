<?php

define('SCRIPTS_DIR', dirname(__FILE__) . '/../scripts/');
define('SCRIPT', 'test.sh');
define('SCRIPT_PARAMS', ' 1>/dev/null 2>/dev/null &');

shell_exec(SCRIPTS_DIR . SCRIPT . SCRIPT_PARAMS);
