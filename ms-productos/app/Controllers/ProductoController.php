<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Producto;

class ProductoController
{
    // Listar todos los productos
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $query = Producto::with('categoria');

        // Filtrar por categoria
        if (!empty($params['categoria_id'])) {
            $query->where('categoria_id', $params['categoria_id']);
        }
        // Filtrar por disponibilidad
        if (isset($params['disponible'])) {
            $query->where('disponible', $params['disponible']);
        }

        $productos = $query->get();
        $response->getBody()->write(json_encode($productos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Crear un nuevo producto
    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Validar que el nombre no esté vacío
        if (empty($data['nombre'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El nombre es obligatorio'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que el precio sea mayor a cero
        if ($data['precio'] <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El precio debe ser mayor a cero'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que no exista un producto con el mismo nombre
        if (Producto::where('nombre', $data['nombre'])->exists()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ya existe un producto con ese nombre'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Crear el producto
        $producto = Producto::create($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data' => $producto
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Editar un producto
    public function update(Request $request, Response $response, $args)
    {
        // Buscar el producto por id
        $producto = Producto::find($args['id']);

        if (!$producto) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();

        // Validar que el precio sea mayor a cero
        if (isset($data['precio']) && $data['precio'] <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El precio debe ser mayor a cero'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Actualizar el producto
        $producto->update($data);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Eliminar un producto
    public function destroy(Request $request, Response $response, $args)
    {
        // Buscar el producto por id
        $producto = Producto::find($args['id']);

        if (!$producto) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Eliminar el producto
        $producto->delete();

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}