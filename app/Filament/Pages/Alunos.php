<?php

namespace App\Filament\Pages;

use App\Models\Aluno;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Alunos extends Page
{
    protected string $view = 'filament.pages.alunos';

    protected static ?string $title = 'Alunos';

    protected static ?string $slug = 'alunos';

    protected static ?string $navigationLabel = 'Alunos';

    protected static string|\UnitEnum|null $navigationGroup = 'Escola';

    protected static ?int $navigationSort = 1;

    public string $nome = '';
    public string $turma = '';
    public string $matricula = '';
    public string $email = '';
    public string $situacao = 'Ativo';
    public string $search = '';
    public ?int $editingId = null;

    public function getHeading(): string
    {
        return 'Gestão de Alunos';
    }

    public function getSubheading(): ?string
    {
        return 'Cadastre, edite e remova alunos no banco de dados.';
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function getAlunos()
    {
        return Aluno::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('nome', 'like', "%{$this->search}%")
                    ->orWhere('turma', 'like', "%{$this->search}%")
                    ->orWhere('matricula', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('situacao', 'like', "%{$this->search}%");
            }))
            ->orderBy('nome')
            ->get();
    }

    public function getTotalAlunos(): int
    {
        return Aluno::count();
    }

    public function save(): void
    {
        $data = $this->validate([
            'nome' => 'required|string|max:255',
            'turma' => 'required|string|max:255',
            'matricula' => 'required|string|max:255|unique:alunos,matricula' . ($this->editingId ? ",{$this->editingId}" : ''),
            'email' => 'required|email|max:255',
            'situacao' => 'required|in:Ativo,Inativo,Transferido',
        ]);

        if ($this->editingId) {
            Aluno::findOrFail($this->editingId)->update($data);
        } else {
            Aluno::create($data);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $aluno = Aluno::findOrFail($id);

        $this->editingId = $aluno->id;
        $this->nome = $aluno->nome;
        $this->turma = $aluno->turma;
        $this->matricula = $aluno->matricula;
        $this->email = $aluno->email;
        $this->situacao = $aluno->situacao;
    }

    public function delete(int $id): void
    {
        Aluno::findOrFail($id)->delete();
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->nome = '';
        $this->turma = '';
        $this->matricula = '';
        $this->email = '';
        $this->situacao = 'Ativo';
        $this->resetValidation();
    }
}
