<?php

use Slim\App;
use App\Controllers\MesaController;
use App\Controllers\ReservaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-reservas funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Rutas de mesas
    $app->get('/mesas', [MesaController::class, 'index']);
    $app->post('/mesas', [MesaController::class, 'store']);
    $app->put('/mesas/{id}', [MesaController::class, 'update']);
    $app->delete('/mesas/{id}', [MesaController::class, 'destroy']);

    // Rutas de reservas
    $app->get('/reservas', [ReservaController::class, 'index']);
    $app->post('/reservas', [ReservaController::class, 'store']);
    $app->put('/reservas/{id}', [ReservaController::class, 'update']);
    $app->delete('/reservas/{id}', [ReservaController::class, 'destroy']);
};