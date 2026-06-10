<?php

use Slim\App;
use App\Controllers\MesaController;
use App\Controllers\ReservaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Ruta de prueba para verificar que el microservicio funciona
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-reservas funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Ruta para listar todas las mesas
    $app->get('/mesas', [MesaController::class, 'index']);

    // Ruta para crear una nueva mesa
    $app->post('/mesas', [MesaController::class, 'store']);

    // Ruta para editar una mesa
    $app->put('/mesas/{id}', [MesaController::class, 'update']);

    // Ruta para eliminar una mesa
    $app->delete('/mesas/{id}', [MesaController::class, 'destroy']);

    // Ruta para listar todas las reservas
    $app->get('/reservas', [ReservaController::class, 'index']);

    // Ruta para crear una nueva reserva
    $app->post('/reservas', [ReservaController::class, 'store']);

    // Ruta para editar una reserva
    $app->put('/reservas/{id}', [ReservaController::class, 'update']);

    // Ruta para cancelar una reserva
    $app->delete('/reservas/{id}', [ReservaController::class, 'destroy']);
};