<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PontoInteresse extends Model
{
    protected $table = 'pontos_interesse';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'foto_principal',
        'galeria_fotos',
        'latitude',
        'longitude',
        'endereco_completo',
        'bairro',
        'cep',
        'referencia',
        'categoria',
        'subcategoria',
        'telefone',
        'whatsapp',
        'email',
        'site',
        'instagram',
        'facebook',
        'horario_funcionamento',
        'funciona_24h',
        'observacoes_horario',
        'servicos_oferecidos',
        'acessibilidade',
        'estacionamento',
        'wifi_publico',
        'capacidade',
        'status',
        'destaque',
        'verificado',
        'ordem_exibicao',
        'responsavel_cadastro',
        'data_verificacao',
        'observacoes_internas',
        'visualizacoes',
        'curtidas',
        'avaliacao_media',
        'total_avaliacoes',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'galeria_fotos' => 'array',
        'horario_funcionamento' => 'array',
        'servicos_oferecidos' => 'array',
        'funciona_24h' => 'boolean',
        'acessibilidade' => 'boolean',
        'estacionamento' => 'boolean',
        'wifi_publico' => 'boolean',
        'destaque' => 'boolean',
        'verificado' => 'boolean',
        'data_verificacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'location'
    ];

    // Atributo computado necessário para o plugin Google Maps
    public function getLocationAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    public function setLocationAttribute(?array $value): void
    {
        if (is_array($value)) {
            $this->attributes['latitude'] = $value['lat'] ?? null;
            $this->attributes['longitude'] = $value['lng'] ?? null;
            unset($this->attributes['location']);
        }
    }

    // Método para obter as categorias disponíveis
    public static function getCategorias(): array
    {
        return [
            'educacao' => 'Educação',
            'saude' => 'Saúde',
            'lazer_esporte' => 'Lazer e Esporte',
            'servicos_publicos' => 'Serviços Públicos',
            'transporte' => 'Transporte',
            'seguranca' => 'Segurança',
            'cultura' => 'Cultura',
            'assistencia_social' => 'Assistência Social',
            'meio_ambiente' => 'Meio Ambiente',
            'legislativo' => 'Legislativo',
            'obras_andamento' => 'Obras em Andamento',
            'locais_votacao' => 'Locais de Votação',
            'turismo' => 'Turismo',
            'religioso' => 'Religioso',
            'comercio_servicos' => 'Comércio e Serviços'
        ];
    }

    public function getCategoriaLabelAttribute(): string
    {
        return self::getCategorias()[$this->categoria] ?? ucfirst(str_replace('_', ' ', $this->categoria));
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeVerificados($query)
    {
        return $query->where('verificado', true);
    }

    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }
}
