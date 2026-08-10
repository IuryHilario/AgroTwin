<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_irrigacao', function (Blueprint $table) {
            $table->id('id_irrigacao');

            $table->unsignedBigInteger('id_lavoura');
            $table->foreign('id_lavoura')->references('id_lavoura')->on('lavouras')->onDelete('cascade');

            $table->string('tp_acionamento'); // automatico | manual
            $table->timestamp('dt_inicio');
            $table->timestamp('dt_fim')->nullable();
            $table->text('ds_motivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_irrigacao');
    }
};
