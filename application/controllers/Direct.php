<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Direct extends CI_Controller {

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

  public function list() {
    $params = $this->params();
    $messages = $this->db->get('message')->result();
    return $this->success(true, [
      'directs' => $messages
    ]);
  }

}