<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla mesas en la base de datos
class Mesa extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'mesas';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'numero',
        'capacidad',
        'estado'
    ];
}
