<?php

namespace Tests\Feature;

use App\Models\Propriedade;
use App\Services\ClimaService;
use App\Services\LocalidadeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class OpenMeteoServicesTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    private const URL_GEOCODIFICACAO = 'geocoding-api.open-meteo.com/*';
    private const URL_PREVISAO = 'api.open-meteo.com/v1/forecast*';

    private function respostaGeocodificacao(): array
    {
        return ['results' => [
            ['name' => 'Rio Verde', 'latitude' => -17.79806, 'longitude' => -50.92806, 'admin1' => 'Goiás', 'country' => 'Brasil'],
            ['name' => 'Rio Verde', 'latitude' => 33.72254, 'longitude' => -111.67403, 'admin1' => 'Arizona', 'country' => 'EUA'],
        ]];
    }

    /** Recorte real da resposta do Open-Meteo (formato conferido na API). */
    private function respostaPrevisao(): array
    {
        return [
            'timezone' => 'America/Sao_Paulo',
            'current' => [
                'temperature_2m' => 25.2, 'relative_humidity_2m' => 64, 'apparent_temperature' => 27.6,
                'precipitation' => 0.0, 'weather_code' => 61, 'wind_speed_10m' => 6.4,
            ],
            'hourly' => ['precipitation_probability' => [3, 40, 71, 20, 1, 0]],
            'daily' => [
                'time' => ['2026-09-23', '2026-09-24', '2026-09-25'],
                'weather_code' => [53, 51, 2],
                'temperature_2m_max' => [30.0, 31.7, 34.3],
                'temperature_2m_min' => [19.8, 19.7, 20.7],
                'precipitation_sum' => [2.6, 0.8, 0.0],
                'precipitation_probability_max' => [81, 64, 10],
                'et0_fao_evapotranspiration' => [4.19, 5.12, 5.4],
            ],
        ];
    }

    // --- LocalidadeService ------------------------------------------------

    public function test_busca_devolve_municipios_com_rotulo_legivel(): void
    {
        Http::fake([self::URL_GEOCODIFICACAO => Http::response($this->respostaGeocodificacao())]);

        $resultados = app(LocalidadeService::class)->buscar('Rio Verde');

        $this->assertCount(2, $resultados);
        $this->assertSame('Rio Verde, Goiás, Brasil', $resultados[0]['rotulo']);
        $this->assertSame(-17.79806, $resultados[0]['latitude']);
        Http::assertSent(fn ($request) => $request['name'] === 'Rio Verde' && $request['language'] === 'pt');
    }

    public function test_busca_sem_resultado_devolve_lista_vazia(): void
    {
        // A API omite "results" quando não acha nada.
        Http::fake([self::URL_GEOCODIFICACAO => Http::response(['generationtime_ms' => 0.3])]);

        $this->assertSame([], app(LocalidadeService::class)->buscar('Xyzabc'));
    }

    public function test_falha_da_api_devolve_null_e_nao_vira_resultado_vazio_no_cache(): void
    {
        Http::fake([self::URL_GEOCODIFICACAO => Http::sequence()
            ->push('erro', 500)
            ->push($this->respostaGeocodificacao())]);

        $servico = app(LocalidadeService::class);

        $this->assertNull($servico->buscar('Rio Verde'));

        // A falha fica lembrada por pouco tempo, e passado esse tempo a busca volta a funcionar.
        $this->travel(3)->minutes();
        $this->assertCount(2, $servico->buscar('Rio Verde'));
    }

    public function test_termo_curto_nem_consulta_a_api(): void
    {
        Http::fake();

        $this->assertSame([], app(LocalidadeService::class)->buscar('a'));
        Http::assertNothingSent();
    }

    // --- ClimaService -----------------------------------------------------

    public function test_previsao_traduz_a_resposta_do_open_meteo(): void
    {
        Http::fake([self::URL_PREVISAO => Http::response($this->respostaPrevisao())]);

        $clima = app(ClimaService::class)->previsao(-17.78, -50.83);

        $this->assertSame(25, $clima['temperatura']);
        $this->assertSame('Chuva', $clima['descricao']);
        $this->assertSame('fa-cloud-rain', $clima['icone']);
        $this->assertSame(71, $clima['chuva_proximas_horas']);
        $this->assertSame(6, $clima['vento_kmh']);
        $this->assertCount(3, $clima['dias']);
        $this->assertSame(['Hoje', 'Amanhã'], [$clima['dias'][0]['rotulo'], $clima['dias'][1]['rotulo']]);
        $this->assertSame(81, $clima['dias'][0]['chance_chuva']);
        $this->assertSame(4.19, $clima['dias'][0]['et0_mm']);

        Http::assertSent(fn ($request) => $request['latitude'] == -17.78
            && str_contains($request['daily'], 'precipitation_probability_max')
            && $request['timezone'] === 'auto');
    }

    public function test_previsao_fica_em_cache(): void
    {
        Http::fake([self::URL_PREVISAO => Http::response($this->respostaPrevisao())]);
        $servico = app(ClimaService::class);

        $servico->previsao(-17.781, -50.831);
        $servico->previsao(-17.782, -50.832); // mesma célula de ~1 km

        Http::assertSentCount(1);
    }

    public function test_api_fora_do_ar_devolve_null(): void
    {
        Http::fake([self::URL_PREVISAO => Http::response('indisponível', 503)]);

        $this->assertNull(app(ClimaService::class)->previsao(-17.78, -50.83));
    }

    public function test_propriedade_com_coordenadas_consulta_direto_pelo_ponto(): void
    {
        Http::fake([self::URL_PREVISAO => Http::response($this->respostaPrevisao())]);
        $propriedade = $this->criarPropriedade($this->criarUsuario(), [
            'ds_localizacao' => 'Rio Verde',
            'nu_latitude' => -17.776903,
            'nu_longitude' => -50.834688,
        ]);

        $this->assertNotNull(app(ClimaService::class)->daPropriedade($propriedade));

        Http::assertSentCount(1);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'geocoding'));
    }

    public function test_propriedade_antiga_sem_coordenadas_cai_na_geocodificacao_da_cidade(): void
    {
        Http::fake([
            self::URL_GEOCODIFICACAO => Http::response($this->respostaGeocodificacao()),
            self::URL_PREVISAO => Http::response($this->respostaPrevisao()),
        ]);
        $propriedade = $this->criarPropriedade($this->criarUsuario(), ['ds_localizacao' => 'Rio Verde']);

        $this->assertNotNull(app(ClimaService::class)->daPropriedade($propriedade));

        Http::assertSent(fn ($request) => str_contains($request->url(), 'forecast') && $request['latitude'] == -17.79806);
    }

    public function test_seletor_do_dashboard_traz_as_coordenadas(): void
    {
        // Regressão: getPropriedadesByUsuario tinha uma lista fixa de colunas e
        // descartava latitude/longitude em silêncio.
        $usuario = $this->criarUsuario();
        $this->criarPropriedade($usuario, ['nu_latitude' => -17.5, 'nu_longitude' => -50.5]);

        $propriedade = Propriedade::byUsuario($usuario->id_usuario)->first();

        $this->assertSame(-17.5, $propriedade->nu_latitude);
        $this->assertSame(-50.5, $propriedade->nu_longitude);
    }
}
