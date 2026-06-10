<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla pedidos en la base de datos
class Pedido extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'pedidos';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'mesa_id',
        'fecha',
        'hora',
        'subtotal',
        'total',
        'estado'
    ];

    // Relación con los detalles del pedido
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }
}