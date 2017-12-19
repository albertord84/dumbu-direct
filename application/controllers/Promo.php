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
    
    private function access_not_allowed() {
        $this->output->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode([
                'success' => FALSE,
                'message' => 'Access not allowed for your privileges level'
            ], JSON_PRETTY_PRINT));
    }

    public function browse() {
        if ($this->session->is_admin) {
            $this->load->view('browse_promo', [
                'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
                'username' => $this->session->username
            ]);
        }
        else {
            return $this->access_not_allowed();
        }
    }

    public function active($page = 0) {
        $words = $this->input->get('words');
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('sent', 0);
            $this->db->where('failed', 0);
            if ($words != NULL) {
                foreach (explode(' ', $words) as $word) {
                    $this->db->like('msg_text', $word);
                }
            }
            $this->db->or_where('sent', 2);
            if ($page == 0) {
                $this->db->limit(5);
            }
            else if ($page > 0) {
                $this->db->limit($page * 5, 5);
            }
            $count_sql = "select count(*) as messages from message "
                    . "where (sent=0 or sent=2) and failed=0";
            $count = current($this->db->query($count_sql)->result())->messages;
            $promos = $this->db->get('message')->result();
            foreach ($promos as $promo) {
                // Excluir el campo 'password' para que no vaya en JSON para
                // el navegador
                $senders_sql = 'select id, username, pk from client where id = ?';
                $q = $this->db->query($senders_sql, [ $promo->user_id ])
                    ->result();
                $promo->sender = $q[0];
            }
            return $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'promos' => $promos,
                    'count' => $count
                ], JSON_PRETTY_PRINT));
        }
        else {
            return $this->access_not_allowed();
        }
    }
    
    public function sent() {
        if ($this->session->is_admin) {
            $this->load->database();
			$this->db->where('sent', 1);
			$this->db->where('promo', 1);
            $this->db->limit(5);
            $promos = $this->db->get('message')->result();
            foreach ($promos as $promo) {
                $q = $this->db->query('select id, username, pk from client where id = ?', [ $promo->user_id ])
                        ->result();
                $promo->sender = $q[0];
            }
            return $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($promos, JSON_PRETTY_PRINT));
        }
        else {
            return $this->access_not_allowed();
        }
    }
    
    public function failed($page = 0) {
		$words = $this->input->get('words');
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('failed', 1);
			if ($words != NULL) {
				foreach (explode(' ', $words) as $word) {
					$this->db->like('msg_text', $word);
				}
			}
			if ($page == 0) {
				$this->db->limit(5);
			}
			else if ($page > 0) {
				$this->db->limit($page * 5, 5);
			}
			$count_sql = "select count(*) as messages from message "
				. "where failed=0";
			$count = current($this->db->query($count_sql)->result())->messages;
            $promos = $this->db->get('message')->result();
            foreach ($promos as $promo) {
                $q = $this->db->query('select id, username, pk from client where id = ?', [ $promo->user_id ])
                        ->result();
                $promo->sender = $q[0];
            }
			return $this->output->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'promos' => $promos,
					'count' => $count
				], JSON_PRETTY_PRINT));
        }
        else {
            return $this->access_not_allowed();
        }
    }
    
    public function sender($username) {
        if ($this->session->is_admin) {
            $this->load->database();
            $query = $this->db->query("select id, username, pk, created_at, priv " . 
                "from client where username like '%$username%' limit 10");
            $senders = $query->result();
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($senders, JSON_PRETTY_PRINT));
            return;
        }
        else {
            $this->access_not_allowed();
        }
    }
    
    public function add() {
        if ($this->session->is_admin) {
            $this->load->database();
            $data = [
                'user_id' => $this->input->post('sender_id'),
                'backup' => $this->input->post('backup_id'),
                'msg_text' => $this->input->post('promo'),
                'hours' => $this->input->post('hours'),
                'sent' => 2,
                'promo' => 1,
                'processing' => 0,
                'failed' => 0,
                'sent_at' => 0
            ];
            $this->db->insert('message', $data);
            redirect('/promo/browse', 'location');
            return;
        }
        else {
            show_error('You have not enough privileges to access here...', 500);
        }
    }
    
    public function change_sender($msg_id, $user_id) {
        if ($this->session->is_admin) {
            $this->load->database();
            $this->db->where('id', $msg_id);
            $this->db->update('message', [ 'user_id' => $user_id ]);
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['success'=>TRUE], JSON_PRETTY_PRINT));
            return;
        }
        else {
            show_error('You have not enough privileges to access here...', 500);
        }
    }
    
    private function delete($msg_id)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->delete('message');
        $this->db->where('msg_id', $msg_id);
        $this->db->delete('stat');
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(['success'=>TRUE], JSON_PRETTY_PRINT));
    }

	private function get($msg_id)
	{
		$this->load->database();
		$this->db->where('id', $msg_id);
		$promos = $this->db->get('message')->result();
		return $this->output->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'success'=>TRUE,
				'promo' => $promos[0]
			], JSON_PRETTY_PRINT));
	}

    public function rest($msg_id)
    {
        if ($this->session->is_admin) {
            $method = $this->input->method();
            if ($method == 'post') {
                echo 'Not implemented yet...';
            }
            else if ($method == 'get') {
            	return $this->get($msg_id);
            }
            else if ($method == 'delete') {
                return $this->delete($msg_id);
            }
        }
        else {
            show_error('You have not enough privileges to access here...', 500);
        }
    }
    
    public function status()
    {
        if (!$this->session->is_admin) {
            $this->access_not_allowed();
            return;
        }
        if(!$this->input->get('log')){
            $this->load->view('delivery_log', [
                'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
                'username' => $this->session->username
            ]);
            return;
        }
        date_default_timezone_set(TIME_ZONE);
        $log = ROOT_DIR . '/var/messages.log';
        $log_age = filemtime($log);
        $ten_secs_ago = intval(date('U')) - 10;
        if ( $log_age <> $ten_secs_ago ) {
            $lines = trim(shell_exec("tail -n 30 " . $log));
            $resp = explode(PHP_EOL, $lines);
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $resp
                ], JSON_PRETTY_PRINT));
            return;
        }
        else {
            $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => []
                ], JSON_PRETTY_PRINT));
            return;
        }
    }
    
    public function empty_data_response($msg = 'Nothing was found') {
        return $this->output->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode([
                'success' => FALSE,
                'message' => $msg
            ], JSON_PRETTY_PRINT));
    }

    public function error_response($msg = 'Something went wrong') {
        return $this->output->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode([
                'success' => FALSE,
                'message' => $msg
            ], JSON_PRETTY_PRINT));
    }
    
    public function collectFollowers($pk = 3670825632)
    {
        set_time_limit(0);
        if($this->input->method()!=='post' || $this->session->is_admin == NULL) {
            $this->access_not_allowed();
            return;
        }
        $this->load->database();
        $users = $this->db->where('pk', $pk)->get('client')->result();
        if (count($users)===0) { return $this->empty_data_response(); }
        $user = $users[0];
        // For debugging purposes only...
        /*$user = json_decode(json_encode([
            'username' => 'yordanoweb',
            'password' => '****************'
        ]));*/
        $instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        $instagram->setUser($user->username, $user->password);
        try {
            $instagram->login();
        }
        catch (Exception $ex) {
            return $this->error_response(sprintf("Error login \"%s\" to Instagram: %s",
                    $user->username, $ex->getMessage()));
        }
        $followers_file = sprintf("%s/var/followers/%s.txt", ROOT_DIR, $pk);
        if (file_exists($followers_file)) { unlink($followers_file); }
        $maxId = null; $c = 0; $followers = [];
        try {
            $resp = $instagram->getUserFollowers($pk);
            do {
                $resp = $instagram->getUserFollowers($pk, $maxId);
                $followers = array_merge($followers, $resp->getUsers());
                $maxId = $resp->getNextMaxId();
                $size = count($followers);
                for ($i = $c; $i < $size; $i++) {
                    $follower = $followers[$i]->pk;
                    shell_exec("echo $follower >> $followers_file");
                }
                $c = $size;
                sleep(5);
            } while ($maxId !== null);
        }
        catch (Exception $ex) {
            return $this->error_response(sprintf("Error getting followers of \"%s\": %s",
                    $pk, $ex->getMessage()));
        }
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => TRUE,
            ], JSON_PRETTY_PRINT));
    }
    
    public function enqueue($msg_id) {
        date_default_timezone_set(TIME_ZONE);
        if($this->input->method()!=='put' || $this->session->is_admin == NULL) {
            return $this->access_not_allowed();
        }
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->update('message', [
            'sent' => 2,
            'failed' => 0,
            'processing' => 0,
            'sent_at' => date('U')
        ]);
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => TRUE,
            ], JSON_PRETTY_PRINT));
    }

	public function start($msg_id) {
		date_default_timezone_set(TIME_ZONE);
		if($this->input->method()!=='put' || $this->session->is_admin == NULL) {
			return $this->access_not_allowed();
		}
		$this->load->database();
		$this->db->where('id', $msg_id);
		$this->db->update('message', [
			'sent' => 0,
			'failed' => 0,
			'processing' => 0,
			'sent_at' => date('U')
		]);
		$promos = $this->db->where('id', $msg_id)->get('message')->result();
		$promo = $promos[0];
		$senders = $this->db->query('select id, username, pk from client where id = ?', [
			$promo->user_id
		])->result();
		$promo->sender = $senders[0];
		return $this->output->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'success' => TRUE,
				'promo' => $promo
			], JSON_PRETTY_PRINT));
	}

	public function pause($msg_id) {
		date_default_timezone_set(TIME_ZONE);
		if($this->input->method()!=='put' || $this->session->is_admin == NULL) {
			return $this->access_not_allowed();
		}
		$this->load->database();
		$this->db->where('id', $msg_id);
		$this->db->update('message', [
			'sent' => 2,
			'failed' => 0,
			'processing' => 0,
			'sent_at' => date('U')
		]);
		$promos = $this->db->where('id', $msg_id)->get('message')->result();
		$promo = $promos[0];
		$senders = $this->db->query('select id, username, pk from client where id = ?', [
			$promo->user_id
		])->result();
		$promo->sender = $senders[0];
		return $this->output->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'success' => TRUE,
				'promo' => $promo
			], JSON_PRETTY_PRINT));
	}

	private function changeText($msg_id, $text) {
		$this->load->database();
		$this->db->where('id', $msg_id);
		$this->db->update('message', [
			'msg_text' => $text
		]);
		return $this->output->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'success' => TRUE
			], JSON_PRETTY_PRINT));
	}

	private function getXhrParam($param)
	{
		$request = json_decode(file_get_contents("php://input"), TRUE);
		return $request[$param];
	}

	public function text($msg_id)
	{
		if ($this->session->is_admin) {
			$method = $this->input->method();
			if ($method == 'put') {
				return $this->changeText($msg_id, $this->getXhrParam('text'));
			}
			else if ($method == 'get') {
				echo 'Not implemented yet...';
			}
		}
		else {
			show_error('You have not enough privileges to access here...', 500);
		}
    }

    public function stats() {
        if ($this->session->is_admin) {
			$this->load->view('promo_stats', [
                'is_admin' => TRUE,
                'username' => $this->session->username
            ]);
		}
		else {
			show_error('You have not enough privileges to access here...', 500);
		}
    }
    
    public function todayPromoStats() {
        $this->load->database();
        $sql = "SELECT username as client, count(*) as sent ".
               "FROM stat join client on stat.user_id=client.id ".
               "JOIN message on stat.msg_id=message.id ".
               "WHERE message.promo=1 and ".
               sprintf("dt>=%s ",
                    \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d 00:00:00'))->timestamp).
               "group by msg_id limit 10";
        $results = $this->db->query($sql)->result();
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => TRUE,
                'results' => $results
            ], JSON_PRETTY_PRINT));
    }

    public function lastPromoStats() {
        $this->load->database();
        $sql = "SELECT username as client, count(*) as sent, message.sent_at as sent_date ".
               "FROM stat join client on stat.user_id=client.id ".
               "JOIN message on stat.msg_id=message.id ".
               "WHERE message.promo=1 ".
               "group by msg_id limit 10";
        $results = $this->db->query($sql)->result();
        foreach ($results as $key => $result) {
            $d = $results[ $key ]->sent_date;
            $results[ $key ]->sent_date = \Carbon\Carbon::createFromTimestamp($d)
                ->format('M\/d h:i A');
        }
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => TRUE,
                'results' => $results
            ], JSON_PRETTY_PRINT));
    }

    public function search($group, $text) {
		if ($this->session->is_admin) {
			$this->load->database();
			if ($group == 'active') {
				$this->db->where('sent', 0);
				$this->db->or_where('sent', 2);
			} else if ($group == 'sent') {
				$this->db->where('sent', 1);
			} else if ($group == 'failed') {
				$this->db->where('failed', 1);
			}
			$this->db->where('promo', 1);
			$this->db->like('msg_text', $text);
			$this->db->limit(5);
			$promos = $this->db->get('message')->result();
			foreach ($promos as $promo) {
				$q = $this->db->query('select id, username, pk from client where id = ?', [ $promo->user_id ])
					->result();
				$promo->sender = $q[0];
			}
			return $this->output->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'results' => $promos
				], JSON_PRETTY_PRINT));
		}
		else {
			return $this->access_not_allowed();
		}
	}
}
