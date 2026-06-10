<?php

use Slim\App;
use App\Controllers\AuthController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Ruta de prueba para verificar que el microservicio funciona
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'ms-auth funcionando']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Ruta para iniciar sesión
    $app->post('/login', [AuthController::class, 'login']);

    // Ruta para cerrar sesión
    $app->post('/logout', [AuthController::class, 'logout']);
};