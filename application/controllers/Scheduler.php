<?php

defined('BASEPATH') OR exit('No direct script access allowed');

define('SENT', '1');
define('NOT_SENT', '0');
define('FAILED', '1');
define('NOT_FAILED', '0');
define('PROCESSING', '1');
define('NOT_PROCESSING', '0');
define('PROCESSED', '0');
define('IS_PROMOTION', '1');
define('IS_MESSAGE', '0');
define('IS_NOT_PROMOTION', '0');
define('IS_WAITING', '2');

class Scheduler extends CI_Controller {

    public $instagram = NULL;

    public function index() {
        PHP_SAPI == 'cli' OR die('You may not access this way...');
        set_time_limit(0);
        printf("\n%s - Procesando mensajes...\n", $this->now());
        try {
            $this->startStoppedPromosBy12h();
            $this->messages();
            $this->promos();
        } catch (Exception $ex) {
            show_error('Error al procesar los mensajes: ' .
                    $ex->getMessage(), 500);
            return;
        }
        printf("%s - Terminado el procesamiento de mensajes.\n", $this->now());
        return;
    }

    public function cleanInstagramApiSession($username)
    {
        $dir = INSTAGRAM_SESSIONS . '/' . $username;
        if (file_exists($dir)) {
            shell_exec("rm -r " . $dir);
        }
    }

    public function setMessageProcessing($msg_id, $status)
    {
        $this->db->where('id', $msg_id);
        $this->db->update('message', [ 'processing' => $status ]);
        printf("- Se establecio el estado del mensaje a \"%s\"...\n",
                $status == PROCESSED ? 'PROCESADO' : 'PROCESANDO');
    }

    public function setMessageSent($msg_id, $sent = 0)
    {
        date_default_timezone_set(TIME_ZONE);
        $this->db->where('id', $msg_id);
        $this->db->update('message', [
            'sent' => SENT,
            'failed' => NOT_FAILED,
            'sent_at' => \Carbon\Carbon::now()->timestamp
        ]);
        printf("- Se establecio el estado del mensaje a \"%s\"...\n",
                $sent == NOT_SENT ? 'NO ENVIADO' : 'ENVIADO');
    }

    public function hasDefinedFollowers($pk)
    {
        $exists_followers_file = file_exists(FOLLOWERS_LIST_DIR . "/$pk.txt");
        return $exists_followers_file;
    }

    public function messageFollowers($user_id, $msg_id)
    {
        $stats = $this->db->where('user_id', $user_id)
            ->where('msg_id', $msg_id)->get('stat')->result();
        $ids = [];
        foreach ($stats as $stat) {
            $ids[] = $stat->follower_id;
        }
        printf("- Seguidores seleccionados [%s]\n", implode(',', $ids));
        return $ids;
    }

    public function updateMessageStat($user_id, $msg_id, $followers)
    {
        date_default_timezone_set(TIME_ZONE);
        foreach ($followers as $follower) {
            $this->db->where('msg_id', $msg_id)
                ->where('user_id', $user_id)
                ->where('follower_id', $follower)
                ->update('stat', [
                    'dt' => \Carbon\Carbon::now()->timestamp
                ]);
        }
    }

    public function messages()
    {
        set_time_limit(0);
        $this->load->database();
        printf("%s - PROCESANDO MENSAJES ENVIADOS DESDE LA WEB...\n", $this->now());
        if ($this->messagesLocked()) { $this->interrupt('- Esta bloqueada la cola de mensajes'); }
        $this->lockMessage();
        $messages = $this->lastMessages();
        if (count($messages) === 0) {
            printf("- No hay mensajes en cola por ahora\n");
            $this->unlockMessage();
            printf("%s - TERMINADO EL PROCESAMIENTO DE MENSAJES...\n", $this->now());
            return;
        }
        foreach ($messages as $msg) {
            try {
                $user = $this->getUser($msg->user_id);
                if ($this->dailyLimitPassed($user->id)) {
                    printf("* Procesado el mensaje %s...\n", $msg->id);
                    continue;
                }
                $followers = $this->messageFollowers($msg->user_id, $msg->id);
                $this->getInstagram();
                $this->loginInstagram($user->username, $user->password);
                $this->setMessageProcessing($msg->id, 1);
                $this->sendGreeting($followers);
                $this->randomWait();
                $this->sendMessage($msg->id, $followers);
                $this->updateMessageStat($msg->user_id, $msg->id, $followers);
                $this->setMessageSent($msg->id, 1);
                $this->setMessageProcessing($msg->id, 0);
                $this->updateSentDate($msg->id);
            } catch (Exception $ex) {
                $this->setMessageFailed($msg->id, 1);
                $this->setMessageProcessing($msg->id, 0);
                $this->unlockMessage();
                $this->interrupt($ex->getMessage());
            }
        }
        $this->unlockMessage();
        printf("%s - TERMINADO EL PROCESAMIENTO DE MENSAJES...\n", $this->now());
    }

    public function promos()
    {
        set_time_limit(0);
        $this->load->database();
        printf("%s - PROCESANDO MENSAJES PROMOCIONALES...\n", $this->now());
        if ($this->messagesLocked()) { $this->interrupt('- Esta bloqueada la cola de mensajes'); }
        $this->lockMessage();
        $messages = $this->lastMessages(TRUE);
        if (count($messages) === 0) {
            printf("- No hay promociones en cola por ahora\n");
            $this->unlockMessage();
            printf("%s - TERMINADO EL PROCESAMIENTO DE PROMOCIONES...\n", $this->now());
            return;
        }
        $this->getInstagram();
        foreach ($messages as $message) {
            printf("* Procesando promocion %s...\n", $message->id);
            $this->setMessageProcessing($message->id, 1);
            $user = $this->getUser($message->user_id);
            if ($this->promoRecipientsCount($user->pk)==0) {
                $this->setMessageProcessing($message->id, 0);
                $this->setMessageSent($message->id, 1);
                printf("* Terminado el envio de la promocion a todos los seguidores...\n");
                continue;
            }
            if ($this->dailyLimitPassed($user->id)) {
                $this->setMessageProcessing($message->id, 0);
                printf("* Procesada la promocion %s...\n", $message->id);
                continue;
            }
            try {
                $this->loginInstagram($user->username, $user->password);
                $followers = $this->promoRecipients($message->id);
                $_followers = array_values($followers);
                $this->purgePromoRecipientsList($message->id, $followers);
                if (count($followers)===0) {
                    $this->setMessageProcessing($message->id, 0);
                    continue;
                }
                $this->sendGreeting($followers);
                $this->randomWait();
                $this->sendMessage($message->id, $followers);
                $this->updateSentDate($message->id);
                $this->insertStat($message->id, $followers);
                $this->popAlreadyTexted($user->pk, $_followers);
                $this->setMessageProcessing($message->id, 0);
            }
            catch (Exception $ex) {
                $this->setMessageFailed($message->id, 1);
                $this->setMessageProcessing($message->id, 0);
                $this->unlockMessage();
                $this->interrupt($ex->getMessage());
            }
            printf("* Procesada la promocion %s...\n", $message->id);
        }
        $this->unlockMessage();
        printf("%s - TERMINADO EL PROCESAMIENTO DE PROMOCIONES...\n", $this->now());
    }

    protected function now() {
        date_default_timezone_set(TIME_ZONE);
        return \Carbon\Carbon::now()->format('d-M H:i:s');
    }

    public function getInstagram() {
        if ($this->instagram !== NULL) { return $this->instagram; }
        $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        printf("- Obtenida instancia del objeto Instagram\n");
    }

    public function alreadyTexted($pk, $msg_id)
    {
        $sql = "select count(*) as messages from stat "
                . "where follower_id = ? and msg_id = ?";
        $result = $this->db->query($sql, [
            'follower_id' => $pk,
            'msg_id' => $msg_id
        ])->result();
        return $result[0]->messages > 0 ? TRUE : FALSE;
    }

    public function promoRecipients($msg_id)
    {
        $message = $this->getMessage($msg_id);
        $pk = $this->getUser($message->user_id)->pk;
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $c = mt_rand(1, 5);
        $followers = trim(shell_exec("head -n $c $followers_file"));
        if ($followers == '') { return NULL; }
        $promo_recip = explode(PHP_EOL, $followers);
        printf("- Se enviara promocion a seguidores: [%s]\n", implode(',', $promo_recip));
        return $promo_recip;
    }

    public function promoRecipientsCount($pk)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $count = trim(shell_exec("cat $followers_file | wc -l"));
        printf("- La promocion tiene %s seguidores pendientes...\n",
            $count);
        return intval($count);
    }

    public function promoDefinedFollowersList($pk)
    {
        $exists_followers_file = file_exists(FOLLOWERS_LIST_DIR . "/$pk.txt");
        return $exists_followers_file;
    }

    public function purgePromoRecipientsList($msg_id, &$followers)
    {
        $count = count($followers);
        for ($i = 0; $i < $count; $i++) {
            $already_texted = $this->alreadyTexted($followers[$i], $msg_id);
            if ($already_texted) {
                printf("- Sacando de la lista al seguidor %s porque ya recibio promocion\n",
                        $followers[$i]);
                array_splice($followers, $i, 1);
                $count--;
            }
        }
    }

    /**
     *
     * @param array() $followers Arreglo con la lista de pks de los seguidores
     * a los que se les envio el mensaje.
     */
    public function insertStat($msg_id, $followers)
    {
        date_default_timezone_set(TIME_ZONE);
        $message = $this->getMessage($msg_id);
        foreach ($followers as $follower) {
            if (trim($follower)=='') { continue; }
            $data = [
                'user_id' => $message->user_id,
                'follower_id' => $follower,
                'msg_id' => $message->id,
                'dt' => \Carbon\Carbon::now()->getTimestamp()
            ];
            $this->db->insert('stat', $data);
        }
        printf("- Registrado el envio para los seguidores [%s]\n",
            implode(',', $followers));
    }

    public function popAlreadyTexted($pk, $followers)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        foreach ($followers as $follower) {
            $cmd = "sed -i '/$follower/d' " . $followers_file;
            shell_exec($cmd);
        }
        printf("- Sacados de la lista de destinatarios los perfiles [%s]\n",
            implode(',', $followers));
    }

    public function randomGreeting($lang = 'pt')
    {
        $greetings = [
            'pt' => [
                0 => "Oi, todo bem?",
                1 => "Olá, todo bem?",
                2 => "Oi, como vai?",
                3 => "Como vai tudo?",
                4 => "Eu tenho algo para lhe dizer",
                5 => "Olá, como vai?",
            ],
            'en' => [
                0 => "Hi!",
                1 => "How are you?",
                2 => "How do you feel today? I want to tell you something...",
                3 => "I have something to tell you...",
            ],
            'es' => [
                0 => "Hola!",
                1 => "Cómo estás?",
                2 => "Cómo te sientes hoy? Tengo algo que decirte...",
                3 => "Tengo algo que decirte...",
            ]
        ];
        $n = mt_rand(0, count($greetings[$lang]) - 1);
        $greeting = $greetings[$lang][$n];
        printf("- Saludo aleatorio escogido: \"%s\"\n", $greeting);
        return $greeting;
    }

    public function sendGreeting($followers, $lang = 'pt')
    {
        try {
            $greeting = $this->randomGreeting($lang);
            $this->instagram->directMessage($followers, $greeting);
            printf("- Enviado saludo \"%s\" a los seguidores escogidos\n", $greeting);
        }
        catch (Exception $ex) {
            $msg = sprintf("- Error al enviar el saludo inicial; ERROR: \"%s\"\n",
                $ex->getMessage());
            throw new Exception($msg);
        }
    }

    public function updateSentDate($msg_id)
    {
        date_default_timezone_set(TIME_ZONE);
        $this->db->where('id', $msg_id)
            ->update('message', [
                'sent_at' => \Carbon\Carbon::now()->timestamp
            ]);
    }

    public function sendMessage($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        try {
            $this->instagram->directMessage($followers, $message->msg_text);
            printf("- Se envio el mensaje \"%s...\"; a los seguidores [%s]\n",
                trim(substr($message->msg_text, 0, 15)), implode(", ", $followers));
        }
        catch (Exception $ex) {
            $msg = sprintf("- Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                trim(substr($message->msg_text, 0, 15)), $ex->getMessage());
            $this->setMessageFailed($msg_id, 1);
            $this->setMessageProcessing($msg_id, 0);
            throw new Exception($msg, 500);
        }
    }

    public function setMessageFailed($msg_id, $failed = 0)
    {
        $this->db->where('id', $msg_id)
                ->update('message', [
                    'failed' => $failed
                ]);
        printf("- Se establecio el estado del mensaje a \"%s\"...\n",
                $failed == 0 ? 'NO FALLIDO' : 'FALLIDO');
    }

    public function isOldMsg($msg_id, $minutes = 10)
    {
        date_default_timezone_set(TIME_ZONE);
        $before = \Carbon\Carbon::now()->subMinutes($minutes)->timestamp;
        $messages = $this->db->where('id', $msg_id)
                ->get('message')->result();
        if ($messages[0]->sent_at <= $before) {
            return TRUE;
        }
        else {
            FALSE;
        }
    }

    public function getMessage($msg_id)
    {
        $messages = $this->db->where('id', $msg_id)->get('message')
            ->result();
        return $messages[0];
    }

    public function getUser($user_id)
    {
        $users = $this->db->where('id', $user_id)->get('client')->result();
        printf("- Obtenidos datos del usuario %s\n", $users[0]->username);
        return $users[0];
    }

    public function loginInstagram($username, $password) {
        try {
            $this->instagram->setUser($username, $password);
            $this->instagram->login();
            printf("- Iniciada sesión en Instagram como %s\n", $username);
        } catch (Exception $ex) {
            $msg = sprintf("- No se pudo iniciar sesion para \"%s\". CAUSA: \"%s\"\n", $username, $ex->getMessage());
            throw new Exception($msg);
        }
    }

    public function randomWait() {
        $secs = mt_rand(10, 30);
        printf("- Esperando %s segs para continuar\n", $secs);
        sleep($secs);
    }

    public function lockMessage() {
        file_put_contents(ROOT_DIR . '/var/message.lock', '');
        printf("- Bloqueada la cola de mensajes...\n");
    }

    public function unlockMessage() {
        if (file_exists(ROOT_DIR . '/var/message.lock')) {
            unlink(ROOT_DIR . '/var/message.lock');
            printf("- Desbloqueada la cola de mensajes...\n");
            return;
        }
        printf("- La cola de mensajes no estaba bloqueada...\n");
    }

    public function interrupt($msg = '')
    {
        if ($msg === '') {
            printf("INTERRUMPIDO!!!\n");
        }
        else {
            printf("INTERRUMPIDO!!! CAUSA: %s\n", $msg);
        }
        die();
    }

    public function messagesLocked()
    {
        $is_locked = file_exists(ROOT_DIR . '/var/message.lock');
        printf("- La cola de mensajes esta %s\n", $is_locked ? 'BLOQUEADA' : 'LIBERADA');
        return $is_locked;
    }

    public function oldestPromoList($minutes = 9, $count = 5)
    {
        date_default_timezone_set(TIME_ZONE);
        $before = \Carbon\Carbon::now()->subMinutes($minutes)->timestamp;
        $messages = $this->db->where('promo', 1)
                ->where('processing', NOT_PROCESSING)
                ->where('failed', NOT_FAILED)
                ->where('sent', NOT_SENT)
                ->where('sent_at <=', $before)
                ->get('message')->result();
        printf("- Devolviendo lista de las %s promociones mas antiguas...\n", $count);
        return $messages;
    }

    public function lastMessages($promo = FALSE)
    {
        if ($promo) {
            return $this->oldestPromoList();
        }
        else {
            $messages = $this->db
                ->where('processing', NOT_PROCESSING)
                ->where('failed', NOT_FAILED)
                ->where('sent', NOT_SENT)
                ->where('promo', IS_NOT_PROMOTION)
                ->get('message')->result();
            printf("- Devolviendo lista de los ultimos 5 mensajes...\n");
            return $messages;
        }
    }

    public function dayStart()
    {
        date_default_timezone_set(TIME_ZONE);
        return \Carbon\Carbon::parse(date('Y-m-d') . ' 00:00:00')->timestamp;
    }

    public function dailyLimitPassed($user_id, $limit = 200)
    {
        $sql = 'select count(*) as messages from stat '
                . 'where user_id = ? and dt >= ?';
        $result = $this->db->query($sql, [
            'user_id' => $user_id,
            'dt' => $this->dayStart()
        ])->result();
        $is_passed = $result[0]->messages < $limit ? FALSE : TRUE;
        if ($is_passed) {
            printf("- El usuario ya llego al limite de mensajes diarios\n");
        }
        return $is_passed;
    }

    public function startStoppedPromosBy12h() {
        date_default_timezone_set(TIME_ZONE);
        $this->load->database();
        $now = new \Carbon\Carbon;
        $hours = 11;
        $pastTime = $now->subHours($hours)->timestamp;
        printf("Reactivando promociones con mas de %sh\n", $hours);
        $sql = sprintf("select id from message ".
               "where sent_at <= %d ".
               "and promo=%d and failed=%d and processing=%d ".
               "and sent=%d",
            $pastTime, IS_PROMOTION, FAILED, NOT_PROCESSING, NOT_SENT);
        $promos = $this->db->query($sql)->result();
        if (count($promos)===0) {
            printf("No hay promociones con mas de %sh\n", $hours);
            return;
        }
        printf("Se reactivaran %s promociones\n", count($promos));
        foreach ($promos as $promo) {
            printf("Reactivando promocion %s\n", $promo->id);
            $this->db->where('id', $promo->id);
            $this->db->update('message', [
                'failed' => NOT_FAILED,
                'processing' => NOT_PROCESSING,
                'sent_at' => date("U")
            ]);
        }
        printf("Se reactivaron %s promociones\n", count($promos));
    }

    public function textBeginnersDumbuONE() {
        $beginnersFiles = FOLLOWERS_LIST_DIR . '/beginners.csv';
        date_default_timezone_set(TIME_ZONE);
        $this->load->database();
        $now = new \Carbon\Carbon;
        printf("* %s - ENVIANDO TEXTO A BEGINNERS...\n", $now->toTimeString());
        $timestamp = $now->timestamp;
        $followersCount = mt_rand(1, 5);
        $followersList = explode(PHP_EOL, shell_exec("head -n $followersCount $beginnersFiles"));
        $ptFollowers = array_filter($followersList, function($item) {
            if (strstr($item, 'PT') !== FALSE) {
                $data = explode(',', $item);
                return $data[0] !== null;
            }
        });
        $ptFollowers = array_map(function($f) {
            $data = explode(',', $f);
            return preg_replace('/"/', '', $data[0]);
        }, $ptFollowers);
        if (count($ptFollowers)>0) {
            printf("- Colectados estos seguidores [%s] (portugues)\n",
                   implode(',', $ptFollowers));
        }
        $enFollowers = array_filter($followersList, function($item) {
            if (strstr($item, 'EN') !== FALSE) {
                $data = explode(",", $item);
                return $data[0] !== null;
            }
        });
        $enFollowers = array_map(function($f) {
            $data = explode(',', $f);
            return preg_replace('/"/', '', $data[0]);
        }, $enFollowers);
        if (count($enFollowers)>0) {
            printf("- Colectados estos seguidores [%s] (ingles)\n",
                   implode(',', $enFollowers));
        }
        $esFollowers = array_filter($followersList, function($item) {
            if (strstr($item, 'ES') !== FALSE) {
                $data = explode(',', $item);
                return $data[0] !== null;
            }
        });
        $esFollowers = array_map(function($f) {
            $data = explode(',', $f);
            return preg_replace('/"/', '', $data[0]);
        }, $esFollowers);
        if (count($esFollowers)>0) {
            printf("- Colectados estos seguidores [%s] (portugues)\n",
                   implode(',', $esFollowers));
        }
        try {
            $this->getInstagram();
            $this->loginInstagram('dumbu_zero_1', 'XPcom01.*');
        }
        catch(\Exception $e) {
            printf("\n", $e->getMessage());
        }
        if (count($ptFollowers)>0) {
            printf("- Se enviara a estos seguidores: [%s]\n",
                implode(',', $ptFollowers));
            $followerMsgFile = sprintf("%s/var/promo.beginner.pt.txt", ROOT_DIR);
            $msgText = file_get_contents($followerMsgFile);
            $greeting = $this->randomGreeting('pt');
            $this->instagram->directMessage($ptFollowers, $greeting);
            $this->randomWait();
            $this->instagram->directMessage($ptFollowers, $msgText);
            printf("- Enviado el mensaje a los seguidores seleccionados\n");
            foreach ($ptFollowers as $data) {
                rep_in_file($beginnersFiles, $data);
            }
            printf("- Sacados de la lista los seguidores texteados\n");
        }
        if (count($enFollowers)>0) {
            printf("- Se enviara a estos seguidores: [%s]\n",
                implode(',', $enFollowers));
            $followerMsgFile = sprintf("%s/var/promo.beginner.en.txt", ROOT_DIR);
            $msgText = file_get_contents($followerMsgFile);
            $greeting = $this->randomGreeting('en');
            $this->instagram->directMessage($enFollowers, $greeting);
            $this->randomWait();
            $this->instagram->directMessage($enFollowers, $msgText);
            printf("- Enviado el mensaje a los seguidores seleccionados\n");
            foreach ($enFollowers as $data) {
                rep_in_file($beginnersFiles, $data);
            }
            printf("- Sacados de la lista los seguidores texteados\n");
        }
        if (count($esFollowers)>0) {
            printf("- Se enviara a estos seguidores: [%s]\n",
                implode(',', $esFollowers));
            $followerMsgFile = sprintf("%s/var/promo.beginner.es.txt", ROOT_DIR);
            $msgText = file_get_contents($followerMsgFile);
            $greeting = $this->randomGreeting('es');
            $this->instagram->directMessage($esFollowers, $greeting);
            $this->randomWait();
            $this->instagram->directMessage($esFollowers, $msgText);
            printf("- Enviado el mensaje a los seguidores seleccionados\n");
            foreach ($esFollowers as $data) {
                rep_in_file($beginnersFiles, $data);
            }
            printf("- Sacados de la lista los seguidores texteados\n");
            $endTime = new \Carbon\Carbon;
        }
        $endTime = new \Carbon\Carbon;
        printf("- Se notifico a %s beginners\n", $followersCount);
        printf("* %s - TERMINADO EL ENVIO A LOS BEGINNERS\n", $endTime->toTimeString());
    }

    public function textBeginnersDumbuPRO() {
        $beginnersFiles = FOLLOWERS_LIST_DIR . '/dumbu.pro.beginners.csv';
        date_default_timezone_set(TIME_ZONE);
        $this->load->database();
        $now = new \Carbon\Carbon;
        printf("* %s - ENVIANDO TEXTO A BEGINNERS...\n", $now->toTimeString());
        $timestamp = $now->timestamp;
        $followersCount = mt_rand(1, 5);
        $followersList = explode(PHP_EOL, shell_exec("head -n $followersCount $beginnersFiles"));
        $ptFollowers = array_filter($followersList, function($item) {
            if (strstr($item, 'PT') !== FALSE) {
                $data = explode(',', $item);
                return $data[0] !== null;
            }
        });
        $ptFollowers = array_map(function($f) {
            $data = explode(',', $f);
            return preg_replace('/"/', '', $data[0]);
        }, $ptFollowers);
        if (count($ptFollowers)>0) {
            printf("- Colectados estos seguidores [%s] (portugues)\n",
                   implode(',', $ptFollowers));
        }
        $enFollowers = array_filter($followersList, function($item) {
            if (strstr($item, 'EN') !== FALSE) {
                $data = explode(",", $item);
                return $data[0] !== null;
            }
        });
        $enFollowers = array_map(function($f) {
            $data = explode(',', $f);
            return preg_replace('/"/', '', $data[0]);
        }, $enFollowers);
        if (count($enFollowers)>0) {
            printf("- Colectados estos seguidores [%s] (ingles)\n",
                   implode(',', $enFollowers));
        }
        $esFollowers = array_filter($followersList, function($item) {
            if (strstr($item, 'ES') !== FALSE) {
                $data = explode(',', $item);
                return $data[0] !== null;
            }
        });
        $esFollowers = array_map(function($f) {
            $data = explode(',', $f);
            return preg_replace('/"/', '', $data[0]);
        }, $esFollowers);
        if (count($esFollowers)>0) {
            printf("- Colectados estos seguidores [%s] (portugues)\n",
                   implode(',', $esFollowers));
        }
        try {
            $this->getInstagram();
            $this->loginInstagram('dumbu.09', 'dumbu2017');
        }
        catch(\Exception $e) {
            printf("\n", $e->getMessage());
        }
        if (count($ptFollowers)>0) {
            printf("- Se enviara a estos seguidores: [%s]\n",
                implode(',', $ptFollowers));
            $followerMsgFile = sprintf("%s/var/promo.beginner.pt.txt", ROOT_DIR);
            $msgText = file_get_contents($followerMsgFile);
            $greeting = $this->randomGreeting('pt');
            $this->instagram->directMessage($ptFollowers, $greeting);
            $this->randomWait();
            $this->instagram->directMessage($ptFollowers, $msgText);
            printf("- Enviado el mensaje a los seguidores seleccionados\n");
            foreach ($ptFollowers as $data) {
                rep_in_file($beginnersFiles, $data);
            }
            printf("- Sacados de la lista los seguidores texteados\n");
        }
        if (count($enFollowers)>0) {
            printf("- Se enviara a estos seguidores: [%s]\n",
                implode(',', $enFollowers));
            $followerMsgFile = sprintf("%s/var/promo.beginner.en.txt", ROOT_DIR);
            $msgText = file_get_contents($followerMsgFile);
            $greeting = $this->randomGreeting('en');
            $this->instagram->directMessage($enFollowers, $greeting);
            $this->randomWait();
            $this->instagram->directMessage($enFollowers, $msgText);
            printf("- Enviado el mensaje a los seguidores seleccionados\n");
            foreach ($enFollowers as $data) {
                rep_in_file($beginnersFiles, $data);
            }
            printf("- Sacados de la lista los seguidores texteados\n");
        }
        if (count($esFollowers)>0) {
            printf("- Se enviara a estos seguidores: [%s]\n",
                implode(',', $esFollowers));
            $followerMsgFile = sprintf("%s/var/promo.beginner.es.txt", ROOT_DIR);
            $msgText = file_get_contents($followerMsgFile);
            $greeting = $this->randomGreeting('es');
            $this->instagram->directMessage($esFollowers, $greeting);
            $this->randomWait();
            $this->instagram->directMessage($esFollowers, $msgText);
            printf("- Enviado el mensaje a los seguidores seleccionados\n");
            foreach ($esFollowers as $data) {
                rep_in_file($beginnersFiles, $data);
            }
            printf("- Sacados de la lista los seguidores texteados\n");
        }
        $endTime = new \Carbon\Carbon;
        printf("- Se notifico a %s beginners\n", $followersCount);
        printf("* %s - TERMINADO EL ENVIO A LOS BEGINNERS\n", $endTime->toTimeString());
    }
}
