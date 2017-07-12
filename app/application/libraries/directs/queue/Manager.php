<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Directs queue manager
 *
 * @author yordano
 */
class Manager {
    
    /**
     * Agrega a la cola un nuevo mensaje direct
     * 
     * @param string $uid Id del usuario autenticado
     * @param string $timestamp Marca de tiempo para identificar de manera unica el mensaje
     * @param string $pks Lista separada por comas de ids de los perfiles destinatarios
     * @param string $message Texto del mensaje que se enviara a los destinos
     * 
     * @return bool Verdadero si se logra crear y poner el archivo en la cola, si no, falso.
     */
    public function add($uid, $timestamp, $pks, $message)
    {
        //if ( $this->exists($uid) ) {
        //    return FALSE;
        //}
        
        $dir = APPPATH . '/logs/directs/queue';
        $filename = sprintf("%s/%s_%s.json", $dir, $timestamp, $uid);
        
        $pks_arr = [];
        $pks_strings = explode(',', $pks);
        for($i = 0; $i < count($pks_strings); $i++)
        {
            if (!empty(trim($pks_strings[$i]))) {
                $pks_arr[] = $pks_strings[$i];
            }
        }
        
        $data = [
            'datetime' => $timestamp,
            'uid' => $uid,
            'pks' => $pks_arr,
            'message' => $message
        ];
        
        $fhandle = fopen($filename, 'w');
        fwrite($fhandle, json_encode($data) . PHP_EOL);
        fclose($fhandle);
        
        if ( file_exists($filename) ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Chequea si ya el usuario esta enviando mensajes.
     * 
     * @param string $uid Id del usuario.
     * @return boolean Verdadero si el usuario ya genero mensajes que estan siendo procesados en la cola, si no, falso.
     */
    public function exists($uid)
    {
        $cmd = sprintf("ls %s | grep -c %s", 
                APPPATH . '/logs/directs/queue', 
                $uid);
        
        $resp = trim(shell_exec($cmd));
        
        return $resp == '0' ? FALSE : TRUE;
    }
    
    /**
     * Devuelve el ultimo mensaje enviado por el usuario.
     * 
     * @param string $uid Id del usuario.
     * @return string Contenido del mensaje en formato JSON.
     */
    public function get($uid)
    {
        $cmd = sprintf("find %s -name \"*%s*\" | sort | tail -n 1", 
                APPPATH . '/logs/directs/queue', 
                $uid);
        
        $resp = trim(shell_exec($cmd));
        
        $file = file_get_contents($resp);
        
        return $file;
    }
    
    /**
     * Devuelve si ya existe un perfil seleccionado para enviarle mensajes.
     * Con esto se puede evitar que se le envien mensajes repetidos a un
     * perfil al que ya se selecciono para enviarle con anterioridad.
     * 
     * @param string $pk Id del perfil de Instagram que se desea chequear si ya se le esta enviando mensajes.
     * @return boolean Verdadero si ya se le esta enviando mensajes al perfil especificado, si no, falso.
     */
    public function pk_taken($pk)
    {
        $cmd = sprintf("cat %s/*.json | grep -c %s", 
                APPPATH . '/logs/directs/queue',
                $pk);
        
        $resp = trim(shell_exec($cmd));
        
        if ( intval( $resp ) <= 1 ) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    /**
     * Devuelve la cantidad de mensajes en la cola.
     * 
     * @return int Cantidad de mensajes que estan en cola.
     */
    public function queue_count()
    {
        $cmd = sprintf("ls -l %s | grep -c json", 
                APPPATH . '/logs/directs/queue');
        
        $resp = trim(shell_exec($cmd));
        
        return $resp;
    }

    /**
     * Devuelve el listado de mensajes en la cola
     * 
     * @param int $page Pagina que se desea mostrar.
     * @param int $count Cantidad de mensajes por pagina.
     * 
     * @return array Lista de mensajes.
     */
    public function msg_page($page, $count)
    {
        $total = $this->queue_count();
        
        $start = intval( $total / $page );
        $cmd = sprintf("ls %s | tail -n %s | head -n %s", 
                APPPATH . '/logs/directs/queue', 
                $start, $count);
        
        $resp = explode( PHP_EOL, trim( shell_exec($cmd) ) );
        
        return $resp;
    }
    
    /**
     * Devuelve el ID del ultimo mensaje enviado.
     * 
     * @return mixed Contenido del ultimo mensaje enviado, si no, FALSE
     */
    public function last_sent()
    {
        $filename = APPPATH . '/logs/directs/last.json';
        
        if ( ! file_exists($filename) ) {
            return FALSE;
        }
        
        return file_get_contents($filename);
    }
    
    /**
     * Establece como ultimo mensaje procesado aquel cuyo nombre de archivo
     * se pase como parametro.
     * 
     * @param string $filename Nombre del archivo que fue el ultimo mensaje procesado
     * @param int $page Numero de la ultima pagina que se proceso
     */
    public function set_last($filename, $page)
    {
        $last = APPPATH . '/logs/directs/last.json';
        $json_data = json_decode( file_get_contents(APPPATH . '/logs/directs/queue/' . $filename) );
        $json_data->page = $page;
        file_put_contents( $last, json_encode($json_data) );
    }
    
}

