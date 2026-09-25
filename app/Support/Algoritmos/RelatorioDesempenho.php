<?php

namespace App\Support\Algoritmos;

/**
 * Monta o relatório de desempenho de cada algoritmo a partir das medições:
 * compara o crescimento observado com a complexidade esperada.
 */
class RelatorioDesempenho
{
    public const NOMES = [
        'insercao_ordenada' => 'INSERÇÃO ORDENADA',
        'bubble_sort' => 'BUBBLE SORT',
        'busca_linear' => 'BUSCA LINEAR',
        'busca_binaria' => 'BUSCA BINÁRIA',
    ];

    /** Complexidade esperada (teoria) por algoritmo e cenário. */
    private const ESPERADO = [
        'insercao_ordenada' => ['melhor' => 'O(1)', 'medio' => 'O(n)', 'pior' => 'O(n)'],
        'bubble_sort' => ['melhor' => 'O(n)', 'medio' => 'O(n²)', 'pior' => 'O(n²)'],
        'busca_linear' => ['linear' => 'O(n)'],
        'busca_binaria' => ['binaria' => 'O(log n)'],
    ];

    /** @var array<int, array{algoritmo:string,n:int,cenario:string,comparacoes:int,movimentacoes:int,tempo_ms:float}> */
    private array $medicoes = [];

    /** De onde vieram as leituras, mostrado no topo do HTML. O padrão descreve os dados simulados. */
    private string $fonte = 'Leituras simuladas a cada 5 minutos, semente fixa 42.';

    public function definirFonte(string $fonte): void
    {
        $this->fonte = $fonte;
    }

    public function adicionar(string $algoritmo, int $n, string $cenario, int $comparacoes, int $movimentacoes, float $ms): void
    {
        $this->medicoes[] = compact('algoritmo', 'n', 'cenario', 'comparacoes', 'movimentacoes') + ['tempo_ms' => round($ms, 3)];
    }

    public function medicoes(): array
    {
        return $this->medicoes;
    }

    /**
     * Classifica o crescimento pelo expoente k em  ops ~ n^k,
     * usando os dois maiores valores de n medidos.
     */
    public static function classificar(int $n1, int $ops1, int $n2, int $ops2): string
    {
        if ($ops1 <= 0 || $ops2 <= 0 || $n2 <= $n1) {
            return '-';
        }
        if ($ops2 === $ops1) {
            return 'O(1)';
        }
        $k = log($ops2 / $ops1) / log($n2 / $n1);

        return match (true) {
            $k < 0.5 => 'O(log n)',
            $k < 1.5 => 'O(n)',
            default => 'O(n²)',
        };
    }

    /**
     * Uma linha por cenário de cada algoritmo, com vários n. Cenários com "real"
     * no nome (seção 4, um único n) ficam de fora.
     *
     * @return array<string, array<int, array>> [algoritmo => linhas]
     */
    public function resumo(): array
    {
        $grupos = [];
        foreach ($this->medicoes as $m) {
            if (str_contains($m['cenario'], 'real')) {
                continue;
            }
            $grupos[$m['algoritmo']][$m['cenario']][$m['n']] = $m;
        }

        $saida = [];
        foreach ($grupos as $algoritmo => $cenarios) {
            foreach ($cenarios as $cenario => $porN) {
                ksort($porN);
                $ns = array_keys($porN);
                $primeiro = $porN[$ns[0]];
                $ultimo = $porN[end($ns)];
                $penultimo = $porN[$ns[max(0, count($ns) - 2)]];

                $observado = self::classificar($penultimo['n'], $penultimo['comparacoes'], $ultimo['n'], $ultimo['comparacoes']);
                $chave = strtok($cenario, ' ');
                $esperado = self::ESPERADO[$algoritmo][$chave] ?? '-';

                $saida[$algoritmo][] = [
                    'cenario' => $cenario,
                    'operacoes' => array_map(fn ($m) => $m['comparacoes'], $porN),
                    'tempos' => array_map(fn ($m) => $m['tempo_ms'], $porN),
                    'n_min' => $primeiro['n'],
                    'n_max' => $ultimo['n'],
                    'fator_n' => $ultimo['n'] / max(1, $primeiro['n']),
                    'fator_ops' => $ultimo['comparacoes'] / max(1, $primeiro['comparacoes']),
                    'observado' => $observado,
                    'esperado' => $esperado,
                    'confere' => $observado === $esperado,
                ];
            }
        }

        return $saida;
    }

    /** Relatório em HTML, com gráficos, para abrir no navegador e usar nos slides. */
    public function html(): string
    {
        $resumo = $this->resumo();
        $e = fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES);
        $secoes = '';
        $graficos = [];
        $id = 0;

        foreach ($resumo as $algoritmo => $linhas) {
            $ns = array_keys($linhas[0]['operacoes']);
            $cab = implode('', array_map(fn ($n) => '<th>n = '.number_format($n, 0, ',', '.').'</th>', $ns));
            $corpo = '';
            $series = [];
            foreach ($linhas as $l) {
                $cels = implode('', array_map(fn ($o) => '<td>'.number_format($o, 0, ',', '.').'</td>', $l['operacoes']));
                $corpo .= '<tr><td>'.$e($l['cenario']).'</td>'.$cels
                    .'<td>'.$e(number_format(end($l['tempos']), 3, ',', '.')).'</td>'
                    .'<td>'.$e($l['esperado']).'</td><td>'.$e($l['observado']).'</td>'
                    .'<td class="'.($l['confere'] ? 'ok' : 'nok').'">'.($l['confere'] ? 'Sim' : 'Não').'</td></tr>';
                $series[] = ['label' => $l['cenario'], 'data' => array_values($l['operacoes'])];
            }
            $graficos[] = ['id' => 'g'.$id, 'labels' => $ns, 'series' => $series];
            $nome = self::NOMES[$algoritmo] ?? $algoritmo;
            $secoes .= "<section><h2>{$e($nome)}</h2><table><thead><tr><th>Cenário</th>{$cab}"
                ."<th>Tempo no maior n (ms)</th><th>Esperado</th><th>Observado</th><th>Confere</th></tr></thead>"
                ."<tbody>{$corpo}</tbody></table><p class='nota'>Valores = comparações (ou verificações, nas buscas).</p>"
                ."<canvas id='g{$id}' height='110'></canvas></section>";
            $id++;
        }

        $reais = array_values(array_filter($this->medicoes, fn ($m) => str_contains($m['cenario'], 'real')));
        if ($reais) {
            $linhas = implode('', array_map(fn ($m) => '<tr><td>'.$e(self::NOMES[$m['algoritmo']] ?? $m['algoritmo'])
                .'</td><td>'.$e($m['cenario']).'</td><td>'.$m['n'].'</td><td>'.number_format($m['comparacoes'], 0, ',', '.')
                .'</td><td>'.number_format($m['movimentacoes'], 0, ',', '.').'</td><td>'.$e(number_format($m['tempo_ms'], 3, ',', '.')).'</td></tr>', $reais));
            $secoes .= "<section><h2>DADOS REAIS DO SENSOR</h2><table><thead><tr><th>Algoritmo</th><th>Cenário</th><th>n</th>"
                ."<th>Comparações</th><th>Movimentações</th><th>Tempo (ms)</th></tr></thead><tbody>{$linhas}</tbody></table></section>";
        }

        $json = json_encode($graficos);
        $data = date('d/m/Y H:i');
        $fonte = $e($this->fonte);

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="utf-8"><title>AgroTwin — Relatório de desempenho</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
body{font-family:Arial,sans-serif;color:#222;max-width:1000px;margin:24px auto;padding:0 16px}
h1{font-size:22px;margin-bottom:4px}h2{font-size:17px;border-bottom:2px solid #2E7D32;padding-bottom:4px;margin-top:36px}
table{border-collapse:collapse;width:100%;font-size:13px}th,td{border:1px solid #bbb;padding:5px 7px;text-align:center}
th{background:#f0f0f0}td:first-child{text-align:left}.ok{color:#1b5e20;font-weight:bold}.nok{color:#b71c1c;font-weight:bold}
.nota{font-size:12px;color:#555}
</style></head><body>
<h1>AgroTwin — Relatório de desempenho dos algoritmos</h1>
<p>Gerado em {$data} pelo comando <code>php artisan algoritmos:analisar</code>. {$fonte}
“Observado” é calculado pelo crescimento das operações entre os dois maiores valores de n.</p>
{$secoes}
<script>
for (const g of {$json}) {
  new Chart(document.getElementById(g.id), {type:'line',
    data:{labels:g.labels.map(n=>'n = '+n), datasets:g.series.map(s=>({label:s.label,data:s.data,tension:.2}))},
    options:{scales:{y:{type:'logarithmic',title:{display:true,text:'operações (escala log)'}}}}});
}
</script></body></html>
HTML;
    }
}
