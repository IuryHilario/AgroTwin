<?php

namespace App\Http\Requests\Sensor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSensorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ds_nome' => 'required|string|max:255',
            'tp_sensor' => 'required|string',
            'id_propriedade' => 'required|exists:propriedades,id_propriedade',
            'id_lavoura' => [
                'required',
                Rule::exists('lavouras', 'id_lavoura')->where('id_propriedade', $this->id_propriedade),
            ],
        ];
    }

    public function messages()
    {
        return [
            'ds_nome.required' => 'O nome do sensor é obrigatório.',
            'ds_nome.max' => 'O nome do sensor não pode exceder 255 caracteres.',
            'tp_sensor.required' => 'O tipo do sensor é obrigatório.',
            'id_propriedade.required' => 'A propriedade é obrigatória.',
            'id_propriedade.exists' => 'A propriedade selecionada é inválida.',
            'id_lavoura.required' => 'A lavoura é obrigatória.',
            'id_lavoura.exists' => 'A lavoura selecionada não pertence à propriedade escolhida.',
        ];
    }
}
