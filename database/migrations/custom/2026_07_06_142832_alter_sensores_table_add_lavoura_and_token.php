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
        Schema::table('sensores', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lavoura')->nullable()->after('id_propriedade');
            $table->foreign('id_lavoura')->references('id_lavoura')->on('lavouras')->onDelete('set null');
            $table->string('token', 40)->nullable()->unique()->after('ds_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensores', function (Blueprint $table) {
            $table->dropForeign(['id_lavoura']);
            $table->dropColumn(['id_lavoura', 'token']);
        });
    }
};
