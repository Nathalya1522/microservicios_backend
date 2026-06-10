<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla categorias en la base de datos
class Categoria extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'categorias';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}