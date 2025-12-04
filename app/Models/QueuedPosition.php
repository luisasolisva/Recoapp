<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa una posición GPS pendiente de enviar a la API externa.
 * Se guarda el payload original para reintentar al pie de la letra.
 */
class QueuedPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'recorrido_id',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];
}
