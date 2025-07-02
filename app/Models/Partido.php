<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sigla',
        'numero_oficial',
        'cor_oficial',
        'logo',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function parlamentares(): HasMany
    {
        return $this->hasMany(Parlamentar::class);
    }
}
