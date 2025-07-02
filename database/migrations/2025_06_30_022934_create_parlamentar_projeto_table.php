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
        Schema::create('parlamentar_projeto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parlamentar_id')->constrained('parlamentares')->onDelete('cascade');
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->timestamps();

            // Evitar duplicatas
            $table->unique(['parlamentar_id', 'projeto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parlamentar_projeto');
    }
};
