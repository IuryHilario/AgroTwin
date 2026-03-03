<?php

namespace App\Http\Requests\Insumo;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstoqueRequest extends FormRequest
{
    public function rules()
    {
        return [
            'tp_controle' => 'required',
            'nu_quantidade' => 'required|numeric|min:0.01',
            'dt_movimentacao' => 'required|date',
            'vl_unitario' => 'required|numeric|min:0',
            'ds_documento' => 'nullable|string|max:100',
            'ds_fornecedor' => 'nullable|string|max:100',
            'ds_observacao' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'tp_controle.required' => 'O tipo de movimentação é obrigatório.',
            'nu_quantidade.required' => 'A quantidade é obrigatória.',
            'dt_movimentacao.required' => 'A data da movimentação é obrigatória.',
            'vl_unitario.required' => 'O valor unitário é obrigatório.',

            'nu_quantidade.numeric' => 'A quantidade deve ser um número.',
            'vl_unitario.numeric' => 'O valor unitário deve ser um número.',

            'nu_quantidade.min' => 'A quantidade deve ser no mínimo 0.01.',
            'vl_unitario.min' => 'O valor unitário deve ser no mínimo 0.',

            'dt_movimentacao.date' => 'A data da movimentação deve ser uma data válida.',

            'ds_documento.string' => 'O documento deve ser uma string.',
            'ds_fornecedor.string' => 'O fornecedor/destino deve ser uma string.',
            'ds_observacao.string' => 'A observação deve ser uma string.',

            'ds_documento.max' => 'O documento não pode exceder 100 caracteres.',
            'ds_fornecedor.max' => 'O fornecedor/destino não pode exceder 100 caracteres.',
            'ds_observacao.max' => 'A observação não pode exceder 500 caracteres.',
        ];
    }
}
