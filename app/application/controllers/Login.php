<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	public function index()
	{
    $user_id = $this->session->userdata('user_id');
		$data['session'] = $this->session->userdata();

		if ($user_id != NULL) {
			$this->load->view('search_form', $data);
			return;
		}

		$this->load->view('login_form', $data);
	}

	private function verify_instagram($user, $pass, &$account_id)
	{
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

		$ig->setUser($user, $pass);

		$response = $ig->login();
		$account_id = $ig->account_id;

		// Cerrar la sesion en Instagram porque la API
		// la deja siempre abierta
		$ig->logout();

		return $response;
	}

	public function auth()
	{
		$username = $this->postVar('username');
		$password = $this->postVar('password');

		if ($username == NULL || $password == NULL) {
			show_error('You have not provided the credentials (Username and Password)', 500);
			return;
		}

		$resp = NULL;
		$account_id = NULL;
    try {
      $resp = $this->verify_instagram($username, $password, $account_id);
			$this->session->set_userdata('user_id', $account_id);

			$r = array('status' => 'OK', 'response' => $resp,
			  'username' => $username, 'pk' => $account_id,
				'session' => $this->session->userdata()
			);
			echo json_encode($r);

			return;
    }
    catch (\Exception $ex) {
			$r = array('status' => 'SOME_ERR', 'response' => $resp);
			echo json_encode($r);
			return;
    }
	}

	public function logout() {
    $user_id = $this->session->userdata('user_id');

		if ($user_id != NULL) {
			$this->session->sess_destroy();
		}

		$data['session'] = $this->session->userdata();
		$this->load->view('login_form', $data);
	}

}
