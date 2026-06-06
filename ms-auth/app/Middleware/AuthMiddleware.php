<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $headers = $request->getHeader('Authorization');
        $token = $headers[0] ?? null;

        if (!$token) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $usuario = \Illuminate\Database\Capsule\Manager::table('usuarios')
            ->where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (!$usuario) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token inválido o sesión expirada']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}