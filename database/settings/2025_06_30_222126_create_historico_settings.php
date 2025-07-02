<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('historico.camara_titulo', 'História da Câmara Municipal de Rio Verde');
        $this->migrator->add('historico.camara_subtitulo', null);
        $this->migrator->add('historico.camara_conteudo', 'Conteúdo da história da Câmara Municipal será adicionado aqui.');
        $this->migrator->add('historico.camara_imagem_destaque', null);
        $this->migrator->add('historico.camara_galeria_imagens', null);
        $this->migrator->add('historico.camara_meta_dados', [
            'description' => 'História da Câmara Municipal de Rio Verde',
            'keywords' => 'câmara municipal, rio verde, história, política'
        ]);

        $this->migrator->add('historico.cidade_titulo', 'História de Rio Verde');
        $this->migrator->add('historico.cidade_subtitulo', null);
        $this->migrator->add('historico.cidade_conteudo', 'Conteúdo da história da cidade será adicionado aqui.');
        $this->migrator->add('historico.cidade_imagem_destaque', null);
        $this->migrator->add('historico.cidade_galeria_imagens', null);
        $this->migrator->add('historico.cidade_meta_dados', [
            'description' => 'História da cidade de Rio Verde, Goiás',
            'keywords' => 'rio verde, goiás, história, cidade'
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('historico.camara_titulo');
        $this->migrator->delete('historico.camara_subtitulo');
        $this->migrator->delete('historico.camara_conteudo');
        $this->migrator->delete('historico.camara_imagem_destaque');
        $this->migrator->delete('historico.camara_galeria_imagens');
        $this->migrator->delete('historico.camara_meta_dados');

        $this->migrator->delete('historico.cidade_titulo');
        $this->migrator->delete('historico.cidade_subtitulo');
        $this->migrator->delete('historico.cidade_conteudo');
        $this->migrator->delete('historico.cidade_imagem_destaque');
        $this->migrator->delete('historico.cidade_galeria_imagens');
        $this->migrator->delete('historico.cidade_meta_dados');
    }
};
