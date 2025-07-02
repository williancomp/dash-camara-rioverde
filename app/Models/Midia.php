<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Midia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'descricao',
        'thumbnail',
        'tipo',
        'youtube_url',
        'youtube_video_id',
        'facebook_url',
        'instagram_url',
        'link_alternativo',
        'duracao_segundos',
        'qualidade',
        'data_evento',
        'hora_inicio',
        'hora_fim',
        'local_evento',
        'parlamentares_presentes',
        'evento_relacionado_id',
        'projetos_discutidos',
        'tags',
        'periodo_legislativo',
        'ano',
        'mes',
        'status',
        'destaque',
        'ordem_exibicao',
        'disponivel_app',
        'visualizacoes',
        'curtidas',
        'compartilhamentos',
        'observacoes',
        'responsavel_upload',
        'data_upload',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'data_evento' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
        'data_upload' => 'datetime',
        'parlamentares_presentes' => 'array',
        'projetos_discutidos' => 'array',
        'tags' => 'array',
        'destaque' => 'boolean',
        'disponivel_app' => 'boolean',
    ];

    // Relacionamentos
    public function eventoRelacionado(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_relacionado_id');
    }

    // Accessors
    public function getParlamentaresPresentesObjAttribute()
    {
        if (empty($this->parlamentares_presentes)) {
            return collect();
        }

        return Parlamentar::whereIn('id', $this->parlamentares_presentes)->get();
    }

    public function getProjetosDiscutidosObjAttribute()
    {
        if (empty($this->projetos_discutidos)) {
            return collect();
        }

        return Projeto::whereIn('id', $this->projetos_discutidos)->get();
    }

    public function getDataEventoFormatadaAttribute(): string
    {
        return $this->data_evento->format('d/m/Y');
    }

    public function getDuracaoFormatadaAttribute(): ?string
    {
        if (!$this->duracao_segundos) {
            return null;
        }

        $horas = floor($this->duracao_segundos / 3600);
        $minutos = floor(($this->duracao_segundos % 3600) / 60);
        $segundos = $this->duracao_segundos % 60;

        if ($horas > 0) {
            return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
        }

        return sprintf('%02d:%02d', $minutos, $segundos);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'sessao_ordinaria' => 'Sessão Ordinária',
            'sessao_extraordinaria' => 'Sessão Extraordinária',
            'sessao_solene' => 'Sessão Solene',
            'audiencia_publica' => 'Audiência Pública',
            'reuniao_comissao' => 'Reunião de Comissão',
            'evento_especial' => 'Evento Especial',
            'solenidade' => 'Solenidade',
            'entrevista' => 'Entrevista',
            'pronunciamento' => 'Pronunciamento',
            'outros' => 'Outros',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'processando' => 'Processando',
            'erro' => 'Erro',
        };
    }

    // Mutators
    public function setTituloAttribute($value)
    {
        $this->attributes['titulo'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function setYoutubeUrlAttribute($value)
    {
        $this->attributes['youtube_url'] = $value;

        if ($value) {
            // Extrair video ID do YouTube
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $value, $matches);
            if (isset($matches[1])) {
                $this->attributes['youtube_video_id'] = $matches[1];
            }
        }
    }

    public function setDataEventoAttribute($value)
    {
        $this->attributes['data_evento'] = $value;

        if ($value) {
            $data = Carbon::parse($value);
            $this->attributes['ano'] = $data->year;
            $this->attributes['mes'] = $data->month;
        }
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeDisponiveisApp($query)
    {
        return $query->where('disponivel_app', true);
    }

    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorAno($query, $ano)
    {
        return $query->where('ano', $ano);
    }

    public function scopePorMes($query, $mes)
    {
        return $query->where('mes', $mes);
    }

    // Métodos
    public function incrementarVisualizacoes()
    {
        $this->increment('visualizacoes');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($midia) {
            if (empty($midia->meta_title)) {
                $midia->meta_title = $midia->titulo;
            }
            if (empty($midia->meta_description)) {
                $midia->meta_description = Str::limit($midia->descricao, 160);
            }
            if (empty($midia->responsavel_upload)) {
                $midia->responsavel_upload = auth()->user()?->name;
            }
            if (empty($midia->data_upload)) {
                $midia->data_upload = now();
            }
        });
    }
}
