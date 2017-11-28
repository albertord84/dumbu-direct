<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

  public $instagram = NULL;

  public $permitted_ips = [ '127.0.0.1' ];

  public function index() {
    return $this->load->view('login_form', []);
  }

  public function login() {
    return $this->load->view('login_form', []);
  }

}
