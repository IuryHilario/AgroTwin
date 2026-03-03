<?php

namespace App\Models;

use App\Entity\UsuarioEntity;
use App\Traits\UsesEntity;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(UsuarioEntity::class);
    }




}
