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
        Schema::table('pontos_interesse', function (Blueprint $table) {
            // Permite que a coluna 'subcategoria' aceite valores nulos
            $table->string('subcategoria')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pontos_interesse', function (Blueprint $table) {
            // Reverte a coluna para não aceitar nulos (opcional, mas boa prática)
            $table->string('subcategoria')->nullable(false)->change();
        });
    }
};