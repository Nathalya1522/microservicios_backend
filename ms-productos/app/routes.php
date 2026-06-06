<?php

use Slim\App;
use App\Controllers\ProductoController;
use App\Controllers\CategoriaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-productos funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Rutas de categorias
    $app->get('/categorias', [CategoriaController::class, 'index']);

    // Rutas de productos
    $app->get('/productos', [ProductoController::class, 'index']);
    $app->post('/productos', [ProductoController::class, 'store']);
    $app->put('/productos/{id}', [ProductoController::class, 'update']);
    $app->delete('/productos/{id}', [ProductoController::class, 'destroy']);
};