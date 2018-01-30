<?php
$username = trim(@$argv[1]);
$password = trim(@$argv[2]);
$profile = trim(@$argv[3]);
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/../vendor/autoload.php';
$debug = FALSE;
$truncatedDebug = TRUE;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    //$ig->setUser($username, $password);
    $ig->login($username, $password);
} catch (\Exception $e) {
    printf("Something went wrong trying to login: %s\n",
        $e->getMessage());
    exit(0);
}
try {
    $userId = $ig->getUsernameId($profile);
    printf("%s id is: %s\n", $profile, $userId);
} catch (\Exception $e) {
    printf("Something went wrong trying to get %s id: %s\n",
        $profile, $e->getMessage());
    exit(0);
}
$maxId = "AQD5HOdVtIfxUFUWlnNHQ94Q8g0L-gVQnqrDUXOHyDaGziBCsERVa0mFx-MUyGMjMEavMxktAVOqcGJp7qVOG88FO9LXp3qpSj95iZIvIGQHtg";
$c = 0; $i = 0;
do {
    /*if ($c === 3) {
        exit(0);
    }*/
    // 
    try {
        $response = $ig->getUserFollowers($userId, $maxId);
    } catch (\Exception $e) {
        printf("Something went wrong trying to get %s followers: %s\n",
            $profile, $e->getMessage());
        exit(0);
    }
    try {
        $maxId = $response->getNextMaxId();
    } catch (\Exception $e) {
        printf("Something went wrong trying to get next followers page id: %s\n",
            $profile, $e->getMessage());
        exit(0);
    }
    printf("next maxId: %s\n", $maxId);
    try {
        $users = $response->getUsers();
    } catch (\Exception $e) {
        printf("Something went wrong trying to get followers list of %s: %s\n",
            $profile, $e->getMessage());
        exit(0);
    }
    foreach ($users as $user) {
        printf("%s\n",
            //++$i,
            $user->getPk()/*,
            $user->getUsername()*/);
    }
    $c++;
    sleep(10);
} while($maxId !== null);
