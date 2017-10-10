<?php

$action = @$argv[1];

// Inyectar la clase estatica de acceso a datos
Command::$schema = $schema;

if ($action === 'messages') {
    $messageQueue = new MessageQueue();
    $messageQueue->process();
}
else if ($action === 'promotion') {
    $promoQueue = new PromotionQueue();
    $promoQueue->process();
}
else {
    printf("\nEspecifique la accion correcta:\n\n");
    printf("- messages  = Procesa la cola de mensajes creados desde la Web\n\n");
    printf("- promotion = Procesa cola de mensajes promocionales\n\n");
}
