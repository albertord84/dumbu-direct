<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        if ($this->session->is_admin !== NULL) {
            $this->load->view('user_admin', [
                'username' => $this->session->username,
                'is_admin' => $this->session->is_admin != NULL,
                'accounts' => $this->last()
            ]);
        } else {
            show_error("Not allowed access", 500);
        }
    }

    private function last($count = 5, $json = FALSE) {
        $sql = "select id, username, pk, priv, created_at ".
               "from client limit ".$count;
        $sql_count = "select count(*) as count from client";
        $this->load->database();
        $data = $this->db->query($sql_count)->result();
        $accounts = [
            'accounts' => $this->db->query($sql)->result(),
            'count' => $data[0]->count
        ];
        if ($json) {
            return $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'success' => TRUE,
                    'accounts' => [
                        'accounts' => $accounts,
                        'count' => $count
                    ]
                ], JSON_PRETTY_PRINT));
        }
        else {
            return $accounts;
        }
    }

    public function rest($data) {
        switch ($this->input->method()) {
            case 'get':
                # code...
                break;

            case 'post':
                # code...
                break;

            case 'put':
                # code...
                break;

            case 'delete':
                # code...
                break;

            default:
                # code...
                break;
        }
    }

}
