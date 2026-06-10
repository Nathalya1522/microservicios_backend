<?php

use Slim\App;
use App\Controllers\ProductoController;
use App\Controllers\CategoriaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Ruta de prueba para verificar que el microservicio funciona
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-productos funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Ruta para listar todas las categorias
    $app->get('/categorias', [CategoriaController::class, 'index']);

    // Ruta para listar todos los productos
    $app->get('/productos', [ProductoController::class, 'index']);

    // Ruta para crear un nuevo producto
    $app->post('/productos', [ProductoController::class, 'store']);

    // Ruta para editar un producto
    $app->put('/productos/{id}', [ProductoController::class, 'update']);

    // Ruta para eliminar un producto
    $app->delete('/productos/{id}', [ProductoController::class, 'destroy']);
};