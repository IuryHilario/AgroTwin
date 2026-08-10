<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lavouras', function (Blueprint $table) {
            $table->string('token_irrigacao', 40)->nullable()->unique()->after('id_usuario');
            $table->boolean('fl_irrigacao_ativa')->default(false)->after('token_irrigacao');
            $table->unsignedInteger('nu_intervalo_leitura_minutos')->default(5)->after('fl_irrigacao_ativa');
        });
    }

    public function down(): void
    {
        Schema::table('lavouras', function (Blueprint $table) {
            $table->dropColumn(['token_irrigacao', 'fl_irrigacao_ativa', 'nu_intervalo_leitura_minutos']);
        });
    }
};
