<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Task
 *
 * @author yordano
 */
class Task {
    
    /**
     * Devuelve el consecutivo ultimo de la tareas en cola
     */
    public static function findLast()
    {
        $cmd = sprintf("ls %s/*.json | tail -n 1",
                APPPATH . '/../tasks');
        return trim(shell_exec($cmd));
    }
    
    /**
     * Crea una tarea que se ejecutara en su momento
     * por el programador de tareas
     * 
     * @param array $task Arreglo con el siguiente formato:
     * [
     *  'pk' => Id de Instagram del usuario
     *  'dest' => Arreglo con los id de los perfiles destinatarios
     *  'message' => Cadena de texto con el mensaje a enviar a los destinatarios
     * ]
     */
    public function create($task)
    {
        $mark = date("Ymd_His_U");
        $taskFileName = sprintf("%s/%s.json", APPPATH . '/../tasks', $mark);
        file_put_contents($taskFileName, json_encode($task));
    }
    
}
