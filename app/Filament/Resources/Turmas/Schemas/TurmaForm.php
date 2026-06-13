<?php

namespace App\Filament\Resources\Turmas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TurmaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('serie')
                    ->required()
                    ->maxLength(255),
                Select::make('turno')
                    ->options([
                        'Manhã' => 'Manhã',
                        'Tarde' => 'Tarde',
                        'Noite' => 'Noite',
                    ])
                    ->required(),
                TextInput::make('ano_letivo')
                    ->numeric()
                    ->required(),
                TextInput::make('capacidade')
                    ->numeric()
                    ->default(0),
                Select::make('professor_id')
                    ->relationship('professor', 'nome')
                    ->label('Professor')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
