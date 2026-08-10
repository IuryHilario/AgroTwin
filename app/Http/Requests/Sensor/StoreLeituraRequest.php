<?php

namespace App\Http\Requests\Sensor;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLeituraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'valor' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'valor.required' => 'O valor da leitura é obrigatório.',
            'valor.numeric' => 'O valor da leitura deve ser numérico.',
        ];
    }

    /**
     * Endpoint consumido por um dispositivo (ESP32), não por navegador —
     * sempre responder em JSON, nunca redirecionar para uma página "anterior".
     */
    protected function failedValidation(ValidatorContract $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
