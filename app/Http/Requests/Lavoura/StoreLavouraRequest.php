<?php

namespace App\Http\Requests\Lavoura;

use Illuminate\Foundation\Http\FormRequest;

class StoreLavouraRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'ds_cultura' => 'required|string|max:255',
            'dt_plantio' => 'required|date',
            'dt_colheita' => 'required|date|after_or_equal:dt_plantio',
            'ds_observacao' => 'nullable|string',
            'id_propriedade' => 'required|exists:propriedade,id_propriedade',
        ];
    }

    public function messages()
    {
        return [
            'ds_cultura.required' => 'A cultura é obrigatória.',
            'dt_plantio.required' => 'A data de plantio é obrigatória.',
            'dt_colheita.required' => 'A data de colheita é obrigatória.',
            'id_propriedade.required' => 'A propriedade é obrigatória.',

            'ds_observacao.string' => 'A observação deve ser uma string.',
            'ds_cultura.string' => 'A cultura deve ser uma string.',

            'dt_plantio.date' => 'A data de plantio deve ser uma data válida.',
            'dt_colheita.date' => 'A data de colheita deve ser uma data válida.',

            'dt_colheita.after_or_equal' => 'A data de colheita deve ser igual ou posterior à data de plantio.',
            'ds_cultura.max' => 'A cultura não pode exceder 255 caracteres.',
        ];
    }
}
