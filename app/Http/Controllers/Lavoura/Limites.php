<?php

namespace App\Http\Controllers\Lavoura;

use App\Enums\TipoSensor;
use App\Models\ConfiguracaoLimite;
use App\Support\FaixasSugeridas;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait Limites
{
    public function telaConfigurarLimites($id)
    {
        $lavoura = $this->buscarDoUsuario($this->model, $id);

        $dados = [
            'lavoura' => $lavoura,
            'limites' => ConfiguracaoLimite::porLavoura($id),
            'tiposSensor' => TipoSensor::cases(),
            'culturas' => FaixasSugeridas::opcoes(),
            'culturaSugerida' => FaixasSugeridas::adivinhar($lavoura->ds_cultura),
            'sugestoes' => FaixasSugeridas::paraFormulario(),
        ];

        return $this->responderView('lavouras.configurar-limites', $dados, $lavoura);
    }

    public function salvarLimites(Request $request, $id)
    {
        $lavoura = $this->buscarDoUsuario($this->model, $id);

        $dados = $request->validate([
            'limites' => 'array',
            'limites.*.valor_min' => 'nullable|numeric',
            'limites.*.valor_max' => 'nullable|numeric',
        ]);

        $this->validarIntervalos($dados['limites'] ?? []);

        ConfiguracaoLimite::salvarParaLavoura($lavoura->id_lavoura, $dados['limites'] ?? []);

        return redirect()->route('lavouras.index')
                        ->with('success', 'Limites da lavoura atualizados com sucesso!');
    }

    /**
     * Um mínimo maior que o máximo cria uma faixa impossível: todas as
     * leituras virariam alerta e o diagnóstico do relatório ficaria sempre
     * crítico. Melhor barrar na hora de salvar.
     */
    private function validarIntervalos(array $limites): void
    {
        $erros = [];

        foreach ($limites as $tpSensor => $valores) {
            $min = $valores['valor_min'] ?? null;
            $max = $valores['valor_max'] ?? null;

            if ($min !== null && $max !== null && $min !== '' && $max !== '' && (float) $min >= (float) $max) {
                $rotulo = TipoSensor::tryFrom($tpSensor)?->label() ?? $tpSensor;
                $erros["limites.{$tpSensor}.valor_min"] = "O mínimo de {$rotulo} precisa ser menor que o máximo.";
            }
        }

        if ($erros) {
            throw ValidationException::withMessages($erros);
        }
    }
}
