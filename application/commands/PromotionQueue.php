<?php

class PromotionQueue extends Command {

    function __construct() {
        
    }
    
    public function process()
    {
        printf("%s - Procesando mensajes promocionales...\n", $this->now());
        if ($this->messagesLocked()) { $this->interrupt('- Esta bloqueada la cola de mensajes'); }
        $this->lockMessage();
        $messages = $this->lastMessages(TRUE);
        if (count($messages) === 0) {
            printf("- No hay promociones en cola por ahora\n");
            $this->unlockMessage();
            return;
        }
        foreach ($messages as $message) {
            $this->setMessageProcessing($message->id, 1);
            $user = $this->getUser($message->user_id);
            if ($this->dailyLimitPassed($user->id)) {
                printf("- El usuario %s ya llego al limite de mensajes diarios\n",
                        $user->username);
                $this->unlockMessage();
                return;
            }
            $this->getInstagram();
            try {
                $this->loginInstagram($user->username, $user->password);
                $followers = $this->promoRecipients($message->id);
                $this->purgePromoRecipientsList($message->id, $followers);
                if (count($followers)===0) {
                    $this->setMessageProcessing($msg_id, 0);
                    continue;
                }
                $this->sendGreeting($followers);
                $this->randomWait();
                $this->sendMessage($message->id, $followers);
                $this->randomWait();
            }
            catch (Exception $ex) {
                $this->interrupt($ex->getMessage());
            }
            $this->setMessageProcessing($msg_id, 0);
        }
        $this->unlockMessage();
    }

}
