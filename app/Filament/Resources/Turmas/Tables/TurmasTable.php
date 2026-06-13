<?php

namespace App\Filament\Resources\Turmas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TurmasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serie')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('turno')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Manhã' => 'success',
                        'Tarde' => 'warning',
                        'Noite' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('ano_letivo')
                    ->sortable(),
                TextColumn::make('professor.nome')
                    ->label('Professor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('alunos_count')
                    ->counts('alunos')
                    ->label('Total Alunos'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
