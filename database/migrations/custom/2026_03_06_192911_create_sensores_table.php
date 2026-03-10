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
        Schema::create('sensores', function (Blueprint $table) {
            $table->id('id_sensor');
            $table->string('ds_nome');
            $table->string('tp_sensor');
            $table->string('ds_status');
            $table->unsignedBigInteger('id_propriedade');
            $table->foreign('id_propriedade')->references('id_propriedade')->on('propriedades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensores');
    }
};
