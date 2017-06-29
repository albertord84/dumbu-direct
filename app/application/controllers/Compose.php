<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Compose extends MY_Controller {

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
