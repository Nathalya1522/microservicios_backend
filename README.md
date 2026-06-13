INSTALACIÓN DEL PROYECTO

1. Instalar XAMPP (PHP 8+ y MySQL).

2. Instalar Composer desde:
https://getcomposer.org/download/

3. Verificar Composer:

composer -V

4. Descargar el proyecto desde GitHub en formato ZIP.

5. Extraer el archivo ZIP en cualquier ubicación del computador (no es necesario usar htdocs).

6. Iniciar Apache y MySQL desde XAMPP.

7. Abrir phpMyAdmin y crear las bases de datos:

db_auth
db_reservas
db_productos
db_pedidos

8. Importar los archivos SQL correspondientes a cada base de datos.

9. Abrir una terminal en cada microservicio e instalar dependencias:

cd ms-auth
composer install

cd ../ms-reservas
composer install

cd ../ms-productos
composer install

cd ../ms-pedidos
composer install

10. Ejecutar los microservicios:

Terminal 1:
cd ms-auth
php -S 127.0.0.1:8001 -t public

Terminal 2:
cd ms-reservas
php -S 127.0.0.1:8002 -t public

Terminal 3:
cd ms-productos
php -S 127.0.0.1:8003 -t public

Terminal 4:
cd ms-pedidos
php -S 127.0.0.1:8004 -t public

ruta de el fronent
php -S 127.0.0.1:5500

11. Verificar funcionamiento:

http://127.0.0.1:8001
http://127.0.0.1:8002
http://127.0.0.1:8003
http://127.0.0.1:8004

12. Ingresar con las credenciales de prueba:

Administrador:
Usuario: admin
Contraseña: admin123

Empleado:
Usuario: empleado
Contraseña: empleado123