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

    public function browse() {
        $this->load->view('browse_promo', [
            'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
            'username' => $this->session->username
        ]);
    }

    public function active() {
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('sent', 0);
            $this->db->or_where('sent', 2);
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
            $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Access not allowed for your privileges level'
                ], JSON_PRETTY_PRINT));
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
            $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Access not allowed for your privileges level'
                ], JSON_PRETTY_PRINT));
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
            $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Access not allowed for your privileges level'
                ], JSON_PRETTY_PRINT));
        }
    }
    
}
