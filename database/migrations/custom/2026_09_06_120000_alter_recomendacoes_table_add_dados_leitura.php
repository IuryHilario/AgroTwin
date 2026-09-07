<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recomendacoes', function (Blueprint $table) {
            $table->float('nu_valor_leitura')->nullable()->after('ds_recomendacao');
            $table->float('nu_limite_min')->nullable()->after('nu_valor_leitura');
            $table->float('nu_limite_max')->nullable()->after('nu_limite_min');
        });
    }

    public function down(): void
    {
        Schema::table('recomendacoes', function (Blueprint $table) {
            $table->dropColumn(['nu_valor_leitura', 'nu_limite_min', 'nu_limite_max']);
        });
    }
};
