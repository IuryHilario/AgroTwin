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
        Schema::create('alertas', function (Blueprint $table) {
            $table->id('id_alerta');
            $table->unsignedBigInteger('id_sensor');
            $table->foreign('id_sensor')->references('id_sensor')->on('sensores')->onDelete('cascade');
            $table->unsignedBigInteger('id_lavoura')->nullable();
            $table->foreign('id_lavoura')->references('id_lavoura')->on('lavouras')->onDelete('cascade');
            $table->string('tp_severidade')->default('warning');
            $table->text('ds_mensagem');
            $table->boolean('fl_lida')->default(false);
            $table->timestamp('dt_alerta')->useCurrent();
            $table->timestamps();

            $table->index(['id_lavoura', 'fl_lida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
