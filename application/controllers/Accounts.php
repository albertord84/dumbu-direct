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

    private function exists($username) {
        $this->load->database();
        $this->db->where('username', $username);
        $users = $this->db->get('client')->result();
        if (count($users)>0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    private function add() {
        $this->load->database();
        return var_dump($this->input->input_stream());
        $data = json_decode(json_encode($this->input->input_stream()));
        if ($this->exists($data->userName)) {
            return $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'The user is already created'
                ], JSON_PRETTY_PRINT));
        }
        $this->db->insert('client', [
            'username' => $data->userName,
            'pk' => $data->pk,
            'password' => $data->password,
            'priv' => $data->priv,
            'created_at' => $data->created_at
        ]);
        $sql = "select id, username, pk, priv, created_at ".
            "from client where username = ?";
        $accounts = $this->db->query($sql, [
            'username' => $data->username
        ])->result();
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => FALSE,
                'newAccount' => $accounts[0]
            ], JSON_PRETTY_PRINT));
    }

    private function remove($id) {
        $this->load->database();
        $this->db->delete('client', [
            'id' => $id
        ]);
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => TRUE,
            ], JSON_PRETTY_PRINT));
    }

    public function rest($data = '') {
        if ($this->session->is_admin === NULL) {
            return show_error("Access not allowed", 500);
        }
        switch ($this->input->method()) {
            case 'get':
                # code...
                break;

            case 'post':
                return $this->add();

            case 'put':
                # code...
                break;

            case 'delete':
                return $this->remove($data);

            default:
                # code...
                break;
        }
    }

    public function search($query) {
        $this->load->database();
        $username = $this->session->username;
        $users = $this->db->where('username', $username)->get('client')->result();
        $user = $users[0];

        $instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        $instagram->setUser($user->username, $user->password);

        try {
            $instagram->login();
        } catch (\Exception $ex) {
            return $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => $e->getMessage()
                ]));
        }

        try {
            $response = $instagram->searchUsers($query)->users;
        } catch (Exception $e) {
            return $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => $e->getMessage()
                ]));
        }

        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

}
