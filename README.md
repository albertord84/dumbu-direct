# dumbu-direct

Agregar esto en el cron para que el gestor de tareas corra sin problemas

* * * * * /opt/lampp/bin/php /home/usuario/dumbu-direct/app/direct-tasks/artisan schedule:run >> /dev/null 2>&1

# api de instagram

- Si da problemas de que no pudo ejecutarse un chmod, verificar que el directorio
vendor/mgp25/instagram-php/sessions tenga permisos de escritura (777).

# almacen de los mensajes

- Requiere permisos de 777, pues contendra archivos creados por el proceso Apache

# para autenticar en instagram

poner en el mismo directorio que el archivo test.php, un archivo que se
llame instagram_credentials que contenga solo una linea con el formato:

usuario:contraseña

no se incluye en el repositorio de fuentes para no revelar credenciales
de la cuenta o las cuentas que se usaran para enviar directs...
