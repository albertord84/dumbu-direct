<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    private $instagram = NULL;

    public function __construct() {
        parent::__construct();
        
        // Para el acceso a la API de Instagram
        set_time_limit(0);
        date_default_timezone_set('UTC');
        require APPPATH . '/../vendor/autoload.php';
    }
    
    protected function loginInstagram($username, $password)
    {
        $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        $this->instagram->setUser($username, $password);
        $this->instagram->login();
    }

    protected function postVar($varName) {
        $postData = json_decode(file_get_contents('php://input'), true);
        return isset($postData[$varName]) ? $postData[$varName] : NULL;
    }

    protected function getLocalDate() {
        $date_cmd = `date "+%F %R:%S"`;
        $date = trim($date_cmd);
        return str_replace(' ', '_', str_replace(':', '', str_replace('-', '', $date)));
    }

}
