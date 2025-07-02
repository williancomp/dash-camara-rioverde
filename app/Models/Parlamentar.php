<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Parlamentar extends Model
{
    use HasFactory;

    protected $table = 'parlamentares';

    protected $fillable = [
        'nome_completo',
        'nome_parlamentar',
        'foto',
        'biografia',
        'partido_id',
        'numero_urna',
        'mandato_inicio',
        'mandato_fim',
        'status',
        'cargo_mesa_diretora',
        'telefone_gabinete',
        'email_oficial',
        'instagram',
        'facebook',
        'site_pessoal',
        'ordem_exibicao',
        'ativo_app',
        'cor_perfil',
    ];

    protected $casts = [
        'mandato_inicio' => 'date',
        'mandato_fim' => 'date',
        'ativo_app' => 'boolean',
    ];

    // Relacionamentos
    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class);
    }

    public function projetosAutor(): HasMany
    {
        return $this->hasMany(Projeto::class, 'autor_id');
    }

    public function projetosCoautor(): BelongsToMany
    {
        return $this->belongsToMany(Projeto::class, 'parlamentar_projeto');
    }

    // Método auxiliar para todos os projetos (autor + coautor)
    public function todosProjetos()
    {
        return $this->projetosAutor->merge($this->projetosCoautor);
    }

    // Relacionamento com eventos
    public function eventos()
    {
        return Evento::whereJsonContains('parlamentares_envolvidos', $this->id);
    }
}
