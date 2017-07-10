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
        
        if ($resp == '0') {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
}

