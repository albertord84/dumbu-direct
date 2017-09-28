<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Task
 *
 * @author yordano
 */
class Task {
    
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
        $taskFileName = sprintf("%s/%s_%s.json", TASKS_DIR, $mark,
                $task['session_id']);
        write_file($taskFileName, json_encode($task, JSON_PRETTY_PRINT));
    }
    
    /**
     * Crea la lista de seguidores sobre los que se ejecutara
     * la tarea
     * 
     * @param string $pk Id del cliente que genero la tarea
     * @param string $data Cadena separada por comas, con los id de perfiles seguidores
     */
    public function saveFollowersList($pk, $data)
    {
        $ids_array = $data;
        $fname = sprintf("%s/%s_%s.txt", FOLLOWERS_DIR, date('U'), $pk);
        $FILE = fopen($fname, "w");
        foreach ($ids_array as $id) {
            fwrite($FILE, trim($id) . PHP_EOL);
        }
        fclose($FILE);
    }
    
    public function createStatsFile($pk)
    {
        $fname = sprintf("%s/%s_%s.txt", STATS_DIR, date('U'), $pk);
        $FILE = fopen($fname, "w");
        fwrite($FILE, '');
        fclose($FILE);
    }
    
    public function alreadyRegistered($session_id)
    {
        $cmd = sprintf('grep -c %s %s', $session_id, 
                QUEUE_PATH . '/registered_tasks');
        $cmd_output = trim(shell_exec($cmd));
        if (intval($cmd_output) === 1) {
            return TRUE;
        }
        else FALSE;
    }

    public function register($session_id)
    {
        shell_exec(sprintf("echo $session_id >> %s",
                QUEUE_PATH . '/registered_tasks'));
    }
    
}
