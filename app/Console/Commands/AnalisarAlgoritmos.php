<?php

namespace App\Console\Commands;

use App\Models\LeituraSensor;
use App\Models\Sensor;
use App\Support\Algoritmos\BubbleSort;
use App\Support\Algoritmos\BuscaLeituras;
use App\Support\Algoritmos\ListaLeiturasOrdenada;
use App\Support\Algoritmos\RelatorioDesempenho;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AnalisarAlgoritmos extends Command
{
    // Comando: php artisan algoritmos:analisar

    protected $signature = 'algoritmos:analisar
        {--tamanhos=10,100,1000,5000 : Valores de n separados por vírgula}
        {--sensor= : ID de um sensor: usa só as leituras dele e roda também a seção 4 (ordem de chegada)}
        {--simulado : Usa leituras simuladas nas seções 1, 2 e 3 em vez da tabela leituras_sensor}';

    protected $description = 'Inserção Ordenada, Bubble Sort e buscas (Análise de Complexidade) sobre leituras reais — exibe e exporta CSV';

    /** Leituras usadas na seção 4 quando o banco não é a fonte das seções 1 a 3 (comportamento antigo). */
    private const LIMITE_SECAO_4_SIMULADO = 2000;

    private array $csv = [];

    private RelatorioDesempenho $relatorio;

    private bool $simulado = false;

    /** Leituras do banco já convertidas, em ordem de dt_leitura e depois id_leitura. */
    private array $reais = [];

    public function handle(): int
    {
        $tamanhos = array_map('intval', explode(',', (string) $this->option('tamanhos')));
        $idSensor = $this->option('sensor') !== null ? (int) $this->option('sensor') : null;
        $this->simulado = (bool) $this->option('simulado');
        $this->relatorio = new RelatorioDesempenho();

        // Sem --simulado o banco é a fonte das seções 1 a 3; com --sensor a seção 4
        // também precisa dele. Em ambos os casos, uma única consulta.
        if (! $this->simulado || $idSensor !== null) {
            $limite = $this->simulado ? self::LIMITE_SECAO_4_SIMULADO : max($tamanhos);
            $modelos = $this->carregarLeituras($idSensor, $limite);
        }

        if (! $this->simulado) {
            if (count($this->reais) < 2) {
                $this->error('A tabela leituras_sensor tem menos de 2 leituras'.($idSensor !== null ? " para o sensor {$idSensor}" : '').'. Use --simulado para rodar com dados simulados.');

                return self::FAILURE;
            }

            $this->relatorio->definirFonte($this->mostrarFonte($modelos));
            $tamanhos = $this->tamanhosDisponiveis($tamanhos);

            if (! $tamanhos) {
                $this->error('Nenhum tamanho de --tamanhos cabe nas '.count($this->reais).' leituras do banco. Informe valores menores.');

                return self::FAILURE;
            }
        }

        $this->insercaoOrdenada($tamanhos);
        $this->bubbleSort($tamanhos);
        $this->buscas($tamanhos);

        if ($idSensor !== null) {
            $this->dadosReais($idSensor);
        }

        $this->mostrarRelatorio();

        $carimbo = now()->format('Ymd_His');
        $csv = "algoritmos/resultados_{$carimbo}.csv";
        $html = "algoritmos/relatorio_{$carimbo}.html";
        Storage::disk('local')->put($csv, "algoritmo;n;cenario;comparacoes;movimentacoes;tempo_ms\n".implode("\n", $this->csv)."\n");
        Storage::disk('local')->put($html, $this->relatorio->html());
        $this->newLine();
        $this->info('CSV salvo em:       '.Storage::disk('local')->path($csv));
        $this->info('Relatório (HTML):   '.Storage::disk('local')->path($html));

        return self::SUCCESS;
    }

    /**
     * Uma só consulta, somente leitura: as leituras mais antigas primeiro, com
     * id_leitura desempatando leituras do mesmo instante. Só são necessárias as
     * primeiras max(n) leituras, então o limite evita carregar a tabela inteira.
     *
     * @return \Illuminate\Support\Collection<int, LeituraSensor>
     */
    private function carregarLeituras(?int $idSensor, int $limite)
    {
        $modelos = LeituraSensor::query()
            ->select(['id_leitura', 'id_sensor', 'valor', 'dt_leitura'])
            ->when($idSensor !== null, fn ($query) => $query->where('id_sensor', $idSensor))
            ->orderBy('dt_leitura')
            ->orderBy('id_leitura')
            ->limit($limite)
            ->get();

        $this->reais = $modelos
            ->map(fn (LeituraSensor $l) => ['timestamp' => $l->dt_leitura->getTimestamp(), 'valor' => (float) $l->valor])
            ->all();

        return $modelos;
    }

    /** Mostra de onde vieram os dados e devolve o mesmo texto para o HTML. */
    private function mostrarFonte($modelos): string
    {
        $ids = $modelos->pluck('id_sensor')->unique()->sort()->values();
        $nomes = Sensor::whereIn('id_sensor', $ids)->pluck('ds_nome', 'id_sensor');
        $sensores = $ids->map(fn ($id) => $id.' ('.($nomes[$id] ?? 'sem nome').')')->implode(', ');
        $primeira = $modelos->first()->dt_leitura->format('d/m/Y H:i:s');
        $ultima = $modelos->last()->dt_leitura->format('d/m/Y H:i:s');
        $quantidade = number_format($modelos->count(), 0, ',', '.');

        $this->info('FONTE DOS DADOS — tabela leituras_sensor');
        $this->line('  Sensor(es):  '.$sensores);
        $this->line("  Leituras:    {$quantidade} (ordenadas por dt_leitura, depois id_leitura)");
        $this->line("  Período:     {$primeira} a {$ultima}");
        $this->newLine();

        return "Fonte: tabela leituras_sensor — sensor(es) {$sensores}; {$quantidade} leituras reais, de {$primeira} a {$ultima}, "
            .'em ordem de dt_leitura e depois id_leitura. Cada n usa as n primeiras leituras.';
    }

    /** Pula (com aviso) os tamanhos maiores que a quantidade de leituras reais. Nada é inventado. */
    private function tamanhosDisponiveis(array $tamanhos): array
    {
        $total = count($this->reais);

        return array_values(array_filter($tamanhos, function (int $n) use ($total) {
            if ($n <= $total) {
                return true;
            }
            $this->warn("n = {$n} pulado: o banco tem só {$total} leituras para esta seleção.");

            return false;
        }));
    }

    /** As n leituras de cada seção: as n primeiras do banco ou n simuladas. */
    private function leituras(int $n): array
    {
        return $this->simulado ? $this->gerar($n) : array_slice($this->reais, 0, $n);
    }

    /** n leituras, uma a cada 5 minutos, em ordem cronológica. */
    private function gerar(int $n): array
    {
        mt_srand(42 + $n);
        $lista = [];
        for ($i = 0; $i < $n; $i++) {
            $lista[] = ['timestamp' => 1_758_000_000 + $i * 300, 'valor' => round(mt_rand(200, 800) / 10, 1)];
        }

        return $lista;
    }

    private function registrar(array &$linhas, string $algoritmo, int $n, string $cenario, int $cmp, int $mov, float $ms): void
    {
        $linhas[] = [$n, $cenario, $cmp, $mov, round($ms, 3)];
        $this->csv[] = implode(';', [$algoritmo, $n, $cenario, $cmp, $mov, round($ms, 3)]);
        $this->relatorio->adicionar($algoritmo, $n, $cenario, $cmp, $mov, $ms);
    }

    /** Relatório de desempenho de cada algoritmo: crescimento observado x esperado. */
    private function mostrarRelatorio(): void
    {
        foreach ($this->relatorio->resumo() as $algoritmo => $linhas) {
            $this->newLine();
            $this->info('==== RELATÓRIO DE DESEMPENHO — '.RelatorioDesempenho::NOMES[$algoritmo].' ====');
            $this->table(
                ['cenário', 'n', 'operações', 'tempo no maior n (ms)', 'esperado', 'observado', 'confere'],
                array_map(fn ($l) => [
                    $l['cenario'],
                    $l['n_min'].' -> '.$l['n_max'],
                    number_format(reset($l['operacoes']), 0, ',', '.').' -> '.number_format(end($l['operacoes']), 0, ',', '.'),
                    number_format(end($l['tempos']), 3, ',', '.'),
                    $l['esperado'],
                    $l['observado'],
                    $l['confere'] ? 'sim' : 'NAO',
                ], $linhas)
            );
            foreach ($linhas as $l) {
                $this->line(sprintf('  %s: n cresceu %sx e as operações cresceram %sx.',
                    $l['cenario'], number_format($l['fator_n'], 0, ',', '.'), number_format($l['fator_ops'], 1, ',', '.')));
            }
        }
    }

    private function insercaoOrdenada(array $tamanhos): void
    {
        $this->info('1) INSERÇÃO ORDENADA — inserir UMA leitura numa lista com n - 1 leituras');
        $linhas = [];
        // Cenário => posição da leitura inserida por último (0 = mais antiga, n - 1 = mais recente).
        // Os nomes não podem conter "real": o resumo reserva essa palavra para a seção 4.
        $rotulos = $this->simulado
            ? ['melhor (final)', 'medio (meio)', 'pior (inicio)']
            : ['melhor (mais recente)', 'medio (do meio)', 'pior (mais antiga)'];
        foreach ($tamanhos as $n) {
            $base = $this->leituras($n);
            $cenarios = array_combine($rotulos, [$n - 1, intdiv($n, 2), 0]);
            foreach ($cenarios as $cenario => $posicao) {
                $nova = $base[$posicao];
                $lista = new ListaLeiturasOrdenada();
                // Exclui pela posição, não pelo valor: duas leituras reais podem ter o mesmo par timestamp/valor.
                foreach ($base as $i => $l) {
                    if ($i !== $posicao) {
                        $lista->inserirOrdenado($l['timestamp'], $l['valor']);
                    }
                }
                $t0 = hrtime(true);
                $lista->inserirOrdenado($nova['timestamp'], $nova['valor']);
                $ms = (hrtime(true) - $t0) / 1e6;
                $this->registrar($linhas, 'insercao_ordenada', $n, $cenario, $lista->comparacoes, $lista->deslocamentos, $ms);
            }
        }
        $this->table(['n', 'cenário', 'comparações', 'deslocamentos', 'tempo (ms)'], $linhas);
    }

    private function bubbleSort(array $tamanhos): void
    {
        $this->info('2) BUBBLE SORT — ordenar um lote de n leituras por timestamp');
        $linhas = [];
        $rotulos = $this->simulado
            ? ['melhor (ja ordenada)', 'medio (aleatoria)', 'pior (invertida)']
            : ['melhor (cronologica)', 'medio (embaralhada)', 'pior (invertida)'];
        foreach ($tamanhos as $n) {
            $base = $this->leituras($n);
            $aleatoria = $base;
            if (! $this->simulado) {
                mt_srand(42); // embaralhamento reprodutível sobre os dados reais
            }
            shuffle($aleatoria);
            $cenarios = array_combine($rotulos, [$base, $aleatoria, array_reverse($base)]);
            foreach ($cenarios as $cenario => $lote) {
                $bs = new BubbleSort();
                $t0 = hrtime(true);
                $bs->ordenar($lote);
                $ms = (hrtime(true) - $t0) / 1e6;
                $this->registrar($linhas, 'bubble_sort', $n, $cenario, $bs->comparacoes, $bs->trocas, $ms);
            }
        }
        $this->table(['n', 'cenário', 'comparações', 'trocas', 'tempo (ms)'], $linhas);
    }

    private function buscas(array $tamanhos): void
    {
        $this->info('3) ESTRUTURAS DE BUSCA — procurar a ÚLTIMA leitura (pior caso da linear)');
        $linhas = [];
        $empates = [];
        $busca = new BuscaLeituras();
        foreach ($tamanhos as $n) {
            $lista = $this->leituras($n);
            $alvo = $lista[$n - 1]['timestamp'];
            foreach (['linear', 'binaria'] as $tipo) {
                $t0 = hrtime(true);
                $busca->$tipo($lista, $alvo);
                $ms = (hrtime(true) - $t0) / 1e6;
                $this->registrar($linhas, 'busca_'.$tipo, $n, $tipo.' (ultima leitura)', $busca->verificacoes, 0, $ms);
            }

            $ocorrencias = count(array_keys(array_column($lista, 'timestamp'), $alvo, true));
            if ($ocorrencias > 1) {
                $empates[] = "n = {$n} ({$ocorrencias} leituras)";
            }
        }
        $this->table(['n', 'busca', 'verificações', '-', 'tempo (ms)'], $linhas);

        // Sensores diferentes enviados no mesmo segundo: a linear para na primeira ocorrência.
        if ($empates) {
            $this->line('  Obs.: o instante da última leitura se repete em '.implode(', ', $empates)
                .' — leituras de sensores diferentes no mesmo segundo. A linear para na primeira ocorrência.');
        }
    }

    /** Os 3 algoritmos sobre as leituras reais de um sensor, na ordem em que chegaram. */
    private function dadosReais(int $idSensor): void
    {
        $reais = $this->reais;
        $n = count($reais);

        if ($n < 2) {
            $this->warn("Sensor {$idSensor}: menos de 2 leituras no banco.");

            return;
        }

        $this->info("4) DADOS REAIS — sensor {$idSensor}, n = {$n} leituras");
        $linhas = [];

        $lista = new ListaLeiturasOrdenada();
        $cmp = $mov = 0;
        $t0 = hrtime(true);
        foreach ($reais as $l) {
            $lista->inserirOrdenado($l['timestamp'], $l['valor']);
            $cmp += $lista->comparacoes;
            $mov += $lista->deslocamentos;
        }
        $this->registrar($linhas, 'insercao_ordenada', $n, 'real (chegada)', $cmp, $mov, (hrtime(true) - $t0) / 1e6);

        $bs = new BubbleSort();
        $t0 = hrtime(true);
        $porValor = $bs->ordenar($reais, 'valor');
        $this->registrar($linhas, 'bubble_sort', $n, 'real (por valor)', $bs->comparacoes, $bs->trocas, (hrtime(true) - $t0) / 1e6);

        $busca = new BuscaLeituras();
        $ordenadas = $lista->leituras();
        $alvo = $ordenadas[$n - 1]['timestamp'];
        foreach (['linear', 'binaria'] as $tipo) {
            $t0 = hrtime(true);
            $busca->$tipo($ordenadas, $alvo);
            $this->registrar($linhas, 'busca_'.$tipo, $n, $tipo.' real (ultima)', $busca->verificacoes, 0, (hrtime(true) - $t0) / 1e6);
        }

        $this->table(['n', 'cenário', 'comparações', 'movimentações', 'tempo (ms)'], $linhas);
        $this->line("Menor valor: {$porValor[0]['valor']} | Maior valor: {$porValor[$n - 1]['valor']}");
    }
}
