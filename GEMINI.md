# Projeto Full Stack Pós - Sistema de Gestão Escolar

Este documento descreve a arquitetura, convenções e fluxos de trabalho do projeto.

## Tecnologias Principais

- **Framework:** Laravel 13.x (Experimental)
- **Painel Administrativo:** Filament 5.x (Experimental)
- **Banco de Dados:** MySQL
- **Frontend:** Vite, TailwindCSS 4.0
- **Testes:** Pest PHP 4.7
- **Containerização:** Docker (Docker Compose)

## Arquitetura e Estrutura de Pastas

O projeto segue a estrutura padrão do Laravel, com as seguintes particularidades do Filament:

- `app/Filament/Resources`: Contém os recursos do painel administrativo.
- `app/Filament/Pages`: Contém páginas customizadas (ex: `Professores`).
- `app/Filament/Resources/{Resource}/Schemas` e `Tables`: Convenção para separação de esquemas de formulários e definições de tabelas (ex: `AlunoForm.php`, `AlunosTable.php`).
- `app/Models`: Modelos Eloquent (`User`, `Aluno`, `Professor`, `Turma`).

## Convenções de Desenvolvimento

- **Nomenclatura:** Seguir o padrão PSR e as convenções do Laravel (CamelCase para classes, snake_case para variáveis e colunas de banco).
- **Filament:**
    - Preferir o uso de `Section` para agrupar campos em formulários.
    - Utilizar `navigationGroup` para organizar o menu lateral ("Acadêmico", "Escola").
    - Cor primária do painel: `Amber`.
- **Páginas Customizadas:** Quando a lógica do Filament for insuficiente, utilizar páginas customizadas com Blade/Livewire e CSS próprio, conforme visto em `Professores.php`.
- **Testes:** Novos recursos devem ser acompanhados de testes utilizando Pest.

## Ferramentas e Comandos (Makefile)

O projeto utiliza um `Makefile` para facilitar tarefas comuns:

- `make rebuild`: Reconstrói os containers Docker sem cache.
- `make restart`: Reinicia os containers.
- `make p`: Acesso direto ao terminal (bash) do container PHP (`escola_app_php`).
- `make admin-reset`: Reseta o banco de dados e cria um usuário administrador padrão (`admin@escola.com` / `admin1234`).
- `make deploy-dev`: Executa o script de deploy para desenvolvimento.

## Variáveis de Ambiente

- `.env.local`: Configurações locais do ambiente.
- `.env.example`: Modelo de variáveis de ambiente.

## Deploy

O deploy para o ambiente de desenvolvimento é gerenciado pelo script `deploy-dev.sh`.
