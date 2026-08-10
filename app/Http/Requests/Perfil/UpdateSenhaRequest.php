<?php

namespace App\Http\Requests\Perfil;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateSenhaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'senha_atual.required' => 'Informe sua senha atual.',
            'nova_senha.required' => 'Informe a nova senha.',
            'nova_senha.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('senha_atual') && !Hash::check($this->senha_atual, $this->user()->password)) {
                $validator->errors()->add('senha_atual', 'A senha atual informada está incorreta.');
            }
        });
    }
}
