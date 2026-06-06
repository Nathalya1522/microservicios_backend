<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Pedido;
use App\Models\DetallePedido;

class PedidoController
{
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $query = Pedido::with('detalles');

        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        $pedidos = $query->get();
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args)
    {
        $pedido = Pedido::with('detalles')->find($args['id']);

        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($pedido));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (empty($data['productos']) || count($data['productos']) === 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El pedido debe tener al menos un producto'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Calcular subtotal y total
        $subtotal = 0;
        foreach ($data['productos'] as $producto) {
            if ($producto['cantidad'] < 1) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'La cantidad debe ser mayor a cero'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $subtotal += $producto['precio_unitario'] * $producto['cantidad'];
        }

        $pedido = Pedido::create([
            'mesa_id' => $data['mesa_id'],
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'estado' => 'pendiente'
        ]);

        foreach ($data['productos'] as $producto) {
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto['producto_id'],
                'nombre_producto' => $producto['nombre_producto'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $producto['precio_unitario'],
                'subtotal' => $producto['precio_unitario'] * $producto['cantidad']
            ]);
        }

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Pedido creado correctamente',
            'data' => $pedido->load('detalles')
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();
        $pedido->update(['estado' => $data['estado']]);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Estado del pedido actualizado correctamente',
            'data' => $pedido
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $pedido->update(['estado' => 'cancelado']);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Pedido cancelado correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}