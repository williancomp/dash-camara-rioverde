<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'detalhes',
        'data',
        'hora_inicio',
        'hora_fim',
        'dia_todo',
        'tipo',
        'local',
        'endereco',
        'parlamentares_envolvidos',
        'projeto_relacionado_id',
        'publico',
        'transmissao_online',
        'link_transmissao',
        'cor_evento',
        'status',
        'observacoes',
        'anexos',
        'destaque',
        'notificar_usuarios',
        'ordem_exibicao',
    ];

    protected $casts = [
        'data' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
        'dia_todo' => 'boolean',
        'publico' => 'boolean',
        'transmissao_online' => 'boolean',
        'destaque' => 'boolean',
        'notificar_usuarios' => 'boolean',
        'parlamentares_envolvidos' => 'array',
        'anexos' => 'array',
    ];

    // Relacionamentos
    public function projetoRelacionado(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_relacionado_id');
    }

    // Métodos auxiliares
    public function getParlamentaresAttribute()
    {
        if (empty($this->parlamentares_envolvidos)) {
            return collect();
        }

        return Parlamentar::whereIn('id', $this->parlamentares_envolvidos)->get();
    }

    public function getDataFormatadaAttribute(): string
    {
        return $this->data->format('d/m/Y');
    }

    public function getHorarioFormatadoAttribute(): string
    {
        if ($this->dia_todo) {
            return 'Dia todo';
        }

        $inicio = Carbon::parse($this->hora_inicio)->format('H:i');
        $fim = $this->hora_fim ? Carbon::parse($this->hora_fim)->format('H:i') : '';

        return $fim ? "{$inicio} às {$fim}" : "A partir de {$inicio}";
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'agendado' => 'Agendado',
            'em_andamento' => 'Em Andamento',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado',
            'adiado' => 'Adiado',
        };
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
            'feriado' => 'Feriado',
            'recesso' => 'Recesso',
        };
    }

    // Scopes
    public function scopePublicos($query)
    {
        return $query->where('publico', true);
    }

    public function scopePorData($query, $data)
    {
        return $query->where('data', $data);
    }

    public function scopePorMes($query, $ano, $mes)
    {
        return $query->whereYear('data', $ano)->whereMonth('data', $mes);
    }

    public function scopeAtivos($query)
    {
        return $query->whereIn('status', ['agendado', 'em_andamento']);
    }
}
