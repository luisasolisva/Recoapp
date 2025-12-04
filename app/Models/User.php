<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo usuario mínimo: solo se usa para emitir tokens Sanctum.
 * La placa del camión se usa como "username" para simplificar el login.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'placa',
        'perfil_id',
        'password'
    ];

    protected $hidden = ['password'];
}
