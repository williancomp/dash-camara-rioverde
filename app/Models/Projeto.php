<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'tipo',
        'titulo',
        'ementa',
        'texto_completo',
        'autor_id',
        'data_apresentacao',
        'status',
        'categoria',
        'prioridade',
        'destaque_app',
    ];

    protected $casts = [
        'data_apresentacao' => 'date',
        'destaque_app' => 'boolean',
    ];

    // Relacionamentos
    public function autor(): BelongsTo
    {
        return $this->belongsTo(Parlamentar::class, 'autor_id');
    }

    public function coautores(): BelongsToMany
    {
        return $this->belongsToMany(Parlamentar::class, 'parlamentar_projeto');
    }

    // Método auxiliar para todos os autores (principal + coautores)
    public function todosAutores()
    {
        return collect([$this->autor])->merge($this->coautores);
    }


    // Relacionamento com eventos
    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'projeto_relacionado_id');
    }
}
