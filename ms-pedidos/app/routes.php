<?php

use Slim\App;
use App\Controllers\PedidoController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Ruta de prueba para verificar que el microservicio funciona
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-pedidos funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Ruta para listar todos los pedidos
    $app->get('/pedidos', [PedidoController::class, 'index']);

    // Ruta para crear un nuevo pedido
    $app->post('/pedidos', [PedidoController::class, 'store']);

    // Ruta para cambiar el estado de un pedido
    $app->put('/pedidos/{id}', [PedidoController::class, 'update']);

    // Ruta para cancelar un pedido
    $app->delete('/pedidos/{id}', [PedidoController::class, 'destroy']);

    // Ruta para ver el detalle de un pedido
    $app->get('/pedidos/{id}', [PedidoController::class, 'show']);
};
