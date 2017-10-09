<?php

class MessageQueue extends Command {

    function __construct() {
        
    }
    
    public function process()
    {
        printf("%s - Procesando cola de mensajes...\n", $this->now());
    }

}