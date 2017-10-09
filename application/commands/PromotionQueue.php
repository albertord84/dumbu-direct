<?php

class PromotionQueue extends Command {

    function __construct() {
        
    }
    
    public function process()
    {
        printf("%s - Procesando mensajes promocionales...\n", $this->now());
    }

}
