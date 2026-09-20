<?php

namespace App\Support;

use App\Enums\TipoSensor;
use Illuminate\Support\Str;

/**
 * Faixas de referência por cultura para a tela de limites.
 *
 * Sem elas o produtor encontra 16 campos numéricos em branco e acaba não
 * configurando nada — foi o que aconteceu na base de homologação, onde só a
 * umidade tinha faixa e o resto do sistema ficava cego.
 *
 * São valores de partida de literatura agronômica geral, para solo. Servem
 * como sugestão editável, não substituem análise de solo nem recomendação de
 * agrônomo — a tela deixa isso explícito.
 *
 * Atenção ao que cada parâmetro mede: 'temperatura' aqui é a do SOLO, lida pela
 * sonda, não a do ar. Faixas de crescimento aéreo (alface 15–24 °C, por exemplo)
 * ficariam permanentemente violadas num solo de cerrado.
 */
class FaixasSugeridas
{
    /**
     * [tipo de sensor => [mínimo, máximo]] por cultura.
     */
    private const CULTURAS = [
        'alface' => [
            'rotulo' => 'Alface',
            'faixas' => [
                'umidade_solo' => [60, 80],
                'temperatura' => [18, 28],
                'ph' => [6.0, 6.8],
                'nitrogenio' => [20, 40],
                'fosforo' => [15, 30],
                'potassio' => [60, 120],
                'npk' => [20, 40],
                'condutividade' => [800, 1600],
            ],
        ],
        'tomate' => [
            'rotulo' => 'Tomate',
            'faixas' => [
                'umidade_solo' => [60, 80],
                'temperatura' => [18, 30],
                'ph' => [5.5, 6.8],
                'nitrogenio' => [30, 60],
                'fosforo' => [20, 40],
                'potassio' => [100, 180],
                'npk' => [30, 60],
                'condutividade' => [1500, 2500],
            ],
        ],
        'soja' => [
            'rotulo' => 'Soja',
            'faixas' => [
                'umidade_solo' => [50, 75],
                'temperatura' => [20, 32],
                'ph' => [5.5, 6.5],
                'nitrogenio' => [15, 30],
                'fosforo' => [10, 20],
                'potassio' => [60, 120],
                'npk' => [15, 30],
                'condutividade' => [500, 1500],
            ],
        ],
        'milho' => [
            'rotulo' => 'Milho',
            'faixas' => [
                'umidade_solo' => [55, 75],
                'temperatura' => [20, 32],
                'ph' => [5.5, 6.5],
                'nitrogenio' => [25, 50],
                'fosforo' => [12, 25],
                'potassio' => [70, 140],
                'npk' => [25, 50],
                'condutividade' => [600, 1600],
            ],
        ],
        'feijao' => [
            'rotulo' => 'Feijão',
            'faixas' => [
                'umidade_solo' => [55, 75],
                'temperatura' => [18, 30],
                'ph' => [5.5, 6.5],
                'nitrogenio' => [20, 40],
                'fosforo' => [12, 25],
                'potassio' => [60, 120],
                'npk' => [20, 40],
                'condutividade' => [500, 1400],
            ],
        ],
        'cafe' => [
            'rotulo' => 'Café',
            'faixas' => [
                'umidade_solo' => [50, 70],
                'temperatura' => [18, 28],
                'ph' => [5.0, 6.0],
                'nitrogenio' => [25, 50],
                'fosforo' => [10, 20],
                'potassio' => [80, 150],
                'npk' => [25, 50],
                'condutividade' => [600, 1500],
            ],
        ],
        'cana' => [
            'rotulo' => 'Cana-de-açúcar',
            'faixas' => [
                'umidade_solo' => [50, 75],
                'temperatura' => [20, 34],
                'ph' => [5.5, 6.5],
                'nitrogenio' => [20, 45],
                'fosforo' => [10, 20],
                'potassio' => [80, 160],
                'npk' => [20, 45],
                'condutividade' => [600, 1600],
            ],
        ],
        'padrao' => [
            'rotulo' => 'Hortaliças em geral',
            'faixas' => [
                'umidade_solo' => [55, 80],
                'temperatura' => [18, 30],
                'ph' => [5.5, 6.8],
                'nitrogenio' => [20, 40],
                'fosforo' => [15, 30],
                'potassio' => [70, 140],
                'npk' => [20, 40],
                'condutividade' => [700, 1800],
            ],
        ],
    ];

    /**
     * Culturas para o seletor da tela: ['alface' => 'Alface', ...].
     *
     * @return array<string, string>
     */
    public static function opcoes(): array
    {
        return collect(self::CULTURAS)->map(fn (array $cultura) => $cultura['rotulo'])->all();
    }

    /**
     * Faixas de uma cultura: ['umidade_solo' => ['min' => 60, 'max' => 80], ...].
     *
     * @return array<string, array{min: float, max: float}>
     */
    public static function daCultura(string $chave): array
    {
        $faixas = (self::CULTURAS[$chave] ?? self::CULTURAS['padrao'])['faixas'];

        return collect($faixas)
            ->map(fn (array $faixa) => ['min' => $faixa[0], 'max' => $faixa[1]])
            ->all();
    }

    /**
     * Tenta adivinhar a cultura pelo texto digitado na lavoura ("Alface
     * crespa" → alface), para o seletor já abrir na opção certa.
     */
    public static function adivinhar(?string $dsCultura): string
    {
        $texto = Str::of($dsCultura ?? '')->lower()->ascii();

        foreach (array_keys(self::CULTURAS) as $chave) {
            if ($chave !== 'padrao' && $texto->contains($chave)) {
                return $chave;
            }
        }

        return 'padrao';
    }

    /**
     * Sugestões no formato consumido pelo JS da tela, já com todos os tipos de
     * sensor conhecidos (os sem referência vêm vazios).
     */
    public static function paraFormulario(): array
    {
        $tipos = array_map(fn (TipoSensor $tipo) => $tipo->value, TipoSensor::cases());

        return collect(self::CULTURAS)
            ->map(function (array $cultura) use ($tipos) {
                $faixas = [];
                foreach ($tipos as $tipo) {
                    $faixas[$tipo] = isset($cultura['faixas'][$tipo])
                        ? ['min' => $cultura['faixas'][$tipo][0], 'max' => $cultura['faixas'][$tipo][1]]
                        : null;
                }

                return $faixas;
            })
            ->all();
    }
}
