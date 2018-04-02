<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Direct extends CI_Controller {

    private $instagram = NULL;

    private $permitted_ips = [ '127.0.0.1' ];

    private function is_logged($session) {
        return $session->username !== NULL;
    }

    private function user_id($pk) {
        $this->load->database();
        $this->db->where('pk', $pk);
        $user = current($this->db->get('client')->result());
        return $user->id;
    }

    private function direct_message($user_id, $message, $ref_profs) {
        $this->db->insert('directs', [
            'user_id' => $user_id,
            'msg_text' => $message
        ]);
        $last_direct_id = $this->db->insert_id();
        $profiles = explode(',', $ref_profs);
        
    }

    public function index() {
        if ($this->is_logged($this->session)) {
            $this->direct_message($this->user_id($this->session->pk),
                $this->input->post('message'),
                $this->input->post('profiles'));
            echo 'OK - direct message inserted';
        } else {
            $this->load->view('login_form');
        }
    }

    
}
