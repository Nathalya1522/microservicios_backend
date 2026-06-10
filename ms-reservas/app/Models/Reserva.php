<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla reservas en la base de datos
class Reserva extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'reservas';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre_cliente',
        'telefono_cliente',
        'cantidad_personas',
        'fecha',
        'hora',
        'observaciones',
        'estado',
        'mesa_id'
    ];

    // Relación con la mesa de la reserva
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }
}