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
            'id_propriedade' => 'required|exists:propriedades,id_propriedade',
            'nu_intervalo_leitura_minutos' => 'nullable|integer|min:1|max:1440',
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
            'nu_intervalo_leitura_minutos.integer' => 'O intervalo de leitura deve ser um número inteiro de minutos.',
            'nu_intervalo_leitura_minutos.min' => 'O intervalo de leitura deve ser de pelo menos 1 minuto.',
            'nu_intervalo_leitura_minutos.max' => 'O intervalo de leitura não pode exceder 1440 minutos (24 horas).',
        ];
    }
}
