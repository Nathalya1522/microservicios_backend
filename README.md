# Sistema Web de Reservas y Pedidos - Restaurante XYZ

## Descripción
Plataforma web para administrar reservas de mesas y gestión de pedidos del Restaurante XYZ, desarrollada bajo una arquitectura basada en microservicios.

## Tecnologías
- PHP 8+
- Slim Framework 4
- Eloquent ORM
- MySQL
- JavaScript Vanilla
- HTML5 y CSS3

## Microservicios
| Microservicio | Puerto | Base de datos |
|---------------|--------|---------------|
| ms-auth | 8001 | db_auth |
| ms-reservas | 8002 | db_reservas |
| ms-productos | 8003 | db_productos |
| ms-pedidos | 8004 | db_pedidos |

## Requisitos previos
- XAMPP instalado (PHP 8+ y MySQL)
- Composer instalado
- Git instalado

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/Nathalya1522/microservicios_backend.git
```

### 2. Crear las bases de datos
Abrir phpMyAdmin en `http://localhost/phpmyadmin` e importar los archivos SQL:
- `ms-auth/db_auth.sql` → crear base de datos `db_auth`
- `ms-reservas/db_reservas.sql` → crear base de datos `db_reservas`
- `ms-productos/db_productos.sql` → crear base de datos `db_productos`
- `ms-pedidos/db_pedidos.sql` → crear base de datos `db_pedidos`

### 3. Instalar dependencias
Ejecutar en cada carpeta de microservicio:
```bash
cd ms-auth
composer install

cd ../ms-reservas
composer install

cd ../ms-productos
composer install

cd ../ms-pedidos
composer install
```

### 4. Ejecutar los microservicios
Abrir 4 terminales y ejecutar uno en cada una:
```bash
cd ms-auth && php -S 127.0.0.1:8001 -t public
cd ms-reservas && php -S 127.0.0.1:8002 -t public
cd ms-productos && php -S 127.0.0.1:8003 -t public
cd ms-pedidos && php -S 127.0.0.1:8004 -t public
```

### 5. Verificar que funcionan
Abrir en el navegador:
- http://127.0.0.1:8001
- http://127.0.0.1:8002
- http://127.0.0.1:8003
- http://127.0.0.1:8004

## Credenciales de prueba
| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | admin123 | administrador |
| empleado | empleado123 | empleado |

## Autor
Nathalia Osorio