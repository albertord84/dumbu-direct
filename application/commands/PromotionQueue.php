<?php

class PromotionQueue extends Command {

    function __construct() {
        
    }
    
    public function process()
    {
        printf("%s - PROCESANDO MENSAJES PROMOCIONALES...\n", $this->now());
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
                $this->unlockMessage();
                $this->interrupt($ex->getMessage());
            }
            $this->setMessageProcessing($message->id, 0);
        }
        $this->unlockMessage();
        printf("%s - TERMINADO EL PROCESAMIENTO DE LAS PROMOCIONES...\n", $this->now());
    }

}
