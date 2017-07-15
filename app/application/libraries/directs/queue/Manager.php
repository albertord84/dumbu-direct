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
            'message' => htmlentities( $message )
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
        // Perfil de control para que el mensaje se envie de todas formas
        // a find e saber que la cola estan trabajando bien
        if (strcmp($pk, "3670825632")===0) return FALSE;
        
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
     * Devuelve el tamano de la pagina de mensajes a procesar.
     * - Si la cantidad de mensajes es menor o igual a 10, se procesaran 1 a 1
     * - Si son mas de 10, y menos de 100, se procesaran de 10 en 10
     * - Si son mas de 100, se procesaran de 50 en 50
     * 
     * @return int Tamano de la pagina a procesar
     */
    public function get_page_size()
    {
        if (TRUE) return 10;
        
        $r = 1;
        $c = $this->queue_count();
        
        if ($c <= 10) $r = 1;
        if ($c > 10 && $c < 100) $r = 10;
        if ($c > 50) $r = 50;
        
        echo sprintf('Tamano de la pagina de mensajes a procesar: %s' . PHP_EOL,
                $r);
        
        return $r;
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
        $cmd = sprintf("ls %s | grep -v error | tail -n %s | head -n %s", 
                APPPATH . '/logs/directs/queue', 
                $start == 0 ? 1 : $start, $count);

        $cmd_out = shell_exec($cmd);
        
        $resp = explode( PHP_EOL, trim( $cmd_out ) );
        
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
        echo sprintf('Desplazando puntero de la cola hacia pagina %s...' . PHP_EOL, 
                intval($page) + 1);
        $last = APPPATH . '/logs/directs/last.json';
        $json_data = json_decode( file_get_contents(APPPATH . '/logs/directs/queue/' . $filename) );
        $last_msg = $this->last_msg();
        if (strcmp($filename, $last_msg)==0) { // Reiniciar la cola desde el primer mensaje
            $filename = $this->first_msg();
            $json_data = json_decode( file_get_contents(APPPATH . '/logs/directs/queue/' . $filename) );
            $json_data->page = 1;
        }
        else {
            $json_data->page = ++$page;
        }
        file_put_contents( $last, json_encode($json_data) );
        echo sprintf('Puntero de la cola desplazado exitosamente a %s' . PHP_EOL,
                $json_data->page);
    }
    
    /**
     * Devuelve el nombre del archivo del ultimo mensaje en la cola
     * 
     * @return string Nombre del archivo
     */
    public function last_msg()
    {
        echo sprintf('Obteniendo ultimo mensaje en la cola...' . PHP_EOL);
        $cmd = sprintf("ls %s | grep -v error | tail -n 1", 
                APPPATH . '/logs/directs/queue');
        return trim( shell_exec($cmd) );
    }
    
    /**
     * Devuelve el nombre del archivo del primer mensaje en la cola
     * 
     * @return string Nombre del archivo
     */
    public function first_msg()
    {
        echo sprintf('Obteniendo primer mensaje en la cola...' . PHP_EOL);
        $cmd = sprintf("ls %s | grep -v error | head -n 1", 
                APPPATH . '/logs/directs/queue');
        return trim( shell_exec($cmd) );
    }
    
}

