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
        Schema::create('insumos', function (Blueprint $table) {
            $table->id('id_insumo');
            $table->string('ds_nome', 100);
            $table->string('tp_insumo');
            $table->string('ds_fabricante', 100)->nullable();
            $table->string('tp_unidade_medida', 20)->nullable();
            $table->date('dt_validade')->nullable();
            $table->integer('nu_estoque_minimo')->default(0);
            $table->string('status')->nullable();
            $table->text('ds_composicao')->nullable();

            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
