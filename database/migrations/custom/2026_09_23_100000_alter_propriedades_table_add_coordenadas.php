<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Coordenadas da propriedade, usadas pelo Open-Meteo (que consulta por
 * latitude/longitude, não por nome de cidade). Seis casas decimais dão
 * precisão de ~10 cm, bem mais do que a grade dos modelos de previsão.
 *
 * Nullable: propriedades antigas continuam válidas e o ClimaService cai
 * para a geocodificação do texto de ds_localizacao enquanto não forem editadas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propriedades', function (Blueprint $table) {
            $table->decimal('nu_latitude', 9, 6)->nullable()->after('ds_localizacao');
            $table->decimal('nu_longitude', 9, 6)->nullable()->after('nu_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('propriedades', function (Blueprint $table) {
            $table->dropColumn(['nu_latitude', 'nu_longitude']);
        });
    }
};
