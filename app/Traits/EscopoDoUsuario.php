<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Multi-tenant: todo acesso a um registro nasce filtrado pelo usuário logado.
 *
 * Sem isso, telas que faziam `Model::findOrFail($id)` deixavam qualquer usuário
 * autenticado abrir (e alterar) o registro de outro produtor só trocando o id
 * da URL — no caso dos sensores isso expunha inclusive o token do ESP32.
 */
trait EscopoDoUsuario
{
    /**
     * Consulta do model já restrita ao usuário.
     *
     * Sensores e lavouras herdam o dono da propriedade; propriedades e insumos
     * têm `id_usuario` na própria tabela.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     */
    protected function consultaDoUsuario(string $model, ?int $idUsuario = null): Builder
    {
        $idUsuario ??= Auth::id();

        if (method_exists($model, 'propriedade')) {
            return $model::whereHas('propriedade', fn (Builder $query) => $query->where('id_usuario', $idUsuario));
        }

        return $model::where('id_usuario', $idUsuario);
    }

    /**
     * Registro do usuário ou 404 — nunca "registro de outro usuário".
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     */
    protected function buscarDoUsuario(string $model, $id, ?int $idUsuario = null)
    {
        return $this->consultaDoUsuario($model, $idUsuario)
            ->where((new $model)->getKeyName(), $id)
            ->firstOrFail();
    }
}
