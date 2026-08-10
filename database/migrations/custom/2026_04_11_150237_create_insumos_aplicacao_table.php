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
        Schema::create('insumos_aplicacao', function (Blueprint $table) {
            $table->id('id_insumo_aplicacao');
            $table->unsignedBigInteger('id_lavoura');
            $table->unsignedBigInteger('id_insumo');
            $table->dateTime('dt_aplicacao');
            $table->decimal('nu_area_aplicada', 10, 2);
            $table->decimal('nu_dosagem_hectare', 10, 2)->nullable();
            $table->decimal('nu_quantidade_aplicada', 10, 2);
            $table->decimal('nu_concentracao', 5, 2)->nullable();
            $table->string('tp_metodo_aplicacao');
            $table->string('ds_equipamento')->nullable();
            $table->string('ds_responsavel');
            $table->string('tp_finalidade')->nullable();
            $table->string('ds_observacoes')->nullable();
            $table->timestamps();

            $table->foreign('id_lavoura')->references('id_lavoura')->on('lavouras')->onDelete('cascade');
            $table->foreign('id_insumo')->references('id_insumo')->on('insumos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_aplicacao');
    }
};
