<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->boolean('fl_notificar_email_alerta')->default(true)->after('password');
            $table->unsignedBigInteger('id_propriedade_padrao')->nullable()->after('fl_notificar_email_alerta');

            $table->foreign('id_propriedade_padrao')
                ->references('id_propriedade')->on('propriedades')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_propriedade_padrao']);
            $table->dropColumn(['fl_notificar_email_alerta', 'id_propriedade_padrao']);
        });
    }
};
