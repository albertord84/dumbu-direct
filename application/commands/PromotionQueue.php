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
            printf("* Procesado el mensaje %s...\n", $message->id);
        }
        $this->unlockMessage();
        printf("%s - TERMINADO EL PROCESAMIENTO DE PROMOCIONES...\n", $this->now());
    }

}
