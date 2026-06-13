<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aluno extends Model
{
    protected $fillable = ['nome', 'matricula', 'email', 'situacao'];

    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class);
    }
}
