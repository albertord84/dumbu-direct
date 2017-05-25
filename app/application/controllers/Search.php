<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

  private $igCreds = array(
    'username' => '', 
    'password' => ''
  );

  private $netProxy = '';

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

    $igCredsFile = dirname(__FILE__) . '/../config/instagram_credentials';

    if (!file_exists($igCredsFile)) {
      show_error('Access credentials file, was not found', 500);
      return;
    }

    $_creds = file_get_contents($igCredsFile);
    $creds = explode(':', $_creds);

    $this->igCreds['username'] = $creds[0];
    $this->igCreds['password'] = $creds[1];

  }

	public function index()
	{
		$this->load->view('search_form');
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
        "{ \"name\": \"John Doe\" }, { \"name\": \"Johnny Doe\" }, " . 
        "{ \"name\": \"Johnson Doe\" }, { \"name\": \"Johnna Doe\" }, " . 
        "{ \"name\": \"Johns Doe\" }, { \"name\": \"Johnky Doe\" } " . 
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
    $ig->login();

    echo "OK";
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
