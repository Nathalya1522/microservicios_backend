<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Crear una nueva instancia de Capsule para manejar la conexión
$capsule = new Capsule;

// Configurar la conexión a la base de datos db_productos
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'db_productos',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Hacer la conexión disponible globalmente
$capsule->setAsGlobal();

// Iniciar Eloquent ORM
$capsule->bootEloquent();