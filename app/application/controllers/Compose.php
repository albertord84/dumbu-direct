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
    $pks = !isset($_POST['pks']) ? NULL : $_POST['pks'];
    if ($pks == NULL) {
      $this->load->view('login_form', array('session' => $this->session->userdata()));
      return;
    }
    $this->session->set_userdata('pks', $pks);
    $user_id = $this->session->userdata('user_id');
    $data['session'] = $this->session->userdata();
    $this->load->view('compose', $data);
  }

}
