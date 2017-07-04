# dumbu-direct

Agregar esto en el cron para que el gestor de tareas corra sin problemas

* * * * * /opt/lampp/bin/php /home/usuario/dumbu-direct/app/direct-tasks/artisan schedule:run >> /dev/null 2>&1

# api de instagram

- Si da problemas de que no pudo ejecutarse un chmod, verificar que el directorio
vendor/mgp25/instagram-php/sessions tenga permisos de escritura (777).
