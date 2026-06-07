<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;

class AuthController
{
    // Iniciar sesión
    public function login(Request $request, Response $response)
    {
        // Obtener los datos enviados por el frontend
        $data = $request->getParsedBody();
        $identificador = $data['usuario'] ?? '';
        $contrasena = $data['contrasena'] ?? '';

        // Buscar usuario por usuario o correo
        $usuario = Usuario::where('usuario', $identificador)
            ->orWhere('correo', $identificador)
            ->where('estado', 'activo')
            ->first();

        // Validar que el usuario exista y la contraseña sea correcta
        if (!$usuario || $usuario->contrasena !== $contrasena) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Generar token simple
        $token = bin2hex(random_bytes(32));

        // Actualizar token y sesión en la base de datos
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        // Retornar respuesta al frontend
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

    // Cerrar sesión
    public function logout(Request $request, Response $response)
    {
        // Obtener el token del header
        $headers = $request->getHeader('Authorization');
        $token = $headers[0] ?? null;

        // Validar que el token exista
        if (!$token) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token no proporcionado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Buscar el usuario con ese token
        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token inválido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Invalidar el token y cerrar la sesión
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