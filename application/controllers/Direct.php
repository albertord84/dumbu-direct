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

  private function propsToCamelCase(array $messages) {
    return array_map(function($direct) {
      $mapped = new \stdClass();
      $mapped->id         = $direct->id;
      $mapped->userId     = $direct->user_id;
      $mapped->text       = $direct->msg_text;
      $mapped->sent       = $direct->sent;
      $mapped->processing = $direct->processing;
      $mapped->promo      = $direct->promo;
      $mapped->sentAt     = $direct->sent_at;
      $mapped->failed     = $direct->failed;
      $mapped->backup     = $direct->backup;
      $mapped->hours      = $direct->hours;
      return $mapped;
    }, $messages);
  }

  public function list() {
    $params = $this->params();
    $messages = $this->db->get('message')->result();
    $mappedItems = $this->propsToCamelCase($messages);
    return $this->success(true, [
      'directs' => $mappedItems
    ]);
  }

  public function delete($id) {
    $this->db->where('id', $id);
    $this->db->delete('message');
    $this->db->where('msg_id', $id);
    $this->db->delete('stat');
    return $this->success(true);
  }

}