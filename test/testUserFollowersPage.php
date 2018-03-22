<?php
$username = trim(@$argv[1]);
$password = trim(@$argv[2]);
$profile = trim(@$argv[3]);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$debug = false;
$truncatedDebug = false;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
$ig->login($username, $password, false, 10800);
$userId = $ig->people->getUserIdForName($profile);
printf("%s id is: %s\n", $profile, $userId);
$maxId = null;
$c = 0; $i = 0;
$maxId = 'AQAzbtDkohaaia77Aj9BmKOeTM2nZh1swUcZP6l1NNgsG8GmCW8rxKxOE149qpfE8yIfdlkMwlsbwYOVTgY0Oka-08L0gIXxy-JfhhfDLG5IfA';
$response = $ig->people->getFollowers($userId, null, $maxId);
$rankToken = \InstagramAPI\Signatures::generateUUID();
$response = $ig->people->getFollowers($userId, null, $maxId);
printf("next maxId: %s\n", $maxId);
sleep(5);
$users = $response->getUsers();
foreach ($users as $user) {
    printf("%s - %s\n", ++$c, $user->getPk());
}
sleep(5);
printf('ended...\n');

