<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Mesa;

class MesaController
{
    // Listar todas las mesas
    public function index(Request $request, Response $response)
    {
        $mesas = Mesa::all();
        $response->getBody()->write(json_encode($mesas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Crear una nueva mesa
    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Validar que los campos no estén vacíos
        if (empty($data['numero']) || empty($data['capacidad'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Número y capacidad son obligatorios'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que la capacidad sea mayor a cero
        if ($data['capacidad'] <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'La capacidad debe ser mayor a cero'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que no exista una mesa con el mismo número
        if (Mesa::where('numero', $data['numero'])->exists()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ya existe una mesa con ese número'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Crear la mesa
        $mesa = Mesa::create([
            'numero' => $data['numero'],
            'capacidad' => $data['capacidad'],
            'estado' => $data['estado'] ?? 'disponible'
        ]);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Mesa creada correctamente',
            'data' => $mesa
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Editar una mesa
    public function update(Request $request, Response $response, $args)
    {
        // Buscar la mesa por id
        $mesa = Mesa::find($args['id']);

        if (!$mesa) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();

        // Validar que la capacidad sea mayor a cero
        if (isset($data['capacidad']) && $data['capacidad'] <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'La capacidad debe ser mayor a cero'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Actualizar la mesa
        $mesa->update($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Mesa actualizada correctamente',
            'data' => $mesa
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Eliminar una mesa
    public function destroy(Request $request, Response $response, $args)
    {
        // Buscar la mesa por id
        $mesa = Mesa::find($args['id']);

        if (!$mesa) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Eliminar la mesa
        $mesa->delete();

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Mesa eliminada correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}