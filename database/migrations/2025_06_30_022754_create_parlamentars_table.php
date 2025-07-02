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
        Schema::create('parlamentares', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo');
            $table->string('nome_parlamentar');
            $table->string('foto')->nullable();
            $table->text('biografia')->nullable();

            // Relacionamento com partido
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');

            $table->integer('numero_urna');
            $table->date('mandato_inicio');
            $table->date('mandato_fim');
            $table->enum('status', ['ativo', 'licenciado', 'afastado'])->default('ativo');
            $table->string('cargo_mesa_diretora')->nullable();

            // Contato público
            $table->string('telefone_gabinete')->nullable();
            $table->string('email_oficial')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('site_pessoal')->nullable();

            // Configurações do app
            $table->integer('ordem_exibicao')->default(0);
            $table->boolean('ativo_app')->default(true);
            $table->string('cor_perfil', 7)->default('#3B82F6'); // Hex color

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parlamentares');
    }
};
