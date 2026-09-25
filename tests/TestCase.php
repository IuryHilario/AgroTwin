<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Nenhum teste sai para a internet: o Open-Meteo não pede chave, então
        // qualquer tela com propriedade geolocalizada tentaria uma requisição
        // real. Requisição sem Http::fake() correspondente vira exceção — os
        // serviços de clima a tratam como "API fora do ar" e devolvem null.
        Http::preventStrayRequests();
    }
}
