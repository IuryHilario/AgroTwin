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
        Schema::create('lavoura', function (Blueprint $table) {
            $table->id('id_lavoura');
            $table->string('ds_cultura')->required();
            $table->date('dt_plantio')->nullable();
            $table->date('dt_colheita')->nullable();
            $table->string('tp_status');
            $table->text('ds_observacao')->nullable();

            $table->unsignedBigInteger('id_propriedade')->nullable();
            $table->foreign('id_propriedade')->references('id_propriedade')->on('propriedade')->onDelete('cascade');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lavoura');
    }
};
