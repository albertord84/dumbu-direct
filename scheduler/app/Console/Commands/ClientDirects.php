<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClientDirects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:clients';
    
    protected $session_id = NULL;

    protected $pk = '';

    protected $pks = NULL;

    protected $username = '';
    
    protected $password = '';

    protected $message = '';
    
    protected $instagram = NULL;

    protected $proxy = NULL;

    protected $suspended = FALSE;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages from logged clients';
    
    protected $task = NULL;
    
    protected function loadTask()
    {
        $task_id = trim(shell_exec(sprintf("tail -n 1 %s", 
                QUEUE_PATH . '/registered_tasks')));
        $task_file = trim(shell_exec("ls %s/*%s.json | tail -n 1"));
        $task_object = json_decode(file_get_contents($task_file));
        $this->task = $task_object;
    }
    
    /**
     * Saca la tarea del registro de tareas pendientes para evitar
     * accesos concurrentes y que entonces se envien mensajes repetidos
     * a un mismo destinatario
     */
    protected function lockTaskAccess()
    {
        $tasks_registry = QUEUE_PATH . '/registered_tasks';
        file_put_contents($tasks_registry . '.lock', '');
    }

    protected function unlockTaskAccess()
    {
        $tasks_registry_lock = QUEUE_PATH . '/registered_tasks.lock';
        unlink($tasks_registry_lock);
    }

    protected function unregisterTask()
    {
        $task_registry_file = QUEUE_PATH . '/registered_tasks';
        $cmd = sprintf("sed -i -- 's/%s//g' %s", $this->session_id,
                $task_registry_file);
    }

    public function init()
    {
        set_time_limit(0);

        while (file_exists(QUEUE_PATH . '/registered_tasks.lock')) {
            sleep(15);
        }

        $this->loadTask();
        $this->lockTaskAccess();
        $this->unregisterTask();
        $this->unlockTaskAccess();
        
        $this->pk = $this->task->pk;
        $this->session_id = $this->task->session_id;
        $this->username = $this->task->username;
        $this->password = $this->task->password;
        $this->pks = array_slice($this->task->pks, 0, 5);
        $this->message = $this->task->message;
        $this->suspended = FALSE;
        
        $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
    }
    

    protected function writeToFollowers()
    {
        try {
            $this->instagram->directMessage($this->pks, $this->message);
            return TRUE;
        }
        catch (\Exception $e) {
            $this->log(sprintf("Error de envio %s(%s) => %s: %s",
                $this->username, $this->pk, implode(',', $this->pks), $e->getMessage()));
            return TRUE;
        }
    }
    
    protected function loginInstagram()
    {
        try {
            $this->instagram->setUser($this->username, $this->password);
            if ($this->proxy !== NULL) {
                $this->instagram->setProxy($this->proxy);
            }
            $this->instagram->login();
            return TRUE;
        }
        catch(\Exception $e) {
            $this->log(sprintf("Error al iniciar sesion como %s: %s",
                $this->username, $e->getMessage()));
            return FALSE;
        }
    }
    
    protected function log($t)
    {
        echo sprintf("%s - %s" . PHP_EOL, 
                Carbon\Carbon::now()->format('M d, H:m:s'),
                $t);
    }
    
    protected function cleanTask()
    {
        $cmd = sprintf("ls %s/*%s.txt | tail -n 1", TASKS_DIR, $this->pk);
        $file_name = trim(shell_exec($cmd));
        unlink($file_name);
    }

    protected function updateStats()
    {
        $cmd = sprintf("ls %s/*%.txt", STATS_DIR, $this->pk);
        $stats_file = trim(shell_exec($cmd));
        foreach ($this->pks as $pk) {
            $already_there = trim(shell_exec("grep -c %s %s",
                $this->pk, $stats_file)) == '0' ? FALSE : TRUE;
            if (!$already_there) {
                file_put_contents($stats_file, $pk, FILE_APPEND);
            }
        }
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
    {
        $this->init();
        
        if ($this->loginInstagram()) {
            $success = $this->writeToFollowers();
        } else {
            return;
        }

        if (!$success) {
            $this->log(sprintf("No se pudo enviar el mensaje de %s: %s",
                $this->username, substr($this->message, 0, 15) . '...'));
            return;
        }
        
        if ($success) {
            $this->updateStats();
            $this->cleanTask();
        }
    }

}
