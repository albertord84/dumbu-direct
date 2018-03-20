<?php
require __DIR__.'/../vendor/autoload.php';
$php = '/usr/bin/php';

$ig = new InstagramAPI\Instagram(true, false);
$ig->login('yordanoweb', 'blahblah');
$uid = $ig->people->getUserIdForName('dumbu.08');
echo $uid . PHP_EOL;
if(true)exit();

$username = $argv[1];
$password = $argv[2];

echo "Trying to log in $username with pass '*******'" . PHP_EOL;

// - Try login...
$cmd = sprintf("%s %s/login.php %s \"%s\"",
	$php, __DIR__, $username, $password);
$loginOutput = shell_exec($cmd);

$data = utf8_decode($loginOutput);

$loginSuccessRegex = '/\{"logged_in_user":.*"status": "ok"\}/';
preg_match($loginSuccessRegex, $data, $matches, PREG_OFFSET_CAPTURE, 0);
if (count($matches)===1) {
	echo "Successfully logged in $username" . PHP_EOL;
	exit(0);
}

preg_match('/\{"num_results":.*"status": "ok"\}/', $data, $matches, PREG_OFFSET_CAPTURE, 0);
if (count($matches)===1) {
	echo "Successfully logged in $username" . PHP_EOL;
	exit(0);
}

if (strstr($data, 'checkpoint_required')!==FALSE) {
	try {
		// - If challenge required, get challenge URL
		$challengeRegex = '/"checkpoint_url": "(.*)", "lock".*/';
		preg_match($challengeRegex, $data, $matches, PREG_OFFSET_CAPTURE, 0);
		$challengeUrl = $matches[1][0];
		echo "Challenging at '$challengeUrl'" . PHP_EOL;
		sleep(mt_rand(5, 10));
		$jar = new \GuzzleHttp\Cookie\CookieJar;
		$client = new \GuzzleHttp\Client(['cookies' => true]);
		echo "Getting the page '$challengeUrl'" . PHP_EOL;
		$response = $client->get($challengeUrl, [
			'cookies' => $jar
		]);
		echo $response->getBody()->read($response->getBody()->getSize()) . PHP_EOL;
		$cookies = json_decode(json_encode($jar->toArray()));
		// - Get the so much famous csrftoken...
		$csrftoken = NULL;
		foreach ($cookies as $cookie) {
			if ($cookie->Name === 'csrftoken') {
				$csrftoken = $cookie->Value;
				echo "Detected csrftoken: $csrftoken" . PHP_EOL;
				break;
			}
		}
		// - Wait for receiving the confirmation code via email
		sleep(mt_rand(5, 10));
		echo "Requesting confirmation code '$challengeUrl'" . PHP_EOL;
		$response = $client->post($challengeUrl, [
			'form_params' => [
				'choice' => 1
			],
			'headers' => [
				'Host' => 'i.instagram.com',
				'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0',
				'Accept' => '*/*',
				'Accept-Language' => 'es,en-US;q=0.7,en;q=0.3',
				'Referer' => $challengeUrl,
				'X-CSRFToken' => $csrftoken,
				'X-Instagram-AJAX' => '1',
				'Content-Type' => 'application/x-www-form-urlencoded',
				'X-Requested-With' => 'XMLHttpRequest',
			],
			'cookies' => $jar
		]);
		echo $response->getBody()->read($response->getBody()->getSize()) . PHP_EOL;
		// If confirmation code does not arrives, request again to this URL
		// https://www.instagram.com/challenge/replay/instagram_user_id_here/same_random_chars_data/
		//////// ================== //////////
		echo "Enter confirmation code: ";
		$verificationCode = trim(fgets(STDIN));
		// - Sending the confirmation code...
		echo "Sending the verification code: $verificationCode" . PHP_EOL;
		sleep(mt_rand(5, 10));
		$response = $client->post($challengeUrl, [
			'form_params' => [
				'security_code' => $verificationCode
			],
			'headers' => [
				'Host' => 'i.instagram.com',
				'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0',
				'Accept' => '*/*',
				'Accept-Language' => 'es,en-US;q=0.7,en;q=0.3',
				'Referer' => $challengeUrl,
				'X-CSRFToken' => $csrftoken,
				'X-Instagram-AJAX' => '1',
				'Content-Type' => 'application/x-www-form-urlencoded',
				'X-Requested-With' => 'XMLHttpRequest',
			],
			'cookies' => $jar
		]);
		printf("status: %s, reason: %s\n",
			$response->getStatusCode(),
			$response->getReasonPhrase());
	} catch (\Exception $e) {
		echo $e->getMessage() . PHP_EOL;
	}
} else {
	echo "Something else happened:" . PHP_EOL . PHP_EOL . $data;
}
