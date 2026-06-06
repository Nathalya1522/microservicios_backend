<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Producto;

class ProductoController
{
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $query = Producto::with('categoria');

        if (!empty($params['categoria_id'])) {
            $query->where('categoria_id', $params['categoria_id']);
        }
        if (isset($params['disponible'])) {
            $query->where('disponible', $params['disponible']);
        }

        $productos = $query->get();
        $response->getBody()->write(json_encode($productos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (empty($data['nombre'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El nombre es obligatorio'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($data['precio'] <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El precio debe ser mayor a cero'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (Producto::where('nombre', $data['nombre'])->exists()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ya existe un producto con ese nombre'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $producto = Producto::create($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data' => $producto
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $producto = Producto::find($args['id']);

        if (!$producto) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();

        if (isset($data['precio']) && $data['precio'] <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El precio debe ser mayor a cero'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $producto->update($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $producto = Producto::find($args['id']);

        if (!$producto) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $producto->delete();

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}