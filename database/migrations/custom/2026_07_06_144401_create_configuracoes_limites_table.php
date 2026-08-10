<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracoes_limites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lavoura');
            $table->foreign('id_lavoura')->references('id_lavoura')->on('lavouras')->onDelete('cascade');
            $table->string('tp_sensor');
            $table->decimal('valor_min', 10, 2)->nullable();
            $table->decimal('valor_max', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['id_lavoura', 'tp_sensor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracoes_limites');
    }
};
