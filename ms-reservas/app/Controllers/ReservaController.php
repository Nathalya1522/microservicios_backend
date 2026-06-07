<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Reserva;
use App\Models\Mesa;

class ReservaController
{
    // Listar todas las reservas
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $query = Reserva::with('mesa');

        // Filtrar por fecha
        if (!empty($params['fecha'])) {
            $query->where('fecha', $params['fecha']);
        }
        // Filtrar por estado
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }
        // Filtrar por cliente
        if (!empty($params['cliente'])) {
            $query->where('nombre_cliente', 'like', '%' . $params['cliente'] . '%');
        }

        $reservas = $query->get();
        $response->getBody()->write(json_encode($reservas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Crear una nueva reserva
    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Validar que la fecha no sea pasada
        if ($data['fecha'] < date('Y-m-d')) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No se permiten reservas en fechas pasadas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que la mesa exista
        $mesa = Mesa::find($data['mesa_id']);
        if (!$mesa) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Validar que la mesa no esté fuera de servicio
        if ($mesa->estado === 'fuera_servicio') {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'La mesa está fuera de servicio'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar capacidad de la mesa
        if ($data['cantidad_personas'] > $mesa->capacidad) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'La cantidad de personas supera la capacidad de la mesa'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que no exista doble reserva
        $reservaExistente = Reserva::where('mesa_id', $data['mesa_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->where('estado', '!=', 'cancelada')
            ->first();

        if ($reservaExistente) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ya existe una reserva para esa mesa en esa fecha y hora'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Crear la reserva
        $reserva = Reserva::create($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Reserva creada correctamente',
            'data' => $reserva
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Editar una reserva
    public function update(Request $request, Response $response, $args)
    {
        // Buscar la reserva por id
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Reserva no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Actualizar los datos
        $data = $request->getParsedBody();
        $reserva->update($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Reserva actualizada correctamente',
            'data' => $reserva
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Cancelar una reserva
    public function destroy(Request $request, Response $response, $args)
    {
        // Buscar la reserva por id
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Reserva no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Cambiar estado a cancelada
        $reserva->update(['estado' => 'cancelada']);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Reserva cancelada correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}