<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Promo extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        $this->load->view('compose_promo', [
            'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
            'username' => $this->session->username
        ]);
    }
    
    private function access_not_allowed() {
        $this->output->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode([
                'success' => FALSE,
                'message' => 'Access not allowed for your privileges level'
            ], JSON_PRETTY_PRINT));
    }

    public function browse() {
        $this->load->view('browse_promo', [
            'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
            'username' => $this->session->username
        ]);
    }

    public function active($page = 0) {
        $words = $this->input->get('words');
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('sent', 0);
            $this->db->where('failed', 0);
            if ($words != NULL) {
                foreach (explode(' ', $words) as $word) {
                    $this->db->like('msg_text', $word);
                }
            }
            $this->db->or_where('sent', 2);
            if ($page == 0) {
                $this->db->limit(5);
            }
            else if ($page > 0) {
                $this->db->limit($page * 5, 5);
            }
            $count_sql = "select count(*) as messages from message where sent=0 or sent=2";
            $count = current($this->db->query($count_sql)->result())->messages;
            $promos = $this->db->get('message')->result();
            foreach ($promos as $promo) {
                // Excluir el campo 'password' para que no vaya en JSON para
                // el navegador
                $senders_sql = 'select id, username, pk from client where id = ?';
                $q = $this->db->query($senders_sql, [ $promo->user_id ])
                    ->result();
                $promo->sender = $q[0];
            }
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'promos' => $promos,
                    'count' => $count
                ], JSON_PRETTY_PRINT));
            return;
        }
        else {
            $this->access_not_allowed();
        }
    }
    
    public function sent() {
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('sent', 1);
            $this->db->limit(5);
            $promos = $this->db->get('message')->result();
            foreach ($promos as $promo) {
                $q = $this->db->query('select id, username, pk from client where id = ?', [ $promo->user_id ])
                        ->result();
                $promo->sender = $q[0];
            }
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($promos, JSON_PRETTY_PRINT));
            return;
        }
        else {
            $this->access_not_allowed();
        }
    }
    
    public function failed() {
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('failed', 1);
            $this->db->limit(5);
            $promos = $this->db->get('message')->result();
            foreach ($promos as $promo) {
                $q = $this->db->query('select id, username, pk from client where id = ?', [ $promo->user_id ])
                        ->result();
                $promo->sender = $q[0];
            }
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($promos, JSON_PRETTY_PRINT));
            return;
        }
        else {
            $this->access_not_allowed();
        }
    }
    
    public function sender($username) {
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->like('username', $username);
            $this->db->limit(10);
            $senders = $this->db->get('client')->result();
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($senders, JSON_PRETTY_PRINT));
            return;
        }
        else {
            $this->access_not_allowed();
        }
    }
    
    public function add() {
        if ($this->session->is_admin) {
            $this->load->database();
            $data = [
                'user_id' => $this->input->post('sender_id'),
                'msg_text' => $this->input->post('promo'),
                'sent' => 2,
                'promo' => 1,
                'processing' => 0,
                'failed' => 0,
                'sent_at' => 0
            ];
            $this->db->insert('message', $data);
            redirect('/promo/browse', 'location');
            return;
        }
        else {
            show_error('You have not enough privileges to access here...', 500);
        }
    }
    
    public function change_sender($msg_id, $user_id) {
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('id', $msg_id);
            $this->db->update('message', [ 'user_id' => $user_id ]);
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['success'=>TRUE], JSON_PRETTY_PRINT));
            return;
        }
        else {
            show_error('You have not enough privileges to access here...', 500);
        }
    }
    
    private function delete($msg_id)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->delete('message');
        $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(['success'=>TRUE], JSON_PRETTY_PRINT));
        return;
    }

    public function rest($msg_id)
    {
        if ($this->session->is_admin) {
            $method = $this->input->method();
            if ($method == 'post') {
                echo 'Not implemented yet...';
            }
            else if ($method == 'get') {
                echo 'Not implemented yet...';
            }
            else if ($method == 'delete') {
                $this->delete($msg_id);
            }
        }
        else {
            show_error('You have not enough privileges to access here...', 500);
        }
    }
}
