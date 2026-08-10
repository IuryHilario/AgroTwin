<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UsuarioEntity
{
    public const TABLE = 'usuarios';

    public const PRIMARY_KEY = 'id_usuario';

    public const FILLABLE = [
        'name',
        'email',
        'password',
        'remember_token',
    ];

    public const CASTS = [
        'email_verified_at' => 'datetime',
    ];


    public static function getUsuario()
    {
        return Auth::user();
    }

    public static function getUsuarioByIdUsuario(Builder $query, $id)
    {
        return $query->select('id_usuario', 'name', 'email')
            ->where('id_usuario', $id);
    }

    public static function getNomeUsuarioById(?int $id): ?string
    {
        if (!$id) {
            return null;
        }

        return DB::table('usuarios')
            ->where('id_usuario', $id)
            ->value('name');
    }
}
