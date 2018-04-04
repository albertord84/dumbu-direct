<?php
set_time_limit(0);
require __DIR__ . '/../vendor/autoload.php';

$username = $argv[1];
$password = $argv[2];
$debug = false;
$truncatedDebug = true;

$GLOBALS['ig'] = new \InstagramAPI\Instagram($debug, $truncatedDebug);
$ig = $GLOBALS['ig'];

try {
    $ig->login($username, $password, false, 21600);
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";
    exit(0);
}
try {
    $has_older = 1;
    $cursor = null;
    while ($has_older) {
      $inbox = $ig->direct->getInbox($cursor)->inbox;
      $threads = $inbox->threads;
      $cursor = $inbox->oldest_cursor;
      $has_older = $inbox->has_older;
      printf("%s\n", $cursor);
      array_map(function($thread){
        $ig = $GLOBALS['ig'];
        if (array_key_exists(0, $thread->users)) {
          if(true){
            print_r($thread);
            die();
          }
          printf("\n\n*****************************\n");
          printf("Email: %s\n",
            $ig->people->getInfoByName($thread->users[0]->username)
               ->user->getPublicEmail());
          printf("*****************************\n%s: %s\n",
            $thread->users[0]->username,
            $thread->items[0]->text);
        }
      }, $threads);
      sleep(mt_rand(3,8));
    }
    printf("TERMINADO...\n");
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
