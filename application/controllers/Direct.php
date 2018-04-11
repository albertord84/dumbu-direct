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
            'msg_text' => $message,
            'hours' => 8,
            'status' => 0,
        ]);
        $last_direct_id = $this->db->insert_id();
        array_reduce(explode(',', $ref_profs), function($data, $profile) {
            $this->db->insert('direct_data', [
                'direct_id' => $data['direct_id'],
                'ref_prof' => $profile,
            ]);
            return $data;
        }, [ 'direct_id' => $last_direct_id ]);
    }

    private function user_data($pk) {
        $this->load->database();
        $this->db->where('pk', $pk);
        $user = current($this->db->get('client')->result());
        return $user;
    }

    public function inbox() {
        if ($this->is_logged($this->session)) {
            $this->load->view('user_instag_inbox');
        } else {
            $this->load->view('login_form');
        }
    }

    public function messages() {
        $cursor = $this->input->post('cursor');
        $hasMore = $this->input->post('hasMore');
        $pk = $this->session->pk;
        $user = $this->user_data($pk);
        $ig = new \InstagramAPI\Instagram(false, true);
        $ig->login($user->username, $user->password, false, 21600);
        $inbox = $ig->direct->getInbox($cursor)->inbox;
        $threads = $inbox->threads;
        $messages = array_map(function($thread){
            if (array_key_exists(0, $thread->users)) {
                return [
                    'username'  => $thread->users[0]->username,
                    'text'      => $thread->items[0]->text,
                    'timestamp' => substr($thread->items[0]->timestamp, 0, 10),
                ];
            }
        }, $threads);
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success'    => true,
                'messages'   => $messages,
                'cursor'     => $inbox->oldest_cursor,
                'hasMore'    => $inbox->has_older,
            ], JSON_PRETTY_PRINT));
    }

    public function index() {
        if ($this->is_logged($this->session)) {
            $user_id = $this->user_id($this->session->pk);
            $this->direct_message($user_id,
                $this->input->post('message'),
                $this->input->post('profiles'));
            redirect('/direct/inbox', 'location');
        } else {
            $this->load->view('login_form');
        }
    }

    
}
