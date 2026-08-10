<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recomendacoes', function (Blueprint $table) {
            $table->id('id_recomendacao');

            $table->unsignedBigInteger('id_lavoura');
            $table->foreign('id_lavoura')->references('id_lavoura')->on('lavouras')->onDelete('cascade');

            $table->string('tp_sensor')->nullable();
            $table->text('ds_recomendacao');
            $table->timestamp('dt_recomendacao');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendacoes');
    }
};
