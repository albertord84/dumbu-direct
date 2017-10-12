<?php

class MessageQueue extends Command {

    function __construct() {
        
    }
    
    public function process()
    {
        printf("%s - PROCESANDO COLA DE MENSAJES...\n", $this->now());
        if ($this->messagesLocked()) { $this->interrupt('- Esta bloqueada la cola de mensajes'); }
        $this->lockMessage();
        $messages = $this->lastMessages();
        if (count($messages) === 0) {
            printf("- No hay mensajes en cola por ahora\n");
            $this->unlockMessage();
            printf("%s - TERMINADO EL PROCESAMIENTO DE MENSAJES...\n", $this->now());
            return;
        }
        $this->getInstagram();
        foreach ($messages as $message) {
            printf("* Procesando mensaje %s...\n", $message->id);
            $this->setMessageProcessing($message->id, 1);
            $user = $this->getUser($message->user_id);
            $followers = $this->messageRecipients($message->user_id);
            if ($this->dailyLimitPassed($user->id)) {
                $this->setMessageProcessing($message->id, 0);
                printf("* Procesado el mensaje %s...\n", $message->id);
                continue;
            }
            try {
                $this->loginInstagram($user->username, $user->password);
                $this->sendGreeting($followers);
                $this->randomWait();
                $this->sendMessage($message->id, $followers);
                $this->updateSentDate($message->id);
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
        printf("%s - TERMINADO EL PROCESAMIENTO DE MENSAJES...\n", $this->now());

    }

}