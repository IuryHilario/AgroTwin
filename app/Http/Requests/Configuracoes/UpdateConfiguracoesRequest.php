<?php

namespace App\Http\Requests\Configuracoes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConfiguracoesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fl_notificar_email_alerta' => 'sometimes|boolean',
            'id_propriedade_padrao' => [
                'nullable',
                Rule::exists('propriedades', 'id_propriedade')->where('id_usuario', $this->user()->id_usuario),
            ],
        ];
    }

    public function messages()
    {
        return [
            'id_propriedade_padrao.exists' => 'A propriedade selecionada é inválida.',
        ];
    }
}
