<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla usuarios en la base de datos
class Usuario extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'usuarios';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'correo',
        'usuario',
        'contrasena',
        'rol',
        'token',
        'sesion_activa',
        'estado'
    ];
}