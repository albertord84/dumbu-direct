<?php
set_time_limit(0);
require __DIR__ . '/../vendor/autoload.php';

$username = $argv[1];
$password = $argv[2];
$debug = false;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

function show_threads($threads) {
  for ($i = 0; $i < count($threads); $i++) {
    $item = $threads[ $i ]->items[0];
    $inviter = $threads[ $i ]->inviter->username;
    $pk = $threads[ $i ]->inviter->pk;
    $timestamp = date('Y-m-d H:i:s', $item->timestamp / 1000000);
    $text = $item->text;
    $recips = [];
    foreach ($threads[ $i ]->users as $recip) {
          $recips[] = $recip->pk;
    }
    echo sprintf("%s ==> %s - \"%s(%s)\" escribio: \"%s...\" a [%s]" . PHP_EOL,
      intval($i) + 1, $timestamp, $inviter, $pk, substr($text, 0, 20),
      implode(',', $recips));
  }
}
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";
    exit(0);
}
try {
    $cursor = null;
    for ($i = 0; $i < 5; $i++) {
      $inbox = $ig->getV2Inbox($cursor)->inbox;
      $cursor = $inbox->oldest_cursor;
      $threads = $inbox->threads;
      show_threads($threads);
      sleep(mt_rand(3,8));
    }
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
