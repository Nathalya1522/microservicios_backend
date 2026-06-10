<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo que representa la tabla detalles_pedidos en la base de datos
class DetallePedido extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'detalles_pedidos';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
}