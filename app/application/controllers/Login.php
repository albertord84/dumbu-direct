<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$this->load->view('login_form');
	}

	public function auth() {
		$username = !array_key_exists('username', $_POST) ? NULL : $_POST['username'];
		$password = !array_key_exists('password', $_POST) ? NULL : $_POST['password'];

		// Para el acceso a la API de Instagram
    set_time_limit(0);
    date_default_timezone_set('UTC');
    require __DIR__.'/../../../vendor/autoload.php';

		$debug = false;
		$truncatedDebug = false;
		$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
		if ($this->useProxy()) {
      $ig->client->setProxy($this->netProxy);
    }

    $ig->setUser($username, $password);
    try {
      $resp = $ig->login();
			if ($resp->isOk()) {
				$this->load->library('session');
				$this->session->set_userdata('user_id', $ig->account_id);
				$r = array('status' => 'OK');
				echo json_encode($r);
				return;
			}
			else {
				$r = array('status' => 'BAD_CREDS');
				echo json_encode($r);
				return;
			}
    }
    catch (\Exception $ex) {
      die('Error while logging to Inst@g: ' . $ex->getMessage());
    }
	}

	private function useProxy() {
		$netProxyFile = dirname(__FILE__) . '/../config/net_proxy';
		if (file_exists($netProxyFile)) {
			$_netProxy = file_get_contents($netProxyFile);
			if (empty($_netProxy) || trim($_netProxy)=='') {
				return false;
			}
			else {
				$this->netProxy = $_netProxy;
				return true;
			}
		}
		return false;
	}

}
