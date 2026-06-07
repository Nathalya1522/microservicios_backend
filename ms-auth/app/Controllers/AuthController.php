<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;

class AuthController
{
    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $identificador = $data['usuario'] ?? '';
        $contrasena = $data['contrasena'] ?? '';

        $usuario = Usuario::where('usuario', $identificador)
            ->orWhere('correo', $identificador)
            ->where('estado', 'activo')
            ->first();

        if (!$usuario || $usuario->contrasena !== $contrasena) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Generar token simple
        $token = bin2hex(random_bytes(32));

        // Actualizar token y sesión
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'rol' => $usuario->rol
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response)
    {
        $headers = $request->getHeader('Authorization');
        $token = $headers[0] ?? null;

        if (!$token) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token no proporcionado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token inválido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Cerrar sesión
        $usuario->token = null;
        $usuario->sesion_activa = false;
        $usuario->save();

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}