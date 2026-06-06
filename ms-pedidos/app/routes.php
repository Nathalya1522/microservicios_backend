<?php

use Slim\App;
use App\Controllers\PedidoController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-pedidos funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Rutas de pedidos
    $app->get('/pedidos', [PedidoController::class, 'index']);
    $app->post('/pedidos', [PedidoController::class, 'store']);
    $app->put('/pedidos/{id}', [PedidoController::class, 'update']);
    $app->delete('/pedidos/{id}', [PedidoController::class, 'destroy']);
    $app->get('/pedidos/{id}', [PedidoController::class, 'show']);
};
