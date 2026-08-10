<?php

namespace App\Http\Requests\Propriedade;

use Illuminate\Foundation\Http\FormRequest;

class StorePropriedadeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ds_nome' => 'required|string|max:255',
            'nu_area_hectares' => 'required|numeric|min:0',
            'ds_localizacao' => 'nullable|string|max:500',
            'tp_solo' => 'nullable|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'ds_nome.required' => 'O nome da propriedade é obrigatório.',
            'nu_area_hectares.required' => 'A área da propriedade é obrigatória.',

            'ds_nome.string' => 'O nome da propriedade deve ser uma string.',
            'ds_localizacao.string' => 'O endereço da propriedade deve ser uma string.',

            'nu_area_hectares.numeric' => 'A área da propriedade deve ser um número.',

            'nu_area_hectares.min' => 'A área da propriedade deve ser um número positivo.',

            'ds_nome.max' => 'O nome da propriedade não pode exceder 255 caracteres.',
            'ds_localizacao.max' => 'O endereço da propriedade não pode exceder 500 caracteres.',
        ];
    }
}
