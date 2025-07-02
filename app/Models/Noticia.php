<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Noticia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'resumo',
        'conteudo',
        'foto_capa',
        'galeria_fotos',
        'categoria',
        'tags',
        'autor_parlamentar_id',
        'parlamentares_relacionados',
        'projeto_relacionado_id',
        'evento_relacionado_id',
        'status',
        'data_publicacao',
        'data_agendamento',
        'meta_title',
        'meta_description',
        'fonte',
        'destaque',
        'breaking_news',
        'notificar_usuarios',
        'ordem_destaque',
        'permitir_comentarios',
        'visualizacoes',
        'curtidas',
        'compartilhamentos',
        'editor_nome',
        'editor_email',
    ];

    protected $casts = [
        'data_publicacao' => 'datetime',
        'data_agendamento' => 'datetime',
        'galeria_fotos' => 'array',
        'tags' => 'array',
        'parlamentares_relacionados' => 'array',
        'destaque' => 'boolean',
        'breaking_news' => 'boolean',
        'notificar_usuarios' => 'boolean',
        'permitir_comentarios' => 'boolean',
    ];

    // Relacionamentos
    public function autorParlamentar(): BelongsTo
    {
        return $this->belongsTo(Parlamentar::class, 'autor_parlamentar_id');
    }

    public function projetoRelacionado(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_relacionado_id');
    }

    public function eventoRelacionado(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_relacionado_id');
    }

    // Accessors
    public function getParlamentaresAttribute()
    {
        if (empty($this->parlamentares_relacionados)) {
            return collect();
        }

        return Parlamentar::whereIn('id', $this->parlamentares_relacionados)->get();
    }

    public function getDataPublicacaoFormatadaAttribute(): string
    {
        return $this->data_publicacao?->format('d/m/Y H:i') ?? 'Não publicado';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'rascunho' => 'Rascunho',
            'agendado' => 'Agendado',
            'publicado' => 'Publicado',
            'arquivado' => 'Arquivado',
        };
    }

    public function getCategoriaLabelAttribute(): string
    {
        return match ($this->categoria) {
            'sessao' => 'Sessão',
            'projeto_lei' => 'Projeto de Lei',
            'audiencia_publica' => 'Audiência Pública',
            'evento' => 'Evento',
            'homenagem' => 'Homenagem',
            'comunicado' => 'Comunicado',
            'obra_publica' => 'Obra Pública',
            'saude' => 'Saúde',
            'educacao' => 'Educação',
            'transporte' => 'Transporte',
            'meio_ambiente' => 'Meio Ambiente',
            'social' => 'Social',
            'economia' => 'Economia',
            'cultura' => 'Cultura',
            'esporte' => 'Esporte',
            'geral' => 'Geral',
        };
    }

    public function getTempoLeituraAttribute(): string
    {
        $palavras = str_word_count(strip_tags($this->conteudo));
        $minutos = ceil($palavras / 200); // 200 palavras por minuto
        return $minutos . ' min de leitura';
    }

    // Mutators
    public function setTituloAttribute($value)
    {
        $this->attributes['titulo'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Scopes
    public function scopePublicadas($query)
    {
        return $query->where('status', 'publicado')
            ->where('data_publicacao', '<=', now());
    }

    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function scopeBreakingNews($query)
    {
        return $query->where('breaking_news', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Métodos
    public function incrementarVisualizacoes()
    {
        $this->increment('visualizacoes');
    }

    public function publicar()
    {
        $this->update([
            'status' => 'publicado',
            'data_publicacao' => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($noticia) {
            // Valores padrão para SEO
            if (empty($noticia->meta_title)) {
                $noticia->meta_title = $noticia->titulo;
            }
            if (empty($noticia->meta_description)) {
                $noticia->meta_description = Str::limit($noticia->resumo, 160);
            }

            // Valores padrão para campos JSON
            if (is_null($noticia->galeria_fotos)) {
                $noticia->galeria_fotos = [];
            }
            if (is_null($noticia->tags)) {
                $noticia->tags = [];
            }
            if (is_null($noticia->parlamentares_relacionados)) {
                $noticia->parlamentares_relacionados = [];
            }

            // Valores padrão para campos numéricos
            $noticia->visualizacoes = $noticia->visualizacoes ?? 0;
            $noticia->curtidas = $noticia->curtidas ?? 0;
            $noticia->compartilhamentos = $noticia->compartilhamentos ?? 0;
            $noticia->ordem_destaque = $noticia->ordem_destaque ?? 0;
        });
    }
}
