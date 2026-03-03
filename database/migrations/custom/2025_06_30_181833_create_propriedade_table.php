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
        Schema::create('propriedade', function (Blueprint $table) {
            $table->id('id_propriedade');
            $table->string('ds_nome');
            $table->string('tp_solo')->nullable();
            $table->decimal('nu_area_hectares', 10, 2)->nullable();
            $table->string('ds_localizacao')->nullable();

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
        Schema::dropIfExists('propriedade');
    }
};
