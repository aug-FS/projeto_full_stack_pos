<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $table = 'turmas';

    protected $fillable = [
        'nome',
        'serie',
        'turno',
        'ano_letivo',
        'capacidade',
    ];
}