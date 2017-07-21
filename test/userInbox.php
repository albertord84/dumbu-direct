<?php

$_creds = file_get_contents( __DIR__ . '/../app/application/config/instagram_credentials' );
$creds = explode(':', $_creds);
set_time_limit(0);
require __DIR__ . '/../vendor/autoload.php';
$username = trim($creds[0]);
$password = trim($creds[1]);
$debug = false;
$truncatedDebug = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->setUser($username, $password);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";
    exit(0);
}
try {
    $threads = $ig->getV2Inbox()->inbox->threads;
    for ($i = 0; $i < count($threads); $i++) {
        if ($i == 10) exit(0);
        $item = $threads[ $i ]->items[0];
        $inviter = $threads[ $i ]->inviter->username;
        $dest_profile = json_decode( json_encode( $threads[ $i ]->last_seen_at ), TRUE );
        $dest_user = NULL;
        foreach ($dest_profile as $key => $value) {
            $dest_user = $ig->getUserInfoById( $key )->user->username;
            break;
        }
        $timestamp = date('Y-m-d H:i:s', $item->timestamp / 1000000);
        $text = $item->text;
        echo sprintf("%s ==> %s - \"%s\" escribio a \"%s\": \"%s...\"" . PHP_EOL, 
                intval($i) + 1, $timestamp, $inviter, $dest_user, substr($text, 0, 20));
    }
} catch (\Exception $e) {
    echo 'Something went wrong trying to get recent activity: ' . $e->getMessage() . "\n";
}
