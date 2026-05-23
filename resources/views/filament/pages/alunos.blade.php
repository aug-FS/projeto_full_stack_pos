<x-filament-panels::page>
    <style>
        .students-page {
            --school-primary: #2563eb;
            --school-primary-dark: #1d4ed8;
            --school-bg: #f8fafc;
            --school-card: #ffffff;
            --school-text: #0f172a;
            --school-muted: #64748b;
            --school-border: #e2e8f0;
            --school-danger: #dc2626;
            --school-success: #16a34a;
            --school-warning: #d97706;
        }

        .students-hero {
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

        .students-hero h2 {
            font-size: 34px;
            font-weight: 800;
            margin: 10px 0 8px;
        }

        .students-hero p {
            margin: 0;
            opacity: 0.9;
        }

        .students-badge {
            background: rgba(255, 255, 255, 0.18);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
        }

        .students-counter {
            background: rgba(255, 255, 255, 0.16);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            min-width: 160px;
        }

        .students-counter strong {
            display: block;
            font-size: 42px;
            line-height: 1;
        }

        .students-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 24px;
        }

        .students-card {
            background: var(--school-card);
            border: 1px solid var(--school-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .students-card h3 {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            color: var(--school-text);
        }

        .students-card p {
            margin: 6px 0 20px;
            color: var(--school-muted);
        }

        .students-card label {
            display: block;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--school-text);
        }

        .students-card input,
        .students-card select {
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

        .students-field-error {
            color: var(--school-danger);
            font-size: 13px;
            margin-bottom: 12px;
            display: block;
        }

        .students-actions {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }

        .students-btn-primary,
        .students-btn-secondary,
        .students-btn-danger,
        .students-btn-edit {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 800;
            transition: 0.2s;
        }

        .students-btn-primary {
            background: var(--school-primary);
            color: white;
            flex: 1;
        }

        .students-btn-primary:hover {
            background: var(--school-primary-dark);
        }

        .students-btn-secondary {
            background: #eef2ff;
            color: var(--school-primary);
        }

        .students-btn-edit {
            background: #e0f2fe;
            color: #0369a1;
        }

        .students-btn-danger {
            background: #fee2e2;
            color: var(--school-danger);
        }

        .students-list-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .students-search {
            max-width: 360px;
        }

        .students-table-wrapper {
            overflow-x: auto;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table th {
            text-align: left;
            color: var(--school-muted);
            font-size: 14px;
            border-bottom: 1px solid var(--school-border);
            padding: 12px;
            white-space: nowrap;
        }

        .students-table td {
            border-bottom: 1px solid var(--school-border);
            padding: 12px;
            vertical-align: middle;
            color: var(--school-text);
        }

        .students-status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
        }

        .students-status-ativo {
            background: #dcfce7;
            color: var(--school-success);
        }

        .students-status-inativo {
            background: #f3f4f6;
            color: var(--school-muted);
        }

        .students-status-transferido {
            background: #fef3c7;
            color: var(--school-warning);
        }

        .students-action-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .students-empty-state {
            text-align: center;
            padding: 30px;
            color: var(--school-muted);
        }

        @media (max-width: 1024px) {
            .students-grid,
            .students-hero {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: stretch;
            }

            .students-list-header {
                flex-direction: column;
            }

            .students-search {
                max-width: 100%;
            }
        }
    </style>

    @php $alunos = $this->getAlunos(); @endphp

    <div class="students-page">
        <section class="students-hero">
            <div>
                <span class="students-badge">Banco de dados</span>
                <h2>Alunos</h2>
                <p>Cadastre, edite, pesquise e remova alunos diretamente no banco de dados.</p>
            </div>

            <div class="students-counter">
                <strong>{{ $this->getTotalAlunos() }}</strong>
                <span>alunos cadastrados</span>
            </div>
        </section>

        <section class="students-grid">
            <form wire:submit="save" class="students-card">
                <div>
                    <h3>{{ $editingId ? 'Editar aluno' : 'Novo aluno' }}</h3>
                    <p>Preencha os dados abaixo.</p>
                </div>

                <label for="student-name">Nome</label>
                <input id="student-name" type="text" placeholder="Ex: Ana Souza" wire:model="nome">
                @error('nome') <span class="students-field-error">{{ $message }}</span> @enderror

                <label for="student-class">Turma</label>
                <input id="student-class" type="text" placeholder="Ex: 3º Ano A" wire:model="turma">
                @error('turma') <span class="students-field-error">{{ $message }}</span> @enderror

                <label for="student-registration">Matrícula</label>
                <input id="student-registration" type="text" placeholder="Ex: 2026001" wire:model="matricula">
                @error('matricula') <span class="students-field-error">{{ $message }}</span> @enderror

                <label for="student-email">Email</label>
                <input id="student-email" type="email" placeholder="Ex: aluno@escola.com" wire:model="email">
                @error('email') <span class="students-field-error">{{ $message }}</span> @enderror

                <label for="student-status">Situação</label>
                <select id="student-status" wire:model="situacao">
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                    <option value="Transferido">Transferido</option>
                </select>
                @error('situacao') <span class="students-field-error">{{ $message }}</span> @enderror

                <div class="students-actions">
                    <button type="submit" class="students-btn-primary">
                        {{ $editingId ? 'Salvar alterações' : 'Cadastrar' }}
                    </button>

                    @if($editingId)
                        <button type="button" class="students-btn-secondary" wire:click="resetForm">
                            Cancelar
                        </button>
                    @endif
                </div>
            </form>

            <section class="students-card">
                <div class="students-list-header">
                    <div>
                        <h3>Lista de alunos</h3>
                        <p>Dados salvos no banco de dados.</p>
                    </div>

                    <input
                        class="students-search"
                        type="search"
                        placeholder="Buscar por nome, turma, matrícula..."
                        wire:model.live="search"
                    >
                </div>

                <div class="students-table-wrapper">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Turma</th>
                                <th>Matrícula</th>
                                <th>Email</th>
                                <th>Situação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($alunos as $aluno)
                                <tr>
                                    <td>{{ $aluno->nome }}</td>
                                    <td>{{ $aluno->turma }}</td>
                                    <td>{{ $aluno->matricula }}</td>
                                    <td>{{ $aluno->email }}</td>
                                    <td>
                                        <span class="students-status-pill students-status-{{ strtolower($aluno->situacao) }}">
                                            {{ $aluno->situacao }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="students-action-cell">
                                            <button
                                                type="button"
                                                class="students-btn-edit"
                                                wire:click="edit({{ $aluno->id }})"
                                            >
                                                Editar
                                            </button>

                                            <button
                                                type="button"
                                                class="students-btn-danger"
                                                wire:click="delete({{ $aluno->id }})"
                                                wire:confirm="Deseja excluir o aluno {{ $aluno->nome }}?"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="students-empty-state">
                                            Nenhum aluno cadastrado ainda.
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
