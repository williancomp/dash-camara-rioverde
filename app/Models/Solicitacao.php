<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Solicitacao extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes';

    protected $fillable = [
        'protocolo',
        'tipo',
        'nome_cidadao',
        'email_cidadao',
        'telefone_cidadao',
        'cpf_cidadao',
        'endereco_cidadao',
        'bairro',
        'identificacao_publica',
        'assunto',
        'descricao',
        'anexos',
        'localizacao',
        'latitude',
        'longitude',
        'categoria',
        'prioridade',
        'tags',
        'parlamentar_responsavel_id',
        'setor_responsavel',
        'atribuido_por_id',
        'data_atribuicao',
        'status',
        'justificativa_status',
        'prazo_resposta',
        'data_resolucao',
        'projeto_relacionado_id',
        'numero_processo',
        'publica',
        'destaque',
        'notificar_cidadao',
        'visualizacoes',
        'apoios',
        'avaliacao_cidadao',
        'comentario_avaliacao',
        'origem',
        'ip_origem',
        'dados_extras',
    ];

    protected $casts = [
        'identificacao_publica' => 'boolean',
        'anexos' => 'array',
        'tags' => 'array',
        'data_atribuicao' => 'datetime',
        'prazo_resposta' => 'datetime',
        'data_resolucao' => 'datetime',
        'publica' => 'boolean',
        'destaque' => 'boolean',
        'notificar_cidadao' => 'boolean',
        'dados_extras' => 'array',
    ];

    // Relacionamentos
    public function parlamentarResponsavel(): BelongsTo
    {
        return $this->belongsTo(Parlamentar::class, 'parlamentar_responsavel_id');
    }

    public function atribuidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atribuido_por_id');
    }

    public function projetoRelacionado(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_relacionado_id');
    }

    // Accessors
    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'sugestao' => 'Sugestão',
            'reclamacao' => 'Reclamação',
            'denuncia' => 'Denúncia',
            'elogio' => 'Elogio',
            'pedido_informacao' => 'Pedido de Informação',
            'solicitacao_servico' => 'Solicitação de Serviço',
            'proposta_projeto' => 'Proposta de Projeto',
            'outros' => 'Outros',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'recebida' => 'Recebida',
            'em_analise' => 'Em Análise',
            'em_andamento' => 'Em Andamento',
            'aguardando_informacoes' => 'Aguardando Informações',
            'resolvida' => 'Resolvida',
            'rejeitada' => 'Rejeitada',
            'arquivada' => 'Arquivada',
        };
    }

    public function getCategoriaLabelAttribute(): string
    {
        return match ($this->categoria) {
            'infraestrutura' => 'Infraestrutura',
            'saude' => 'Saúde',
            'educacao' => 'Educação',
            'seguranca' => 'Segurança',
            'meio_ambiente' => 'Meio Ambiente',
            'transporte' => 'Transporte',
            'assistencia_social' => 'Assistência Social',
            'cultura_esporte' => 'Cultura e Esporte',
            'administracao' => 'Administração',
            'fiscalizacao' => 'Fiscalização',
            'outros' => 'Outros',
        };
    }

    public function getNomeCidadaoExibicaoAttribute(): string
    {
        return $this->identificacao_publica ? $this->nome_cidadao : 'Cidadão Anônimo';
    }

    public function getTempoResolucaoAttribute(): ?string
    {
        if ($this->status === 'resolvida' && $this->data_resolucao) {
            return $this->created_at->diffForHumans($this->data_resolucao, true);
        }
        return null;
    }

    public function getStatusCorAttribute(): string
    {
        return match ($this->status) {
            'recebida' => 'gray',
            'em_analise' => 'warning',
            'em_andamento' => 'info',
            'aguardando_informacoes' => 'secondary',
            'resolvida' => 'success',
            'rejeitada' => 'danger',
            'arquivada' => 'gray',
        };
    }

    // Scopes
    public function scopeRecebidas($query)
    {
        return $query->where('status', 'recebida');
    }

    public function scopeEmAndamento($query)
    {
        return $query->whereIn('status', ['em_analise', 'em_andamento']);
    }

    public function scopeFinalizadas($query)
    {
        return $query->whereIn('status', ['resolvida', 'rejeitada', 'arquivada']);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePublicas($query)
    {
        return $query->where('publica', true);
    }

    // Métodos
    public function gerarProtocolo()
    {
        $ano = now()->year;
        $ultimo = static::whereYear('created_at', $ano)->count() + 1;
        $this->protocolo = sprintf('SOL-%d-%06d', $ano, $ultimo);
        $this->save();
    }

    public function atribuirResponsavel($parlamentarId, $userId = null)
    {
        $this->update([
            'parlamentar_responsavel_id' => $parlamentarId,
            'atribuido_por_id' => $userId ?? auth()->id(),
            'data_atribuicao' => now(),
            'status' => 'em_analise',
        ]);
    }

    public function marcarComoResolvida($justificativa = null)
    {
        $this->update([
            'status' => 'resolvida',
            'data_resolucao' => now(),
            'justificativa_status' => $justificativa,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitacao) {
            if (empty($solicitacao->protocolo)) {
                $ano = now()->year;
                $ultimo = static::whereYear('created_at', $ano)->count() + 1;
                $solicitacao->protocolo = sprintf('SOL-%d-%06d', $ano, $ultimo);
            }
        });
    }
}
