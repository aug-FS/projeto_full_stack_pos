<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão Escolar</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<header>
    <h1>Gestão Escolar</h1>

    <nav>
        <a href="{{ route('landing') }}">Início</a>
        <a href="#funcionalidades">Funcionalidades</a>
        <a href="#contato">Informações</a>
        <a href="/admin">Área Administrativa</a>
    </nav>
</header>

<section class="hero">
    <div class="slide"></div>
    <div class="slide"></div>
    <div class="slide"></div>

    <div class="overlay">
        <h2>Gerencie sua escola com eficiência</h2>
        <p>Controle alunos, turmas, matrículas e muito mais em um só sistema</p>

        <button class="btn" onclick="document.getElementById('contato').scrollIntoView({ behavior: 'smooth' })">
            Começar Agora
        </button>
    </div>
</section>

<section class="container" id="funcionalidades">
    <h2>Funcionalidades</h2>
    <br>

    <div class="cards">
        <div class="card">
            <h3>Cadastro de Alunos</h3>
            <p>Gerencie nome, matrícula, turma e status dos alunos.</p>
        </div>

        <div class="card">
            <h3>Controle de Turmas</h3>
            <p>Organize alunos por turmas de forma simples.</p>
        </div>

        <div class="card">
            <h3>Gestão Completa</h3>
            <p>Tenha visão total da sua instituição em um só lugar.</p>
        </div>
    </div>
</section>

<section class="container" id="contato">
    <h2>Entre em Contato</h2>

    @if (session('success'))
        <p style="text-align: center; margin-bottom: 20px; color: green;">
            {{ session('success') }}
        </p>
    @endif

    <form method="POST" action="{{ route('landing.contact') }}">
        @csrf

        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" placeholder="Seu nome" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="Seu email" required>

        <label for="mensagem">Mensagem</label>
        <textarea id="mensagem" name="mensagem" rows="5" placeholder="Sua mensagem" required></textarea>

        <button class="btn" type="submit">Enviar</button>
    </form>
</section>

<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Sobre</h3>
            <p>Sistema de Gestão Escolar para controle de alunos, turmas e informações acadêmicas.</p>
        </div>

        <div class="footer-section">
            <h3>Contato</h3>
            <p>📧 escolaInteligente@gmail.com</p>
            <p>📞 (41) 99999-9999</p>
            <p>📸 @escola.inteligente</p>
        </div>

        <div class="footer-section">
            <h3>Equipe</h3>
            <p>Augusto</p>
            <p>Matheus</p>
            <p>Luisa</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} Sistema de Gestão Escolar</p>
    </div>
</footer>

</body>
</html>
