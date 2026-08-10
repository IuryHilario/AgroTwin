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
        Schema::create('leituras_sensor', function (Blueprint $table) {
            $table->id('id_leitura');
            $table->unsignedBigInteger('id_sensor');
            $table->foreign('id_sensor')->references('id_sensor')->on('sensores')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->timestamp('dt_leitura')->useCurrent();
            $table->timestamps();

            $table->index(['id_sensor', 'dt_leitura']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leituras_sensor');
    }
};
