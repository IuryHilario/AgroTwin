<?php

/**
 * Traduz as leituras de um período em diagnóstico agronômico: quanto tempo
 * cada parâmetro ficou dentro da faixa ideal da lavoura, para onde o valor
 * está indo e o que o produtor pode fazer a respeito.
 *
 * O relatório antes mostrava só mínimo/média/máximo — números que o produtor
 * precisava interpretar sozinho. Aqui esses números viram situação e ação.
 */

namespace App\Services;

use App\Enums\TipoSensor;
use App\Models\ConfiguracaoLimite;
use App\Models\Sensor;
use Illuminate\Support\Collection;

class DiagnosticoSoloService
{
    /** A partir deste percentual de leituras dentro da faixa o parâmetro está ok. */
    private const META_IDEAL = 85;

    /** Abaixo deste percentual o parâmetro é tratado como crítico. */
    private const LIMIAR_ATENCAO = 60;

    /** Variação mínima entre o início e o fim do período para virar tendência. */
    private const LIMIAR_TENDENCIA = 5;

    /**
     * @param  Collection  $relatorioSensores  saída de SensorReadingService::relatorioPorSensores()
     * @param  \App\Models\Lavoura|null  $lavoura  dona das faixas ideais
     * @return array{parametros: Collection, indice: ?int, situacaoGeral: array, acoes: Collection}
     */
    public function avaliar(Collection $relatorioSensores, $lavoura): array
    {
        $limites = $lavoura ? ConfiguracaoLimite::porLavoura($lavoura->id_lavoura) : collect();

        $parametros = $relatorioSensores
            ->map(fn (array $item) => $this->avaliarSensor($item, $limites))
            ->sortBy('ordem')
            ->values();

        return [
            'parametros' => $parametros,
            'indice' => $this->indiceDeSaude($parametros),
            'situacaoGeral' => $this->situacaoGeral($parametros),
            'acoes' => $this->acoes($parametros),
        ];
    }

    private function avaliarSensor(array $item, Collection $limites): array
    {
        /** @var Sensor $sensor */
        $sensor = $item['sensor'];
        $tipo = $sensor->tp_sensor;
        $limite = $tipo ? $limites->get($tipo->value) : null;
        $valores = $item['leituras']->pluck('valor');

        $faixa = $limite ? ['min' => $limite->valor_min, 'max' => $limite->valor_max] : null;
        $contagem = $this->contarPorFaixa($valores, $faixa);
        $percentualDentro = $valores->isEmpty() || !$faixa
            ? null
            : (int) round($contagem['dentro'] / $valores->count() * 100);

        $direcao = $contagem['abaixo'] === $contagem['acima']
            ? null
            : ($contagem['abaixo'] > $contagem['acima'] ? 'abaixo' : 'acima');

        $situacao = $this->classificar($valores->count(), $faixa, $percentualDentro);
        $tendencia = $this->tendencia($item['serie']);

        return [
            'sensor' => $sensor,
            'tipo' => $tipo,
            'rotulo' => $tipo?->label() ?? $sensor->ds_nome,
            'unidade' => $tipo?->unidade() ?? '',
            'resumo' => $item['resumo'],
            'serie' => $item['serie'],
            'faixa' => $faixa,
            'contagem' => $contagem,
            'percentualDentro' => $percentualDentro,
            'direcao' => $direcao,
            'situacao' => $situacao,
            'tendencia' => $tendencia,
            'diagnostico' => $this->diagnostico($situacao, $percentualDentro, $contagem, $direcao, $faixa, $item['resumo'], $tipo),
            'acao' => $this->acaoRecomendada($situacao, $direcao, $tipo),
            'ordem' => $this->ordem($situacao),
        ];
    }

    /**
     * Quantas leituras ficaram abaixo, dentro e acima da faixa ideal.
     */
    private function contarPorFaixa(Collection $valores, ?array $faixa): array
    {
        $contagem = ['abaixo' => 0, 'dentro' => 0, 'acima' => 0, 'total' => $valores->count()];

        if (!$faixa) {
            return $contagem;
        }

        foreach ($valores as $valor) {
            if ($faixa['min'] !== null && $valor < $faixa['min']) {
                $contagem['abaixo']++;
            } elseif ($faixa['max'] !== null && $valor > $faixa['max']) {
                $contagem['acima']++;
            } else {
                $contagem['dentro']++;
            }
        }

        return $contagem;
    }

    private function classificar(int $quantidade, ?array $faixa, ?int $percentualDentro): string
    {
        if ($quantidade === 0) {
            return 'sem_dados';
        }

        if (!$faixa) {
            return 'sem_limite';
        }

        return match (true) {
            $percentualDentro >= self::META_IDEAL => 'ideal',
            $percentualDentro >= self::LIMIAR_ATENCAO => 'atencao',
            default => 'critico',
        };
    }

    /** Parâmetros com problema aparecem primeiro na tela e no PDF. */
    private function ordem(string $situacao): int
    {
        return ['critico' => 0, 'atencao' => 1, 'ideal' => 2, 'sem_limite' => 3, 'sem_dados' => 4][$situacao];
    }

    /**
     * Compara a média da primeira metade do período com a da segunda para
     * dizer se o parâmetro está subindo, caindo ou estável.
     */
    private function tendencia(Collection $serieDiaria): ?array
    {
        if ($serieDiaria->count() < 4) {
            return null;
        }

        $valores = $serieDiaria->values();
        $meio = intdiv($valores->count(), 2);
        $inicio = $valores->take($meio)->avg();
        $fim = $valores->slice($meio)->avg();

        if (!$inicio) {
            return null;
        }

        $variacao = round(($fim - $inicio) / abs($inicio) * 100, 1);

        return [
            'sentido' => abs($variacao) < self::LIMIAR_TENDENCIA ? 'estavel' : ($variacao > 0 ? 'subindo' : 'caindo'),
            'variacao' => $variacao,
        ];
    }

    private function diagnostico(
        string $situacao,
        ?int $percentualDentro,
        array $contagem,
        ?string $direcao,
        ?array $faixa,
        array $resumo,
        ?TipoSensor $tipo
    ): string {
        if ($situacao === 'sem_dados') {
            return 'Nenhuma leitura chegou no período. Verifique se o sensor está ligado e conectado à rede.';
        }

        if ($situacao === 'sem_limite') {
            return 'Sem faixa ideal configurada para esta lavoura, então o sistema não consegue dizer se os valores estão bons nem gerar alertas.';
        }

        $unidade = $tipo?->unidade() ?? '';
        $media = $this->numero($resumo['media']) . $unidade;

        if ($situacao === 'ideal') {
            return "Dentro da faixa ideal em {$percentualDentro}% das leituras, com média de {$media}. Pode manter o manejo atual.";
        }

        $fora = $contagem['total'] - $contagem['dentro'];
        $percentualFora = 100 - $percentualDentro;
        $referencia = $direcao === 'acima'
            ? 'do máximo de ' . $this->numero($faixa['max']) . $unidade
            : 'do mínimo de ' . $this->numero($faixa['min']) . $unidade;

        $ladoTexto = $direcao ? ($direcao === 'acima' ? "acima {$referencia}" : "abaixo {$referencia}") : 'fora da faixa ideal';

        return "Ficou {$ladoTexto} em {$percentualFora}% do período ({$fora} de {$contagem['total']} leituras), com média de {$media}.";
    }

    /**
     * O que fazer na lavoura. Texto pensado para quem está no campo, não para
     * quem está lendo o banco: cada recomendação diz a consequência prática.
     */
    private function acaoRecomendada(string $situacao, ?string $direcao, ?TipoSensor $tipo): ?string
    {
        if ($situacao === 'sem_dados') {
            return 'Confira a alimentação e o sinal do ESP32 — sem leituras o sistema não gera alertas nem recomendações.';
        }

        if ($situacao === 'sem_limite') {
            return 'Defina a faixa ideal deste parâmetro em Lavouras › Limites, de acordo com a cultura plantada.';
        }

        if ($situacao === 'ideal' || !$tipo || !$direcao) {
            return null;
        }

        $baixo = $direcao === 'abaixo';

        return match ($tipo) {
            TipoSensor::UMIDADE_SOLO => $baixo
                ? 'Solo secando abaixo do ideal: aumente o tempo ou a frequência da irrigação e, se a irrigação automática estiver desligada, avalie ligá-la.'
                : 'Solo encharcado: reduza a lâmina de irrigação. Excesso de água tira oxigênio da raiz e lava o nitrogênio para fora da zona radicular.',
            TipoSensor::TEMPERATURA => $baixo
                ? 'Solo frio desacelera o crescimento e a absorção de nutrientes. Palhada ou cobertura morta ajuda a segurar o calor.'
                : 'Solo quente estressa a planta: mantenha cobertura morta e concentre a irrigação no início da manhã ou no fim da tarde.',
            TipoSensor::PH => $baixo
                ? 'Solo ácido: avalie calagem. Em pH baixo o fósforo fica indisponível mesmo que você adube, e o alumínio prejudica a raiz.'
                : 'Solo alcalino: avalie adubos de reação ácida ou enxofre. Em pH alto o ferro, o zinco e o manganês ficam indisponíveis.',
            TipoSensor::NITROGENIO => $baixo
                ? 'Nitrogênio baixo é o que limita folha e crescimento: avalie adubação nitrogenada em cobertura, parcelada.'
                : 'Nitrogênio alto: suspenda a adubação nitrogenada. O excesso atrasa a maturação, favorece doença e acaba lixiviado.',
            TipoSensor::FOSFORO => $baixo
                ? 'Fósforo baixo compromete raiz e enchimento de grão: avalie fosfatagem e confira o pH, porque em solo ácido o fósforo fica preso.'
                : 'Fósforo acima do necessário: reduza a dose na próxima adubação e economize insumo sem perder produtividade.',
            TipoSensor::POTASSIO => $baixo
                ? 'Potássio baixo reduz a resistência à seca e a qualidade do produto: avalie cloreto de potássio em cobertura.'
                : 'Potássio alto: reduza a dose. O excesso compete com cálcio e magnésio e pode induzir deficiência desses nutrientes.',
            TipoSensor::NPK => $baixo
                ? 'Fertilidade abaixo do ideal: avalie adubação de cobertura e confirme o resultado com uma análise de solo em laboratório.'
                : 'Fertilidade acima do ideal: revise as doses da próxima adubação antes de aplicar mais.',
            TipoSensor::CONDUTIVIDADE => $baixo
                ? 'Condutividade baixa indica solo pobre em sais solúveis: cruze com as leituras de N, P e K antes de definir a adubação.'
                : 'Condutividade alta indica excesso de sais ou adubo: suspenda a adubação e avalie uma irrigação mais longa para lixiviar o sal.',
        };
    }

    /**
     * Índice de saúde do solo: percentual médio de leituras dentro da faixa
     * ideal, considerando só os parâmetros que têm faixa configurada.
     */
    private function indiceDeSaude(Collection $parametros): ?int
    {
        $avaliaveis = $parametros->whereNotNull('percentualDentro');

        return $avaliaveis->isEmpty() ? null : (int) round($avaliaveis->avg('percentualDentro'));
    }

    private function situacaoGeral(Collection $parametros): array
    {
        $criticos = $parametros->where('situacao', 'critico')->count();
        $atencao = $parametros->where('situacao', 'atencao')->count();
        $ideais = $parametros->where('situacao', 'ideal')->count();

        [$nivel, $titulo, $texto] = match (true) {
            $parametros->whereIn('situacao', ['critico', 'atencao', 'ideal'])->isEmpty() => [
                'neutro',
                'Sem dados para avaliar',
                'Configure as faixas ideais da lavoura e confirme se os sensores estão enviando leituras.',
            ],
            $criticos > 0 => [
                'critico',
                $criticos === 1 ? '1 parâmetro exige ação' : "{$criticos} parâmetros exigem ação",
                ($criticos === 1 ? 'Ficou' : 'Ficaram') . ' fora da faixa ideal na maior parte do período. Veja as ações sugeridas abaixo.',
            ],
            $atencao > 0 => [
                'atencao',
                $atencao === 1 ? '1 parâmetro em atenção' : "{$atencao} parâmetros em atenção",
                ($atencao === 1 ? 'Passou' : 'Passaram') . ' parte do período fora da faixa ideal. Vale acompanhar de perto nos próximos dias.',
            ],
            default => [
                'ideal',
                'Solo dentro do esperado',
                $ideais === 1
                    ? 'O parâmetro monitorado ficou dentro da faixa ideal na maior parte do período.'
                    : "Os {$ideais} parâmetros monitorados ficaram dentro da faixa ideal na maior parte do período.",
            ],
        };

        return ['nivel' => $nivel, 'titulo' => $titulo, 'texto' => $texto, 'criticos' => $criticos, 'atencao' => $atencao, 'ideais' => $ideais];
    }

    /**
     * Lista de ações do período, dos parâmetros mais críticos para os menos.
     * Problemas de configuração e de sensor sem leitura viram uma única linha
     * cada, senão a mesma frase se repetiria para todo parâmetro.
     */
    private function acoes(Collection $parametros): Collection
    {
        $acoes = $parametros
            ->whereIn('situacao', ['critico', 'atencao'])
            ->map(fn (array $p) => [
                'rotulo' => $p['rotulo'],
                'sensor' => $p['sensor']->ds_nome,
                'situacao' => $p['situacao'],
                'texto' => $p['acao'],
            ])
            ->values();

        $semDados = $parametros->where('situacao', 'sem_dados');
        if ($semDados->isNotEmpty()) {
            $acoes->push([
                'rotulo' => 'Sensores sem leitura',
                'sensor' => $semDados->pluck('sensor.ds_nome')->implode(', '),
                'situacao' => 'sem_dados',
                'texto' => 'Nenhuma leitura chegou de ' . $this->listar($semDados->pluck('rotulo')->all())
                    . ' no período. Confira a alimentação e o sinal do ESP32.',
            ]);
        }

        $semLimite = $parametros->where('situacao', 'sem_limite');
        if ($semLimite->isNotEmpty()) {
            $acoes->push([
                'rotulo' => 'Faixas ideais não configuradas',
                'sensor' => $semLimite->pluck('sensor.ds_nome')->implode(', '),
                'situacao' => 'sem_limite',
                'texto' => 'Defina em Lavouras › Limites a faixa ideal de ' . $this->listar($semLimite->pluck('rotulo')->all())
                    . '. Sem isso o sistema não avalia essas leituras nem gera alertas.',
            ]);
        }

        return $acoes;
    }

    /** "pH, Temperatura e Fósforo" */
    private function listar(array $itens): string
    {
        if (count($itens) <= 1) {
            return (string) reset($itens);
        }

        $ultimo = array_pop($itens);

        return implode(', ', $itens) . ' e ' . $ultimo;
    }

    private function numero(?float $valor): string
    {
        return $valor === null ? '—' : rtrim(rtrim(number_format($valor, 1, ',', '.'), '0'), ',');
    }
}
