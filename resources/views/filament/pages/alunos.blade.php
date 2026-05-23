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
            margin-bottom: 16px;
            font-size: 15px;
            background: white;
            color: var(--school-text);
            box-sizing: border-box;
        }

        .students-actions {
            display: flex;
            gap: 12px;
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
            display: none;
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
            display: none;
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

    <div class="students-page" data-students-admin-page>
        <section class="students-hero">
            <div>
                <span class="students-badge">LocalStorage</span>
                <h2>Alunos</h2>
                <p>Cadastre, edite, pesquise e remova alunos diretamente no navegador.</p>
            </div>

            <div class="students-counter">
                <strong id="students-count">0</strong>
                <span>alunos cadastrados</span>
            </div>
        </section>

        <section class="students-grid">
            <form id="student-form" class="students-card">
                <input type="hidden" id="student-id">

                <div>
                    <h3 id="form-title">Novo aluno</h3>
                    <p>Preencha os dados abaixo.</p>
                </div>

                <label for="student-name">Nome</label>
                <input id="student-name" type="text" placeholder="Ex: Ana Souza" required>

                <label for="student-class">Turma</label>
                <input id="student-class" type="text" placeholder="Ex: 3º Ano A" required>

                <label for="student-registration">Matrícula</label>
                <input id="student-registration" type="text" placeholder="Ex: 2026001" required>

                <label for="student-email">Email</label>
                <input id="student-email" type="email" placeholder="Ex: aluno@escola.com" required>

                <label for="student-status">Situação</label>
                <select id="student-status" required>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                    <option value="Transferido">Transferido</option>
                </select>

                <div class="students-actions">
                    <button type="submit" class="students-btn-primary" id="submit-button">Cadastrar</button>
                    <button type="button" class="students-btn-secondary" id="cancel-edit-button">Cancelar</button>
                </div>
            </form>

            <section class="students-card">
                <div class="students-list-header">
                    <div>
                        <h3>Lista de alunos</h3>
                        <p>Dados salvos no LocalStorage do navegador.</p>
                    </div>

                    <input class="students-search" id="student-search" type="search" placeholder="Buscar por nome, turma, matrícula...">
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

                        <tbody id="students-table-body"></tbody>
                    </table>
                </div>

                <div id="empty-state" class="students-empty-state">
                    Nenhum aluno cadastrado ainda.
                </div>
            </section>
        </section>
    </div>

    <script>
        (() => {
            const page = document.querySelector('[data-students-admin-page]');

            if (!page) {
                return;
            }

            const storageKey = 'escola_alunos';

            const form = document.getElementById('student-form');
            const idInput = document.getElementById('student-id');
            const nameInput = document.getElementById('student-name');
            const classInput = document.getElementById('student-class');
            const registrationInput = document.getElementById('student-registration');
            const emailInput = document.getElementById('student-email');
            const statusInput = document.getElementById('student-status');
            const tableBody = document.getElementById('students-table-body');
            const searchInput = document.getElementById('student-search');
            const emptyState = document.getElementById('empty-state');
            const countElement = document.getElementById('students-count');
            const formTitle = document.getElementById('form-title');
            const submitButton = document.getElementById('submit-button');
            const cancelEditButton = document.getElementById('cancel-edit-button');

            const getStudents = () => {
                try {
                    return JSON.parse(localStorage.getItem(storageKey)) || [];
                } catch {
                    return [];
                }
            };

            const saveStudents = (students) => {
                localStorage.setItem(storageKey, JSON.stringify(students));
            };

            const createId = () => {
                if (window.crypto && window.crypto.randomUUID) {
                    return window.crypto.randomUUID();
                }

                return String(Date.now());
            };

            const escapeHtml = (value) => {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            };

            const normalize = (value) => String(value).toLowerCase().trim();

            const getStatusClass = (status) => {
                return `students-status-${normalize(status).replaceAll(' ', '-')}`;
            };

            const resetForm = () => {
                form.reset();
                idInput.value = '';
                formTitle.textContent = 'Novo aluno';
                submitButton.textContent = 'Cadastrar';
                cancelEditButton.style.display = 'none';
                statusInput.value = 'Ativo';
            };

            const renderStudents = () => {
                const students = getStudents();
                const search = normalize(searchInput.value);

                const filteredStudents = students.filter((student) => {
                    return normalize(student.nome).includes(search)
                        || normalize(student.turma).includes(search)
                        || normalize(student.matricula).includes(search)
                        || normalize(student.email).includes(search)
                        || normalize(student.situacao).includes(search);
                });

                countElement.textContent = students.length;
                tableBody.innerHTML = '';

                emptyState.style.display = filteredStudents.length ? 'none' : 'block';

                filteredStudents.forEach((student) => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td>${escapeHtml(student.nome)}</td>
                        <td>${escapeHtml(student.turma)}</td>
                        <td>${escapeHtml(student.matricula)}</td>
                        <td>${escapeHtml(student.email)}</td>
                        <td>
                            <span class="students-status-pill ${getStatusClass(student.situacao)}">
                                ${escapeHtml(student.situacao)}
                            </span>
                        </td>
                        <td>
                            <div class="students-action-cell">
                                <button type="button" class="students-btn-edit" data-action="edit" data-id="${student.id}">
                                    Editar
                                </button>

                                <button type="button" class="students-btn-danger" data-action="delete" data-id="${student.id}">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    `;

                    tableBody.appendChild(row);
                });
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const students = getStudents();

                const student = {
                    id: idInput.value || createId(),
                    nome: nameInput.value.trim(),
                    turma: classInput.value.trim(),
                    matricula: registrationInput.value.trim(),
                    email: emailInput.value.trim(),
                    situacao: statusInput.value,
                    updatedAt: new Date().toISOString(),
                };

                const duplicatedRegistration = students.some((item) => {
                    return item.matricula === student.matricula && item.id !== student.id;
                });

                if (duplicatedRegistration) {
                    alert('Já existe um aluno com essa matrícula.');
                    return;
                }

                const editing = Boolean(idInput.value);

                if (editing) {
                    const updatedStudents = students.map((item) => {
                        return item.id === student.id ? { ...item, ...student } : item;
                    });

                    saveStudents(updatedStudents);
                } else {
                    student.createdAt = new Date().toISOString();
                    saveStudents([...students, student]);
                }

                resetForm();
                renderStudents();
            });

            tableBody.addEventListener('click', (event) => {
                const button = event.target.closest('button[data-action]');

                if (!button) {
                    return;
                }

                const id = button.dataset.id;
                const action = button.dataset.action;
                const students = getStudents();
                const student = students.find((item) => item.id === id);

                if (!student) {
                    return;
                }

                if (action === 'edit') {
                    idInput.value = student.id;
                    nameInput.value = student.nome;
                    classInput.value = student.turma;
                    registrationInput.value = student.matricula;
                    emailInput.value = student.email;
                    statusInput.value = student.situacao;

                    formTitle.textContent = 'Editar aluno';
                    submitButton.textContent = 'Salvar alterações';
                    cancelEditButton.style.display = 'inline-block';

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                if (action === 'delete') {
                    const confirmed = confirm(`Deseja excluir o aluno ${student.nome}?`);

                    if (!confirmed) {
                        return;
                    }

                    const updatedStudents = students.filter((item) => item.id !== id);

                    saveStudents(updatedStudents);
                    resetForm();
                    renderStudents();
                }
            });

            cancelEditButton.addEventListener('click', resetForm);
            searchInput.addEventListener('input', renderStudents);

            renderStudents();
        })();
    </script>
</x-filament-panels::page>
