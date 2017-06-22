<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Compose extends CI_Controller {

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

  public function index()
  {
    // Get the selected profile ids
    $pks = !array_key_exists('pks', $_POST) ? NULL : $_POST['pks'];
    if ($pks == NULL) {
      show_error('You must select some profile references first...', 500);
      return;
    }
    // For now, store the selected pks in the session...
    // Later on, will be stored in the DB
    $this->load->library('session');
    $this->session->set_userdata('pks', $pks);
    // Save to an external file the session data
    // to be accesible from the cron task
    $s_id = $this->session->userdata('session_id');
    echo var_dump($this->session);
    $this->load->view('compose');
  }

}
