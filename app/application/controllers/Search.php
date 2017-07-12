<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $data['session'] = $this->session->userdata();

        if ($user_id == NULL) {
            $this->load->view('login_form', $data);
            return;
        }

        $this->load->view('search_form', $data);
    }

    /**
     * Typeahead espera JSON con comillas no con comillas simples...
     */
    public function users($query) {

        // Para el acceso a la API de Instagram
        set_time_limit(0);
        date_default_timezone_set('UTC');
        require __DIR__ . '/../../../vendor/autoload.php';

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

        $this->loadIgCreds();

        if ($this->useProxy()) {
            $ig->client->setProxy($this->netProxy);
        }

        $ig->setUser($this->igCreds['username'], $this->igCreds['password']);
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

    // Posibles metodos de la API a usar aqui:
    //
  // Instagram::getAutoCompleteUserList
    // Instagram::getSuggestedUsers
    // Instagram::getUserFollowers
    // Instagram::getUserFollowings
    // Instagram::searchFBUsers
    // Instagram::searchUsers
}
