<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla productos en la base de datos
class Producto extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'productos';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'disponible',
        'categoria_id'
    ];

    // Relación con la categoria del producto
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}