<?php

namespace App\Http\Requests\Lavoura;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLavouraRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ds_cultura' => 'required|string|max:255',
            'ds_observacao' => 'nullable|string',
            'id_propriedade' => 'required|exists:propriedade,id_propriedade',
        ];
    }

    public function messages()
    {
        return [
            'ds_cultura.required' => 'A cultura é obrigatória.',
            'id_propriedade.required' => 'A propriedade é obrigatória.',

            'ds_observacao.string' => 'A observação deve ser uma string.',
            'ds_cultura.string' => 'A cultura deve ser uma string.',

            'ds_cultura.max' => 'A cultura não pode exceder 255 caracteres.',

        ];
    }
}
