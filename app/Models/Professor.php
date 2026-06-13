<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    protected $table = 'professores';

    protected $fillable = ['nome', 'disciplina', 'email', 'telefone', 'situacao'];

    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class);
    }
}
