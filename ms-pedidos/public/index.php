<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Middleware\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Configuración de la base de datos
require __DIR__ . '/../app/Config/Database.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// El AuthMiddleware solo aplica si NO es una petición OPTIONS
$app->add(function (Request $request, $handler) {
    if ($request->getMethod() === 'OPTIONS') {
        return $handler->handle($request);
    }
    $middleware = new \App\Middleware\AuthMiddleware();
    return $middleware($request, $handler);
});

// CORS (se agrega de último, se ejecuta primero)
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
