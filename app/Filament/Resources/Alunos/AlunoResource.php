<?php

namespace App\Filament\Resources\Alunos;

use App\Filament\Resources\Alunos\Pages;
use App\Models\Aluno;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AlunoResource extends Resource
{
    protected static ?string $model = Aluno::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Alunos';

    protected static ?string $modelLabel = 'Aluno';

    protected static ?string $pluralModelLabel = 'Alunos';

    protected static string | UnitEnum | null $navigationGroup = 'Acadêmico';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do aluno')
                    ->description('Cadastro e edição dos dados principais do aluno.')
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome')
                            ->placeholder('Ex: Ana Souza')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('turma')
                            ->label('Turma')
                            ->placeholder('Ex: 3º Ano A')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('matricula')
                            ->label('Matrícula')
                            ->placeholder('Ex: 2026001')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->placeholder('Ex: aluno@escola.com')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('situacao')
                            ->label('Situação')
                            ->options([
                                'Ativo' => 'Ativo',
                                'Inativo' => 'Inativo',
                                'Transferido' => 'Transferido',
                            ])
                            ->default('Ativo')
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

                TextColumn::make('turma')
                    ->label('Turma')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                TextColumn::make('situacao')
                    ->label('Situação')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ativo' => 'success',
                        'Inativo' => 'gray',
                        'Transferido' => 'warning',
                        default => 'gray',
                    })
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
                SelectFilter::make('situacao')
                    ->label('Situação')
                    ->options([
                        'Ativo' => 'Ativo',
                        'Inativo' => 'Inativo',
                        'Transferido' => 'Transferido',
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
                        ->label('Excluir selecionados'),
                ]),
            ]);
    }

    public static function getPages(): array
{
    return [
        'index' => Pages\ListAlunos::route('/'),
        'create' => Pages\CreateAluno::route('/create'),
        'edit' => Pages\EditAluno::route('/{record}/edit'),
    ];
}
}