<?php

namespace App\Filament\Resources\Turmas;

use App\Filament\Resources\Turmas\Pages;
use App\Models\Turma;

use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\SelectFilter;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TurmaResource extends Resource
{
    protected static ?string $model = Turma::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Turmas';

    protected static ?string $modelLabel = 'Turma';

    protected static ?string $pluralModelLabel = 'Turmas';

    protected static string|UnitEnum|null $navigationGroup = 'Acadêmico';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados da turma')
                    ->description('Cadastro e edição das informações da turma.')
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome da turma')
                            ->placeholder('Ex: 3º Ano A')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('serie')
                            ->label('Série')
                            ->placeholder('Ex: Ensino Médio')
                            ->required()
                            ->maxLength(255),

                        Select::make('turno')
                            ->label('Turno')
                            ->options([
                                'Manhã' => 'Manhã',
                                'Tarde' => 'Tarde',
                                'Noite' => 'Noite',
                            ])
                            ->required(),

                        TextInput::make('ano_letivo')
                            ->label('Ano letivo')
                            ->placeholder('Ex: 2026')
                            ->numeric()
                            ->required(),

                        TextInput::make('capacidade')
                            ->label('Capacidade')
                            ->placeholder('Ex: 30')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serie')
                    ->label('Série')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('turno')
                    ->label('Turno')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Manhã' => 'success',
                        'Tarde' => 'warning',
                        'Noite' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('ano_letivo')
                    ->label('Ano letivo')
                    ->sortable(),

                TextColumn::make('capacidade')
                    ->label('Capacidade')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nome')
            ->filters([
                SelectFilter::make('turno')
                    ->label('Turno')
                    ->options([
                        'Manhã' => 'Manhã',
                        'Tarde' => 'Tarde',
                        'Noite' => 'Noite',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),

                DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Excluir selecionadas'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurmas::route('/'),
            'create' => Pages\CreateTurma::route('/create'),
            'edit' => Pages\EditTurma::route('/{record}/edit'),
        ];
    }
}