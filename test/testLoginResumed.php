<?php

$ts = date('U');

$sessions_path = '/home/yordano/Projects/dumbu-direct/vendor/mgp25/instagram-php/sessions';
$username = 'yordanoweb';

echo $ts;

$cmd1 = sprintf('sed -i \'s/"last_login":"[0-9]*"/"last_login":"%s"/\' %s/%s/%s-settings.dat',
  $ts, $sessions_path, $username, $username);

$cmd2 = sprintf('sed -i \'s/"last_experiments":"[0-9]*"/"last_experiments":"%s"/\' %s/%s/%s-settings.dat',
  date('U'), $sessions_path, $username, $username);

$output =  shell_exec($cmd1);
$output .= shell_exec($cmd2);
$output .= shell_exec(sprintf("cat %s/%s/%s-settings.dat",
  $sessions_path, $username, $username));

echo $output . PHP_EOL;
