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
    
    /**
     * Hay un problema con la API. Cada vez que se hace
     * un inicio de sesión, más bien lo que sucede es que
     * continúa con la última sesión abierta. De modo que
     * en ocasiones, aunque uno suministre mal la contraseña,
     * aun así, el cliente logra iniciar sesión. Con este
     * método se limpia la cascarilla que permitiría que se
     * inicie sesión con una contraseña incorrecta.
     */
    protected function clearOldCredentials($username)
    {
        $user_session_dir = sprintf("%s/%s", SESSIONS_DIR, $username);
        if (file_exists($user_session_dir)) {
            $cmd = sprintf("rm -r %s/%s", SESSIONS_DIR, $username);
            shell_exec($cmd);
        }
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

    protected function createClientAccount($username, $password, $pk)
    {
        $this->load->database();
        $this->db->where('pk', $pk);
        $query = $this->db->get('client');
        $exists = $query->num_rows() == 0 ? FALSE : TRUE;
        if (!$exists) {
            $data = [
                'username' => $username,
                'password' => $password,
                'pk' => $pk
            ];
            $this->db->insert('client', $data);
        }
    }

}
