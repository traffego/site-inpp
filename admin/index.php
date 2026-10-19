<?php
// admin/index.php — Página de Login do Painel Administrativo com Tema Tabler
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

// Se já está logado, vai pro dashboard
if (auth_check()) {
    redirect('/admin/dashboard.php');
}

$erro = flash('error');
$sucesso = flash('success');

// Processa o POST de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if (empty($usuario) || empty($senha)) {
        flash('error', 'Preencha o usuário e a senha.');
        redirect('/admin/index.php');
    }

    if (auth_login($usuario, $senha)) {
        redirect('/admin/dashboard.php');
    } else {
        flash('error', 'Credenciais inválidas ou conta bloqueada temporariamente.');
        redirect('/admin/index.php');
    }
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Entrar &bull; Painel Administrativo — Instituto Nacional de Políticas Públicas (INPP)</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css"/>
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
  </head>
  <body class="d-flex flex-column">
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="/" class="navbar-brand navbar-brand-autodark">
            <img src="/assets/img/logo_inpp.png" height="54" alt="INPP" style="display:inline-block; max-height:54px;">
          </a>
          <h1 class="h3 mt-3 mb-1">Instituto Nacional de Políticas Públicas</h1>
          <p class="text-secondary small">Acesso Restrito &bull; Gestão &amp; Controle Social</p>
        </div>
        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4 card-title">Entrar no Sistema</h2>
            <?php if ($erro): ?>
              <div class="alert alert-danger" role="alert"><?= e($erro) ?></div>
            <?php endif; ?>
            <?php if ($sucesso): ?>
              <div class="alert alert-success" role="alert"><?= e($sucesso) ?></div>
            <?php endif; ?>
            <form action="/admin/index.php" method="POST" autocomplete="off" novalidate>
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-label" for="usuario">Usuário ou E-mail</label>
                <input type="text" class="form-control" id="usuario" name="usuario" value="<?= e($_POST['usuario'] ?? '') ?>" placeholder="Digite seu usuário..." autocomplete="username" required autofocus>
              </div>
              <div class="mb-3">
                <label class="form-label" for="senha">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha..." autocomplete="current-password" required>
              </div>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Acessar Painel</button>
              </div>
            </form>
          </div>
        </div>
        <div class="text-center text-muted mt-3">
          <a href="/" class="text-muted small">&larr; Voltar ao portal institucional</a>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js" defer></script>
  </body>
</html>
