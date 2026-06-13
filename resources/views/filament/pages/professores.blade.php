<x-filament-panels::page>
    <style>
        .teachers-page {
            --school-primary: #7c3aed;
            --school-primary-dark: #6d28d9;
            --school-card: #ffffff;
            --school-text: #0f172a;
            --school-muted: #64748b;
            --school-border: #e2e8f0;
            --school-danger: #dc2626;
            --school-success: #16a34a;
            --school-warning: #d97706;
        }

        .teachers-hero {
            background: linear-gradient(135deg, var(--school-primary), var(--school-primary-dark));
            color: white;
            border-radius: 24px;
            padding: 32px;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: center;
            margin-bottom: 24px;
        }

        .teachers-hero h2 {
            font-size: 34px;
            font-weight: 800;
            margin: 10px 0 8px;
        }

        .teachers-hero p {
            margin: 0;
            opacity: 0.9;
        }

        .teachers-badge {
            background: rgba(255, 255, 255, 0.18);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
        }

        .teachers-counter {
            background: rgba(255, 255, 255, 0.16);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            min-width: 160px;
        }

        .teachers-counter strong {
            display: block;
            font-size: 42px;
            line-height: 1;
        }

        .teachers-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 24px;
        }

        .teachers-card {
            background: var(--school-card);
            border: 1px solid var(--school-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .teachers-card h3 {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            color: var(--school-text);
        }

        .teachers-card p {
            margin: 6px 0 20px;
            color: var(--school-muted);
        }

        .teachers-card label {
            display: block;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--school-text);
        }

        .teachers-card input,
        .teachers-card select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--school-border);
            border-radius: 12px;
            margin-bottom: 4px;
            font-size: 15px;
            background: white;
            color: var(--school-text);
            box-sizing: border-box;
        }

        .teachers-field-error {
            color: var(--school-danger);
            font-size: 13px;
            margin-bottom: 12px;
            display: block;
        }

        .teachers-turmas-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            max-height: 200px;
            overflow-y: auto;
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--school-border);
        }

        .teachers-turma-item {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .teachers-turma-item input {
            width: auto;
            margin: 0;
        }

        .teachers-actions {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }

        .teachers-btn-primary,
        .teachers-btn-secondary,
        .teachers-btn-danger,
        .teachers-btn-edit {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 800;
            transition: 0.2s;
        }

        .teachers-btn-primary {
            background: var(--school-primary);
            color: white;
            flex: 1;
        }

        .teachers-btn-primary:hover {
            background: var(--school-primary-dark);
        }

        .teachers-btn-secondary {
            background: #ede9fe;
            color: var(--school-primary);
        }

        .teachers-btn-edit {
            background: #e0f2fe;
            color: #0369a1;
        }

        .teachers-btn-danger {
            background: #fee2e2;
            color: var(--school-danger);
        }

        .teachers-list-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .teachers-search {
            max-width: 360px;
        }

        .teachers-table-wrapper {
            overflow-x: auto;
        }

        .teachers-table {
            width: 100%;
            border-collapse: collapse;
        }

        .teachers-table th {
            text-align: left;
            color: var(--school-muted);
            font-size: 14px;
            border-bottom: 1px solid var(--school-border);
            padding: 12px;
            white-space: nowrap;
        }

        .teachers-table td {
            border-bottom: 1px solid var(--school-border);
            padding: 12px;
            vertical-align: middle;
            color: var(--school-text);
        }

        .teachers-status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
        }

        .teachers-status-ativo {
            background: #dcfce7;
            color: var(--school-success);
        }

        .teachers-status-inativo {
            background: #f3f4f6;
            color: var(--school-muted);
        }

        .teachers-status-afastado {
            background: #fef3c7;
            color: var(--school-warning);
        }

        .teachers-action-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .teachers-empty-state {
            text-align: center;
            padding: 30px;
            color: var(--school-muted);
        }

        @media (max-width: 1024px) {
            .teachers-grid,
            .teachers-hero {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: stretch;
            }

            .teachers-list-header {
                flex-direction: column;
            }

            .teachers-search {
                max-width: 100%;
            }
        }
    </style>

    @php $professores = $this->getProfessores(); @endphp

    <div class="teachers-page">
        <section class="teachers-hero">
            <div>
                <span class="teachers-badge">Banco de dados</span>
                <h2>Professores</h2>
                <p>Cadastre, edite, pesquise e remova professores diretamente no banco de dados.</p>
            </div>

            <div class="teachers-counter">
                <strong>{{ $this->getTotalProfessores() }}</strong>
                <span>professores cadastrados</span>
            </div>
        </section>

        <section class="teachers-grid">
            <form wire:submit="save" class="teachers-card">
                <div>
                    <h3>{{ $editingId ? 'Editar professor' : 'Novo professor' }}</h3>
                    <p>Preencha os dados abaixo.</p>
                </div>

                <label for="teacher-name">Nome</label>
                <input id="teacher-name" type="text" placeholder="Ex: Carlos Mendes" wire:model="nome">
                @error('nome') <span class="teachers-field-error">{{ $message }}</span> @enderror

                <label for="teacher-discipline">Disciplina</label>
                <input id="teacher-discipline" type="text" placeholder="Ex: Matemática" wire:model="disciplina">
                @error('disciplina') <span class="teachers-field-error">{{ $message }}</span> @enderror

                <label for="teacher-email">Email</label>
                <input id="teacher-email" type="email" placeholder="Ex: professor@escola.com" wire:model="email">
                @error('email') <span class="teachers-field-error">{{ $message }}</span> @enderror

                <label for="teacher-phone">Telefone</label>
                <input id="teacher-phone" type="text" placeholder="Ex: (11) 99999-0000" wire:model="telefone">
                @error('telefone') <span class="teachers-field-error">{{ $message }}</span> @enderror

                <label for="teacher-status">Situação</label>
                <select id="teacher-status" wire:model="situacao">
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                    <option value="Afastado">Afastado</option>
                </select>
                @error('situacao') <span class="teachers-field-error">{{ $message }}</span> @enderror

                <label>Vincular Turmas</label>
                <div class="teachers-turmas-list">
                    @forelse($this->getAllTurmas() as $turma)
                        <label class="teachers-turma-item">
                            <input type="checkbox" wire:model="turmasSelecionadas" value="{{ $turma->id }}">
                            <span>{{ $turma->nome }} ({{ $turma->serie }})</span>
                        </label>
                    @empty
                        <span style="color: var(--school-muted); font-size: 13px;">Nenhuma turma cadastrada.</span>
                    @endforelse
                </div>

                <div class="teachers-actions">
                    <button type="submit" class="teachers-btn-primary">
                        {{ $editingId ? 'Salvar alterações' : 'Cadastrar' }}
                    </button>

                    @if($editingId)
                        <button type="button" class="teachers-btn-secondary" wire:click="resetForm">
                            Cancelar
                        </button>
                    @endif
                </div>
            </form>

            <section class="teachers-card">
                <div class="teachers-list-header">
                    <div>
                        <h3>Lista de professores</h3>
                        <p>Dados salvos no banco de dados.</p>
                    </div>

                    <input
                        class="teachers-search"
                        type="search"
                        placeholder="Buscar por nome, disciplina, email..."
                        wire:model.live="search"
                    >
                </div>

                <div class="teachers-table-wrapper">
                    <table class="teachers-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Disciplina</th>
                                <th>Turmas</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Situação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($professores as $professor)
                                <tr>
                                    <td>{{ $professor->nome }}</td>
                                    <td>{{ $professor->disciplina }}</td>
                                    <td>
                                        <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                            @forelse($professor->turmas as $turma)
                                                <span class="teachers-status-pill teachers-status-ativo" style="background: #e0f2fe; color: #0369a1; font-size: 11px;">
                                                    {{ $turma->nome }}
                                                </span>
                                            @empty
                                                <span style="color: var(--school-muted); font-size: 11px;">Nenhuma</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>{{ $professor->email }}</td>
                                    <td>{{ $professor->telefone ?? '—' }}</td>
                                    <td>
                                        <span class="teachers-status-pill teachers-status-{{ strtolower($professor->situacao) }}">
                                            {{ $professor->situacao }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="teachers-action-cell">
                                            <button
                                                type="button"
                                                class="teachers-btn-edit"
                                                wire:click="edit({{ $professor->id }})"
                                            >
                                                Editar
                                            </button>

                                            <button
                                                type="button"
                                                class="teachers-btn-danger"
                                                wire:click="delete({{ $professor->id }})"
                                                wire:confirm="Deseja excluir o professor {{ $professor->nome }}?"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="teachers-empty-state">
                                            Nenhum professor cadastrado ainda.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </div>
</x-filament-panels::page>
