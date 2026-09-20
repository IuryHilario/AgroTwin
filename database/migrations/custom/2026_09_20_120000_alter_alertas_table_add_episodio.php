<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alerta deixa de ser "uma linha por leitura fora da faixa" e passa a ser um
 * episódio: abre quando o parâmetro sai da faixa, acumula as ocorrências
 * enquanto continuar fora e encerra quando volta ao normal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->string('tp_direcao', 10)->nullable()->after('id_lavoura');
            $table->unsignedInteger('nu_ocorrencias')->default(1)->after('tp_severidade');
            $table->timestamp('dt_ultima_ocorrencia')->nullable()->after('dt_alerta');
            $table->timestamp('dt_normalizado')->nullable()->after('dt_ultima_ocorrencia');

            // Consulta feita a cada leitura: existe episódio aberto para este sensor?
            $table->index(['id_sensor', 'tp_direcao', 'dt_normalizado'], 'alertas_episodio_aberto_index');
        });

        // Os alertas antigos são fotografias de uma leitura só, já passadas:
        // entram como episódios encerrados, com a direção lida da mensagem.
        DB::table('alertas')->update([
            'dt_ultima_ocorrencia' => DB::raw('dt_alerta'),
            'dt_normalizado' => DB::raw('dt_alerta'),
            'tp_direcao' => DB::raw("CASE WHEN ds_mensagem LIKE '%abaixo%' THEN 'abaixo' ELSE 'acima' END"),
        ]);
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropIndex('alertas_episodio_aberto_index');
            $table->dropColumn(['tp_direcao', 'nu_ocorrencias', 'dt_ultima_ocorrencia', 'dt_normalizado']);
        });
    }
};
