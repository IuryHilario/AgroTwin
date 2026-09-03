<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class MqttSubscribeSensor extends Command
{
    protected $signature = 'mqtt:subscribe-sensor';
    protected $description = 'Escuta o topico MQTT do sensor AgroTwin e salva as leituras no banco';

    // Mapeia o campo do JSON para o tp_sensor cadastrado na tabela sensores
    protected array $mapaCampos = [
        'umidade'       => 'umidade_solo',
        'temperatura'   => 'temperatura',
        'condutividade' => 'condutividade',
        'ph'            => 'ph',
        'nitrogenio'    => 'nitrogenio',
        'fosforo'       => 'fosforo',
        'potassio'      => 'potassio',
    ];

    public function handle()
    {
        $this->info('Conectando ao broker MQTT...');

        $mqtt = MQTT::connection();

        $mqtt->subscribe('agrotwin/sensor/leitura', function (string $topic, string $message) {
            $this->info("Mensagem recebida em [$topic]");
            $this->processarMensagem($message);
        }, 0);

        $this->info('Aguardando mensagens... (Ctrl+C para parar)');

        $mqtt->loop(true);
    }

    protected function processarMensagem(string $message): void
    {
        $dados = json_decode($message, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('JSON invalido recebido: ' . $message);
            return;
        }

        if (isset($dados['erro']) && $dados['erro'] === true) {
            $this->warn('ESP32 reportou erro de leitura. Codigo: ' . ($dados['codigo'] ?? '?'));
            return;
        }

        $leitura = $dados['leitura'] ?? null;

        if (!$leitura) {
            $this->error('JSON sem campo "leitura"');
            return;
        }

        // Busca os sensores cadastrados (id_sensor por tp_sensor)
        $sensores = DB::table('sensores')
            ->whereIn('tp_sensor', array_values($this->mapaCampos))
            ->pluck('id_sensor', 'tp_sensor');

        $agora = now();
        $inseridos = 0;

        foreach ($this->mapaCampos as $campoJson => $tpSensor) {
            if (!isset($leitura[$campoJson])) {
                continue;
            }

            $idSensor = $sensores[$tpSensor] ?? null;

            if (!$idSensor) {
                $this->warn("Sensor tipo '$tpSensor' nao encontrado na tabela sensores");
                continue;
            }

            DB::table('leituras_sensor')->insert([
                'id_sensor'  => $idSensor,
                'valor'      => $leitura[$campoJson],
                'dt_leitura' => $agora,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);

            $inseridos++;
        }

        $this->info("$inseridos leituras salvas no banco.");
    }
}