<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

  private function params() {
    $input = file_get_contents('php://input');
    return json_decode($input);
  }

  private function success($textOrBool, $more = []) {
    $data = array_merge($more, [
      'success' => $textOrBool,
    ]);
    return $this->output
      ->set_content_type('application/json')
      ->set_status_header(200)
      ->set_output(json_encode($data));
  }

  private function error($text, $more = []) {
    $data = array_merge($more, [
      'error' => $text,
    ]);
    return $this->output
      ->set_content_type('application/json')
      ->set_status_header(500)
      ->set_output(json_encode($data));
  }

  public function index() {
    echo 'Nothing to show...';
  }

  public function auth() {
    $params = $this->params();
    $this->db->where([
      'username' => $params->username,
      'password' => $params->password,
    ]);
    $records = $this->db->get('client')->result();
    if (count($records) === 1) {
      return $this->success(true);
    }
    else $this->error('Not allowed to access this time. Verify your username/password.');
  }

}