<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransmissaoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'youtube_url',
        'youtube_video_id',
        'titulo_transmissao',
        'descricao',
        'iniciada_em',
        'finalizada_em',
        'notificar_usuarios',
        'metadata',
    ];

    protected $casts = [
        'iniciada_em' => 'datetime',
        'finalizada_em' => 'datetime',
        'notificar_usuarios' => 'boolean',
        'metadata' => 'array',
    ];

    // Método para pegar a configuração atual (sempre existe apenas 1 registro)
    public static function current()
    {
        return static::firstOrCreate([
            'id' => 1
        ], [
            'status' => 'offline',
            'titulo_transmissao' => 'Transmissão da Câmara Municipal'
        ]);
    }

    // Método para extrair video ID do YouTube
    public function setYoutubeUrlAttribute($value)
    {
        $this->attributes['youtube_url'] = $value;

        if ($value) {
            // Extrair video ID de diferentes formatos de URL do YouTube
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $value, $matches);
            if (isset($matches[1])) {
                $this->attributes['youtube_video_id'] = $matches[1];
            }
        }
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'online' => 'AO VIVO',
            'offline' => 'OFFLINE',
            'aguarde' => 'AGUARDE',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'online' => 'success',
            'offline' => 'danger',
            'aguarde' => 'warning',
        };
    }

    public function getDuracaoTransmissaoAttribute(): ?string
    {
        if ($this->status === 'online' && $this->iniciada_em) {
            return $this->iniciada_em->diffForHumans(null, true);
        }

        if ($this->status === 'offline' && $this->iniciada_em && $this->finalizada_em) {
            return $this->iniciada_em->diffForHumans($this->finalizada_em, true);
        }

        return null;
    }
}
