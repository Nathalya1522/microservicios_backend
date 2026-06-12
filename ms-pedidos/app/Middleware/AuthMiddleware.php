<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Obtener el token del header Authorization
        $headers = $request->getHeader('Authorization');
        $token   = $headers[0] ?? null;

        // Validar que el token exista
        if (!$token) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Conectar a db_auth con PDO para validar el token
        try {
            $pdo = new \PDO(
                'mysql:host=127.0.0.1;dbname=db_auth;charset=utf8',
                'root',
                ''
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare(
                'SELECT id FROM usuarios WHERE token = :token AND sesion_activa = 1 LIMIT 1'
            );
            $stmt->execute([':token' => $token]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Error interno de autenticación']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        if (!$usuario) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token inválido o sesión expirada']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}