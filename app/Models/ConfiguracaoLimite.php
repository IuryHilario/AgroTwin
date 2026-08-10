<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Entity\ConfiguracaoLimiteEntity;
use App\Traits\UsesEntity;

class ConfiguracaoLimite extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(ConfiguracaoLimiteEntity::class);
    }

    public function lavoura()
    {
        return $this->belongsTo(Lavoura::class, 'id_lavoura', 'id_lavoura');
    }

    /**
     * Retorna as configurações de limite de uma lavoura, indexadas pelo tipo de sensor.
     */
    public static function porLavoura($idLavoura)
    {
        return ConfiguracaoLimiteEntity::getPorLavoura(self::query(), $idLavoura)
            ->get()
            ->keyBy(fn (self $limite) => $limite->tp_sensor?->value);
    }

    /**
     * Salva (cria ou atualiza) os limites de uma lavoura de uma vez.
     * $limites = ['umidade_solo' => ['valor_min' => 30, 'valor_max' => 80], ...]
     */
    public static function salvarParaLavoura($idLavoura, array $limites): void
    {
        foreach ($limites as $tpSensor => $valores) {
            $valorMin = $valores['valor_min'] ?? null;
            $valorMax = $valores['valor_max'] ?? null;

            if ($valorMin === null && $valorMax === null) {
                self::where('id_lavoura', $idLavoura)->where('tp_sensor', $tpSensor)->delete();
                continue;
            }

            self::updateOrCreate(
                ['id_lavoura' => $idLavoura, 'tp_sensor' => $tpSensor],
                ['valor_min' => $valorMin, 'valor_max' => $valorMax]
            );
        }
    }
}
