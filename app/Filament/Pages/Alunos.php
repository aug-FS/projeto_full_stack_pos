<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Alunos extends Page
{
    protected string $view = 'filament.pages.alunos';

    protected static ?string $title = 'Alunos';

    protected static ?string $slug = 'alunos';

    protected static ?string $navigationLabel = 'Alunos';

    protected static string | \UnitEnum | null $navigationGroup = 'Escola';

    protected static ?int $navigationSort = 1;

    public function getHeading(): string
    {
        return 'Gestão de Alunos';
    }

    public function getSubheading(): ?string
    {
        return 'CRUD local em LocalStorage para nome, turma, matrícula, email e situação.';
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
