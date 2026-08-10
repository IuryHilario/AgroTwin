<?php

namespace App\Http\Requests\Insumo;

use Illuminate\Foundation\Http\FormRequest;

class StoreInsumoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ds_nome' => 'required|string|max:100',
            'tp_insumo' => 'required|',
            'tp_unidade_medida' => 'required',
            'nu_estoque_minimo' => 'required|numeric|min:0',
            'ds_composicao' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'ds_nome.required' => 'O nome do insumo é obrigatório.',
            'tp_insumo.required' => 'O tipo do insumo é obrigatório.',
            'tp_unidade_medida.required' => 'A unidade de medida é obrigatória.',
            'nu_estoque_minimo.required' => 'A quantidade em estoque é obrigatória.',

            'ds_composicao.string' => 'A composição deve ser uma string.',
            'ds_nome.string' => 'O nome do insumo deve ser uma string.',

            'nu_estoque_minimo.numeric' => 'A quantidade em estoque deve ser um número.',

            'nu_estoque_minimo.min' => 'A quantidade em estoque deve ser um número positivo.',

            'ds_nome.max' => 'O nome do insumo não pode exceder 100 caracteres.',
        ];
    }
}
