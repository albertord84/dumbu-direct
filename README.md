# dumbu-direct

Agregar esto en el cron para que el gestor de tareas corra sin problemas

* * * * * /opt/lampp/bin/php /home/usuario/dumbu-direct/app/direct-tasks/artisan schedule:run >> /dev/null 2>&1
