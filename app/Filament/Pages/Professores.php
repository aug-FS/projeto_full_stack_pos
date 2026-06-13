<?php

namespace App\Filament\Pages;

use App\Models\Professor;
use App\Models\Turma;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Professores extends Page
{
    protected string $view = 'filament.pages.professores';

    protected static ?string $title = 'Professores';

    protected static ?string $slug = 'professores';

    protected static ?string $navigationLabel = 'Professores';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Acadêmico';

    protected static ?int $navigationSort = 2;

    public string $nome = '';
    public string $disciplina = '';
    public string $email = '';
    public string $telefone = '';
    public string $situacao = 'Ativo';
    public array $turmasSelecionadas = []; // To store IDs of turmas to link
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
            ->with('turmas')
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

    public function getAllTurmas()
    {
        return Turma::orderBy('nome')->get();
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
            $professor = Professor::findOrFail($this->editingId);
            $professor->update($data);
        } else {
            $professor = Professor::create($data);
        }

        // Update Turmas relationship
        // Since Turma belongs to Professor (professor_id in turmas table), we update turmas
        Turma::where('professor_id', $professor->id)->update(['professor_id' => null]);
        if (!empty($this->turmasSelecionadas)) {
            Turma::whereIn('id', $this->turmasSelecionadas)->update(['professor_id' => $professor->id]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $professor = Professor::with('turmas')->findOrFail($id);

        $this->editingId = $professor->id;
        $this->nome = $professor->nome;
        $this->disciplina = $professor->disciplina;
        $this->email = $professor->email;
        $this->telefone = $professor->telefone ?? '';
        $this->situacao = $professor->situacao;
        $this->turmasSelecionadas = $professor->turmas->pluck('id')->map(fn($id) => (string)$id)->toArray();
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
        $this->turmasSelecionadas = [];
        $this->resetValidation();
    }
}
