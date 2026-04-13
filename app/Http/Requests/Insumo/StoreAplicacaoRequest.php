<?php

namespace App\Http\Requests\Insumo;

use Illuminate\Foundation\Http\FormRequest;

class StoreAplicacaoRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('dt_aplicacao')) {
            $dtAplicacao = $this->dt_aplicacao;
            if (strpos($dtAplicacao, 'T') !== false) {
                $dtAplicacao = str_replace('T', ' ', $dtAplicacao);
            }
            $this->merge(['dt_aplicacao' => $dtAplicacao]);
        }
    }

    public function rules()
    {
        return [
            'dt_aplicacao' => 'required|date_format:Y-m-d H:i',
            'id_lavoura' => 'required|exists:lavouras,id_lavoura',
            'nu_area_aplicada' => 'required|numeric|min:0.01',
            'nu_dosagem_hectare' => 'nullable|numeric|min:0.01',
            'nu_quantidade_aplicada' => 'required|numeric|min:0.01',
            'nu_concentracao' => 'nullable|numeric|min:0|max:100',
            'tp_metodo_aplicacao' => 'required',
            'ds_equipamento' => 'nullable|string|max:255',
            'ds_responsavel' => 'required|string|max:255',
            'tp_finalidade' => 'nullable',
            'ds_observacoes' => 'nullable|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'dt_aplicacao.required' => 'A data de aplicação é obrigatória.',
            'dt_aplicacao.date' => 'A data de aplicação deve ser uma data válida.',

            'id_lavoura.required' => 'A lavoura é obrigatória.',
            'id_lavoura.exists' => 'A lavoura selecionada é inválida.',

            'nu_area_aplicada.required' => 'A área aplicada é obrigatória.',
            'nu_area_aplicada.numeric' => 'A área aplicada deve ser um número.',
            'nu_area_aplicada.min' => 'A área aplicada deve ser no mínimo 0.01 ha.',

            'nu_dosagem_hectare.numeric' => 'A dosagem por hectare deve ser um número.',
            'nu_dosagem_hectare.min' => 'A dosagem por hectare deve ser no mínimo 0.01.',

            'nu_quantidade_aplicada.required' => 'A quantidade aplicada é obrigatória.',
            'nu_quantidade_aplicada.numeric' => 'A quantidade aplicada deve ser um número.',
            'nu_quantidade_aplicada.min' => 'A quantidade aplicada deve ser no mínimo 0.01.',

            'nu_concentracao.numeric' => 'A concentração deve ser um número.',
            'nu_concentracao.min' => 'A concentração deve ser no mínimo 0%.',
            'nu_concentracao.max' => 'A concentração deve ser no máximo 100%.',

            'tp_metodo_aplicacao.required' => 'O método de aplicação é obrigatório.',

            'ds_equipamento.string' => 'O equipamento deve ser uma string.',
            'ds_equipamento.max' => 'O equipamento deve ter no máximo 255 caracteres.',

            'ds_responsavel.required' => 'O responsável pela aplicação é obrigatório.',
            'ds_responsavel.string' => 'O responsável pela aplicação deve ser uma string.',
            'ds_responsavel.max' => 'O responsável pela aplicação deve ter no máximo 255 caracteres.',

            'ds_observacoes.string' => 'As observações devem ser uma string.',
            'ds_observacoes.max' => 'As observações devem ter no máximo 255 caracteres.'
        ];
    }
}