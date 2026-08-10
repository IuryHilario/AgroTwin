<?php

namespace Tests\Concerns;

use App\Models\ConfiguracaoLimite;
use App\Models\Lavoura;
use App\Models\Propriedade;
use App\Models\Sensor;
use App\Models\User;

/**
 * Monta a cadeia Usuário -> Propriedade -> Lavoura -> Sensor usada pelos
 * testes de ingestão/alertas, sem repetir o setup em cada teste.
 */
trait CriaCenarioSensor
{
    protected function criarUsuario(array $atributos = []): User
    {
        return User::factory()->create($atributos);
    }

    protected function criarPropriedade(User $usuario, array $atributos = []): Propriedade
    {
        return (new Propriedade())->inserir(array_merge([
            'ds_nome' => 'Propriedade Teste',
        ], $atributos), $usuario->id_usuario);
    }

    protected function criarLavoura(Propriedade $propriedade, array $atributos = []): Lavoura
    {
        return (new Lavoura())->inserir(array_merge([
            'ds_cultura' => 'Lavoura Teste',
            'id_propriedade' => $propriedade->id_propriedade,
        ], $atributos), $propriedade->id_usuario);
    }

    protected function criarSensor(Lavoura $lavoura, array $atributos = []): Sensor
    {
        return (new Sensor())->inserir(array_merge([
            'ds_nome' => 'Sensor Teste',
            'tp_sensor' => 'ph',
            'id_propriedade' => $lavoura->id_propriedade,
            'id_lavoura' => $lavoura->id_lavoura,
        ], $atributos), $lavoura->id_usuario);
    }

    protected function definirLimite(Lavoura $lavoura, string $tipoSensor, ?float $min, ?float $max): void
    {
        ConfiguracaoLimite::salvarParaLavoura($lavoura->id_lavoura, [
            $tipoSensor => ['valor_min' => $min, 'valor_max' => $max],
        ]);
    }

    /**
     * Monta o cenário completo de uma vez: usuário com propriedade, lavoura e
     * um sensor de pH pronto para receber leituras.
     *
     * @return array{0: User, 1: Lavoura, 2: Sensor}
     */
    protected function cenarioComSensor(array $atributosSensor = []): array
    {
        $usuario = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario);
        $lavoura = $this->criarLavoura($propriedade);
        $sensor = $this->criarSensor($lavoura, $atributosSensor);

        return [$usuario, $lavoura, $sensor];
    }
}
