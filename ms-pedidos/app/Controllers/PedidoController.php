<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Pedido;
use App\Models\DetallePedido;

class PedidoController
{
    // Listar todos los pedidos
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $query = Pedido::with('detalles');

        // Filtrar por estado
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        $pedidos = $query->get();
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Ver detalle de un pedido
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

    // Crear un nuevo pedido
    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Validar que el pedido tenga al menos un producto
        if (empty($data['productos']) || count($data['productos']) === 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El pedido debe tener al menos un producto'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que la mesa no esté disponible
        // Para verificar el estado de la mesa, consultamos db_reservas
        try {
            $pdo = new \PDO(
                'mysql:host=127.0.0.1;dbname=db_reservas;charset=utf8',
                'root',
                ''
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare('SELECT estado FROM mesas WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $data['mesa_id']]);
            $mesa = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$mesa) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Mesa no encontrada'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            if ($mesa['estado'] === 'disponible') {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No se pueden registrar pedidos para mesas disponibles'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch (\Exception $e) {
            // Si no se puede verificar la mesa, se permite continuar
        }

        // Calcular subtotal y total
        $subtotal = 0;
        foreach ($data['productos'] as $producto) {
            // Validar que la cantidad sea mayor a cero
            if ($producto['cantidad'] < 1) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'La cantidad debe ser mayor a cero'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $subtotal += $producto['precio_unitario'] * $producto['cantidad'];
        }

        // Crear el pedido
        $pedido = Pedido::create([
            'mesa_id'  => $data['mesa_id'],
            'fecha'    => date('Y-m-d'),
            'hora'     => date('H:i:s'),
            'subtotal' => $subtotal,
            'total'    => $subtotal,
            'estado'   => 'pendiente'
        ]);

        // Crear los detalles del pedido
        foreach ($data['productos'] as $producto) {
            DetallePedido::create([
                'pedido_id'       => $pedido->id,
                'producto_id'     => $producto['producto_id'],
                'nombre_producto' => $producto['nombre_producto'],
                'cantidad'        => $producto['cantidad'],
                'precio_unitario' => $producto['precio_unitario'],
                'subtotal'        => $producto['precio_unitario'] * $producto['cantidad']
            ]);
        }

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Pedido creado correctamente',
            'data'    => $pedido->load('detalles')
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Cambiar estado del pedido
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
            'data'    => $pedido
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Cancelar un pedido
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