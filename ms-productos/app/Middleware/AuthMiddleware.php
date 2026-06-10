<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    // Se ejecuta antes de llegar al controlador
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Obtener el token del header Authorization
        $headers = $request->getHeader('Authorization');
        $token = $headers[0] ?? null;

        // Validar que el token exista
        if (!$token) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Buscar el usuario con ese token y sesión activa
        $usuario = \Illuminate\Database\Capsule\Manager::table('usuarios')
            ->where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        // Si el token no es válido o la sesión expiró
        if (!$usuario) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token inválido o sesión expirada']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Si todo está bien, continuar al controlador
        return $handler->handle($request);
    }
}