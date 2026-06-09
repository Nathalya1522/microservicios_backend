<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Categoria;

class CategoriaController
{
    // Listar todas las categorias
    public function index(Request $request, Response $response)
    {
        // Obtener todas las categorias de la base de datos
        $categorias = Categoria::all();
        $response->getBody()->write(json_encode($categorias));
        return $response->withHeader('Content-Type', 'application/json');
    }
}