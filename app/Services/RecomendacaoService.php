<?php

/**
 * Gera sugestões textuais simples (regras condicionais if/else), com base na
 * última leitura de cada sensor de uma lavoura comparada aos limites configurados.
 * Etapa anterior ao Machine Learning, como o próprio TCC propõe (seção 4.5).
 */

namespace App\Services;

use App\Enums\TipoSensor;
use App\Models\ConfiguracaoLimite;
use App\Models\Lavoura;
use App\Models\Recomendacao;
use App\Models\Sensor;

class RecomendacaoService
{
    public function __construct(private SensorReadingService $leituraService) {}

    /**
     * Avalia uma nova leitura e, se ela justificar uma recomendação, salva
     * no histórico da lavoura (tabela "recomendacoes"). Chamado a partir da
     * ingestão de leituras, junto com o AlertaService — mesmo ponto de
     * entrada, mesma lógica de comparação com os limites configurados.
     */
    public function avaliarERegistrar(Sensor $sensor, float $valor): ?Recomendacao
    {
        if (! $sensor->id_lavoura || ! $sensor->tp_sensor) {
            return null;
        }

        $limite = ConfiguracaoLimite::where('id_lavoura', $sensor->id_lavoura)
            ->where('tp_sensor', $sensor->tp_sensor->value)
            ->first();

        if (! $limite) {
            return null;
        }

        $sugestao = $this->sugerirPara($sensor->tp_sensor, $valor, $limite);
        if (! $sugestao) {
            return null;
        }

        return Recomendacao::create([
            'id_lavoura' => $sensor->id_lavoura,
            'tp_sensor' => $sensor->tp_sensor->value,
            'ds_recomendacao' => $sugestao,
            'nu_valor_leitura' => $valor,
            'nu_limite_min' => $limite->valor_min,
            'nu_limite_max' => $limite->valor_max,
            'dt_recomendacao' => now(),
        ]);
    }

    /**
     * @return string[] lista de sugestões textuais para a lavoura
     */
    public function gerarParaLavoura(Lavoura $lavoura): array
    {
        $recomendacoes = [];
        $limites = ConfiguracaoLimite::porLavoura($lavoura->id_lavoura);

        foreach ($lavoura->sensores as $sensor) {
            if (! $sensor->tp_sensor) {
                continue;
            }

            $limite = $limites->get($sensor->tp_sensor->value);
            if (! $limite) {
                continue;
            }

            $ultimaLeitura = $this->leituraService->ultimaLeitura($sensor);
            if (! $ultimaLeitura) {
                continue;
            }

            $sugestao = $this->sugerirPara($sensor->tp_sensor, $ultimaLeitura->valor, $limite);
            if ($sugestao) {
                $recomendacoes[] = $sugestao;
            }
        }

        return $recomendacoes;
    }

    private function sugerirPara(TipoSensor $tipo, float $valor, ConfiguracaoLimite $limite): ?string
    {
        $abaixoDoMinimo = $limite->valor_min !== null && $valor < $limite->valor_min;
        $acimaDoMaximo = $limite->valor_max !== null && $valor > $limite->valor_max;

        if (! $abaixoDoMinimo && ! $acimaDoMaximo) {
            return null;
        }

        return match ($tipo) {
            TipoSensor::UMIDADE_SOLO => $abaixoDoMinimo
                ? 'Umidade do solo baixa — considere irrigar a lavoura.'
                : 'Umidade do solo acima do ideal — reduza ou pause a irrigação.',
            TipoSensor::PH => $abaixoDoMinimo
                ? 'pH do solo baixo (ácido) — considere aplicar calcário.'
                : 'pH do solo alto (alcalino) — considere aplicar enxofre ou matéria orgânica.',
            TipoSensor::TEMPERATURA => $abaixoDoMinimo
                ? 'Temperatura do solo baixa — pode reduzir a absorção de nutrientes.'
                : 'Temperatura do solo alta — monitore o estresse térmico da cultura.',
            TipoSensor::NITROGENIO => $abaixoDoMinimo ? 'Nível de nitrogênio baixo — considere adubação nitrogenada.' : null,
            TipoSensor::FOSFORO => $abaixoDoMinimo ? 'Nível de fósforo baixo — considere adubação fosfatada.' : null,
            TipoSensor::POTASSIO => $abaixoDoMinimo ? 'Nível de potássio baixo — considere adubação potássica.' : null,
            TipoSensor::NPK => $abaixoDoMinimo ? 'Nível geral de NPK baixo — revise o plano de adubação.' : null,
            TipoSensor::CONDUTIVIDADE => $abaixoDoMinimo
                ? 'Condutividade elétrica baixa — solo com poucos nutrientes dissolvidos, considere adubação.'
                : 'Condutividade elétrica alta — risco de salinização do solo, reduza a adubação e monitore a irrigação.',
        };
    }
}
