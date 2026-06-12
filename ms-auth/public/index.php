<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Configuración de la base de datos
require __DIR__ . '/../app/Config/Database.php';

// Crear la app
$app = AppFactory::create();

// AGREGAR ESTA LÍNEA - permite parsear JSON del body
$app->addBodyParsingMiddleware();

// Configuración de CORS
$app->options('/{routes:.+}', fn($req, $res) => $res);
$app->add(function (Request $request, $handler) {
    $origin = $request->getHeaderLine('Origin') ?: '*';
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

// Cargar rutas
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();