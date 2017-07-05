<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    protected $igCreds = array(
        'username' => '',
        'password' => ''
    );
    
    protected $netProxy = '';

    protected function postVar($varName) {
        $postData = json_decode(file_get_contents('php://input'), true);
        return isset($postData[$varName]) ? $postData[$varName] : NULL;
    }

    protected function useProxy() {

        $netProxyFile = dirname(__FILE__) . '/../config/net_proxy';

        if (file_exists($netProxyFile)) {
            $_netProxy = file_get_contents($netProxyFile);
            if (empty($_netProxy) || trim($_netProxy) == '') {
                return false;
            } else {
                $this->netProxy = $_netProxy;
                return true;
            }
        }

        return false;
    }

    protected function loadIgCreds() {

        try {
            $igCredsFile = dirname(__FILE__) . '/../config/instagram_credentials';

            if (!file_exists($igCredsFile)) {
                show_error('Access credentials file, was not found', 500);
                return;
            }

            $_creds = file_get_contents($igCredsFile);
            $creds = explode(':', $_creds);

            $this->igCreds['username'] = $creds[0];
            $this->igCreds['password'] = $creds[1];
        } catch (Exception $ex) {
            die('Error while getting Inst@g credentials: ' . $ex->getMessage());
        }
    }

    protected function getLocalDate() {
        $date_cmd = `date "+%F %R"`;
        $date = trim($date_cmd);
        return str_replace(' ', '_', str_replace(':', '', str_replace('-', '', $date)));
    }

}
