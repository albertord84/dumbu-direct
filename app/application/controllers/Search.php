<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

  private $igCreds = array(
    'username' => '',
    'password' => ''
  );

  private $netProxy = '';

  public function index()
	{
    $user_id = $this->session->userdata('user_id');
		$data['session'] = $this->session->userdata();

		if ($user_id == NULL) {
			$this->load->view('login_form', $data);
			return;
		}

		$this->load->view('search_form', $data);
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

  private function loadIgCreds() {

    try {
      $igCredsFile = dirname(__FILE__) . '/../config/instagram_credentials';

      if (!file_exists($igCredsFile)) {
        show_error('Access credentials file, was not found', 500);
        return;
      }

      $_creds = file_get_contents($igCredsFile);
      $creds = explode(':', $_creds);

      $this->igCreds['username'] = $creds[0];
      $this->igCreds['password'] = $creds[1];

    } catch (Exception $ex) {
      die('Error while getting Inst@g credentials: ' . $ex->getMessage());
    }

  }

	/**
   * Typeahead espera JSON con comillas no con comillas simples...
   */
  public function users($query) {

    // Para el acceso a la API de Instagram
    set_time_limit(0);
    date_default_timezone_set('UTC');
    require __DIR__.'/../../../vendor/autoload.php';

    if ($query == 'johndoe') {
      echo "[ " .
        "{ \"username\": \"John Doe\" }, { \"username\": \"Johnny Doe\" }, " .
        "{ \"username\": \"Johnson Doe\" }, { \"username\": \"Johnna Doe\" }, " .
        "{ \"username\": \"Johns Doe\" }, { \"username\": \"Johnky Doe\" } " .
      " ]";
      return;
    }

    $debug = false;
    $truncatedDebug = false;
    $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

    $this->loadIgCreds();

    if ($this->useProxy()) {
      $ig->client->setProxy($this->netProxy);
    }

    $ig->setUser($this->igCreds['username'], $this->igCreds['password']);
    try {
      $ig->login();
    }
    catch (\Exception $ex) {
      die('Error while logging to Inst@g: ' . $ex->getMessage());
    }

    try {
      echo json_encode($ig->searchUsers($query)->users);
      return;
    } catch (Exception $e) {
      die('Error while getting profiles list: ' . $ex->getMessage());
    }
  }

  // Posibles metodos de la API a usar aqui:
  //
  // Instagram::getAutoCompleteUserList
  // Instagram::getSuggestedUsers
  // Instagram::getUserFollowers
  // Instagram::getUserFollowings
  // Instagram::searchFBUsers
  // Instagram::searchUsers

}
