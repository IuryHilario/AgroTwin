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
        Schema::create('insumos_controle_estoque', function (Blueprint $table) {
            $table->id('id_controle_estoque');
            $table->string('tp_controle');
            $table->decimal('nu_quantidade', 10, 2)->default(0);
            $table->date('dt_movimentacao');
            $table->decimal('vl_unitario', 10, 2)->default(0)->required();
            $table->string('ds_documento')->nullable();
            $table->string('ds_fornecedor')->nullable();
            $table->text('ds_observacao')->nullable();

            $table->unsignedBigInteger('id_insumo');
            $table->foreign('id_insumo')->references('id_insumo')->on('insumos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_controle_estoque');
    }
};
