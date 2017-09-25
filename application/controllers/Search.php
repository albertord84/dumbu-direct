<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {

    public function index() {
        $user_id = $this->session->pk;
        $data['session'] = $this->session->userdata();

        if ($user_id == NULL) {
            $this->load->view('login_form', $data);
            return;
        }

        $data['session']['password'] = d_guid();
        $this->load->view('search_form', $data);
    }

    /**
     * Typeahead espera JSON con comillas no con comillas simples...
     */
    public function users($query) {

        if ($query == 'johndoe') {
            echo "[ " .
            "{ \"username\": \"John Doe\" }, { \"username\": \"Johnny Doe\" }, " .
            "{ \"username\": \"Johnson Doe\" }, { \"username\": \"Johnna Doe\" }, " .
            "{ \"username\": \"Johns Doe\" }, { \"username\": \"Johnky Doe\" } " .
            " ]";
            return;
        }

        $debug = false;
        $truncatedDebug = false;
        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

        $ig->setUser($this->session->username, $this->session->password);
        try {
            $ig->login();
        } catch (\Exception $ex) {
            die('Error while logging to Inst@g: ' . $ex->getMessage());
        }

        try {
            echo json_encode($ig->searchUsers($query)->users);
            return;
        } catch (Exception $e) {
            die('Error while getting profiles list: ' . $ex->getMessage());
        }
    }

}
