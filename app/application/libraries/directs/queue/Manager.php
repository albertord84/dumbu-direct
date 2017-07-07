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
     */
    public function add($uid, $timestamp, $pks, $message)
    {
        //if ( $this->exists($uid) ) {
        //    return FALSE;
        //}
        
        $dir = APPPATH . '/logs/directs/queue';
        $filename = sprintf("%s/%s_%s.json", $dir, $timestamp, $uid);
        
        $data = [
            'uid' => $uid,
            'pks' => $pks,
            'message' => $message
        ];
        
        $fhandle = fopen($filename, 'w');
        fwrite($fhandle, json_encode($data));
        
        if ( !file_exists($filename) )
        
        return TRUE;
    }
    
    public function exists($uid)
    {
        return TRUE;
    }
    
    public function get($uid)
    {
        
    }
    
}

