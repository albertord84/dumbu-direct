<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compose extends MY_Controller {

    public function index() {
        // Get the selected profile ids and names
        $pks = !isset($_POST['pks']) ? NULL : $_POST['pks'];
        $names = !isset($_POST['names']) ? NULL : $_POST['names'];
        if ($pks == NULL) {
            $this->load->view('login_form', [
                'session' => $this->session->userdata()
            ]);
            return;
        }
        $this->session->pks = explode(',', $pks);
        $this->session->followers = explode(',', $names);
        $user_id = $this->session->pk;
        $data['session'] = $this->session->userdata();
        $data['session']['password'] = d_guid();
        $this->load->view('compose', $data);
    }

}
