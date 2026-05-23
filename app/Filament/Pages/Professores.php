<?php

namespace App\Filament\Pages;

use App\Models\Professor;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Professores extends Page
{
    protected string $view = 'filament.pages.professores';

    protected static ?string $title = 'Professores';

    protected static ?string $slug = 'professores';

    protected static ?string $navigationLabel = 'Professores';

    protected static string|\UnitEnum|null $navigationGroup = 'Escola';

    protected static ?int $navigationSort = 2;

    public string $nome = '';
    public string $disciplina = '';
    public string $email = '';
    public string $telefone = '';
    public string $situacao = 'Ativo';
    public string $search = '';
    public ?int $editingId = null;

    public function getHeading(): string
    {
        return 'Gestão de Professores';
    }

    public function getSubheading(): ?string
    {
        return 'Cadastre, edite e remova professores no banco de dados.';
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function getProfessores()
    {
        return Professor::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('nome', 'like', "%{$this->search}%")
                    ->orWhere('disciplina', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('telefone', 'like', "%{$this->search}%")
                    ->orWhere('situacao', 'like', "%{$this->search}%");
            }))
            ->orderBy('nome')
            ->get();
    }

    public function getTotalProfessores(): int
    {
        return Professor::count();
    }

    public function save(): void
    {
        $data = $this->validate([
            'nome' => 'required|string|max:255',
            'disciplina' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:professores,email' . ($this->editingId ? ",{$this->editingId}" : ''),
            'telefone' => 'nullable|string|max:20',
            'situacao' => 'required|in:Ativo,Inativo,Afastado',
        ]);

        if ($this->editingId) {
            Professor::findOrFail($this->editingId)->update($data);
        } else {
            Professor::create($data);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $professor = Professor::findOrFail($id);

        $this->editingId = $professor->id;
        $this->nome = $professor->nome;
        $this->disciplina = $professor->disciplina;
        $this->email = $professor->email;
        $this->telefone = $professor->telefone ?? '';
        $this->situacao = $professor->situacao;
    }

    public function delete(int $id): void
    {
        Professor::findOrFail($id)->delete();
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->nome = '';
        $this->disciplina = '';
        $this->email = '';
        $this->telefone = '';
        $this->situacao = 'Ativo';
        $this->resetValidation();
    }
}
