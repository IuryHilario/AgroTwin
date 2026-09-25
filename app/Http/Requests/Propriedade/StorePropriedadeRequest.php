<?php

namespace App\Http\Requests\Propriedade;

use App\Enums\TipoSolo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Cadastro e edição de propriedade (o mesmo formulário em etapas serve aos dois).
 *
 * As coordenadas são obrigatórias: é por elas que o Open-Meteo devolve a
 * previsão e a chance de chuva no ponto da lavoura.
 */
class StorePropriedadeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ds_nome' => 'required|string|max:255',
            'nu_area_hectares' => 'required|numeric|min:0',
            'tp_solo' => ['nullable', Rule::enum(TipoSolo::class)],
            'ds_localizacao' => 'required|string|max:500',
            'nu_latitude' => 'required|numeric|between:-90,90',
            'nu_longitude' => 'required|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'ds_nome.required' => 'O nome da propriedade é obrigatório.',
            'ds_nome.max' => 'O nome da propriedade não pode exceder 255 caracteres.',

            'nu_area_hectares.required' => 'A área da propriedade é obrigatória.',
            'nu_area_hectares.numeric' => 'A área da propriedade deve ser um número.',
            'nu_area_hectares.min' => 'A área da propriedade deve ser um número positivo.',

            'tp_solo.enum' => 'Escolha um tipo de solo da lista.',

            'ds_localizacao.required' => 'Dê um nome ao local (cidade ou endereço da fazenda).',
            'ds_localizacao.max' => 'O nome do local não pode exceder 500 caracteres.',

            'nu_latitude.required' => 'Marque a localização da propriedade no mapa.',
            'nu_longitude.required' => 'Marque a localização da propriedade no mapa.',
            'nu_latitude.between' => 'Latitude fora do intervalo válido (-90 a 90).',
            'nu_longitude.between' => 'Longitude fora do intervalo válido (-180 a 180).',
        ];
    }
}
