<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$this->load->library('session');
    $user_id = array_key_exists('user_id', $this->session->userdata());
		$data['session'] = $this->session;

		if ($user_id) {
			$this->load->view('search_form', $data);
			return;
		}

		$this->load->view('login_form', $data);
	}

	public function auth() {
		$this->load->library('session');
		$this->session->sess_destroy();

		$postData = json_decode(file_get_contents('php://input'), true);
		$username = !array_key_exists('username', $postData) ? NULL : $postData['username'];
		$password = !array_key_exists('password', $postData) ? NULL : $postData['password'];

		if ($username == NULL || $password == NULL) {
			show_error('You have not provided the credentials (Username and Password)', 500);
			return;
		}

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
		$resp = NULL;
    try {
      $resp = $ig->login();

			$this->load->library('session');
			$this->session->set_userdata('user_id', $ig->account_id);

			$r = array('status' => 'OK', 'response' => $resp,
			  'pk' => $ig->account_id, 'session' => $this->session);
			echo json_encode($r);

			// Cerrar aqui la sesion porque la API la deja siempre
			// abierta
			$ig->logout();

			return;
    }
    catch (\Exception $ex) {
			$r = array('status' => 'SOME_ERR', 'response' => $resp);
			echo json_encode($r);
			return;
    }
	}

	public function logout() {
		$this->load->library('session');
    $user_id = array_key_exists('user_id', $this->session->userdata());

		if ($user_id) {
			$this->session->sess_destroy();
		}

		$data['session'] = $this->session;
		$this->load->view('login_form', $data);
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
