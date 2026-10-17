<?php
/**
 * Admin: Alterar Senha
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Alterar Senha';
$breadcrumb = [
    ['label' => 'Dashboard',    'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Configurações'],
    ['label' => 'Senha'],
];

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $senha_atual     = $_POST['senha_atual'] ?? '';
    $nova_senha      = $_POST['nova_senha'] ?? '';
    $confirmar_nova  = $_POST['confirmar_nova_senha'] ?? '';

    // Buscar hash atual
    $usuario = db_fetch('SELECT id, senha FROM tab_login WHERE id = ?', [auth_user()['id']]);

    if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
        $erros[] = 'Senha atual incorreta.';
    }
    if (strlen($nova_senha) < 8) {
        $erros[] = 'A nova senha deve ter pelo menos 8 caracteres.';
    }
    if ($nova_senha !== $confirmar_nova) {
        $erros[] = 'A confirmação da nova senha não confere.';
    }

    if (empty($erros)) {
        db_update(
            'tab_login',
            ['senha' => password_hash($nova_senha, PASSWORD_BCRYPT)],
            'id = ?',
            [$usuario['id']]
        );
        flash('success', 'Senha alterada com sucesso!');
        redirect(base_url('/admin/configuracoes/senha.php'));
    }
}

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Segurança da Conta</div>
            <h2 class="page-title">Alterar Senha de Acesso</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/dashboard.php') ?>" class="btn btn-outline-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Voltar ao Dashboard
            </a>
        </div>
    </div>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <h4 class="alert-title mb-1">Não foi possível alterar sua senha:</h4>
                <ul class="mb-0 ps-3">
                    <?php foreach ($erros as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<div class="row row-cards">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Credenciais de Autenticação</h3>
            </div>
            <div class="card-body">
                <form method="POST" id="form-senha">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="senha_atual" class="form-label required">Senha Atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" class="form-control" required autocomplete="current-password" placeholder="Digite sua senha em uso">
                    </div>

                    <div class="mb-3">
                        <label for="nova_senha" class="form-label required">Nova Senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" class="form-control" required autocomplete="new-password" minlength="8" placeholder="Pelo menos 8 caracteres">
                        <small class="form-hint">Recomendamos utilizar combinações de letras maiúsculas, números e símbolos.</small>
                    </div>

                    <div class="mb-4">
                        <label for="confirmar_nova_senha" class="form-label required">Confirmar Nova Senha</label>
                        <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" class="form-control" required autocomplete="new-password" minlength="8" placeholder="Repita a nova senha">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Atualizar Senha
                        </button>
                        <a href="<?= base_url('/admin/dashboard.php') ?>" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card de Boas Práticas de Segurança -->
    <div class="col-12 col-md-5 col-lg-6">
        <div class="card bg-body-tertiary">
            <div class="card-body">
                <h4 class="card-title d-flex align-items-center mb-3">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary me-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Recomendações de Segurança
                </h4>
                <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.7;">
                    <li>Utilize senhas exclusivas para este painel institucional.</li>
                    <li>Evite nomes pessoais, datas comemorativas ou sequências simples (ex: 123456).</li>
                    <li>Sua senha deve conter no mínimo <strong>8 caracteres</strong>.</li>
                    <li>Não compartilhe suas credenciais com outros membros da equipe; cada gestor deve possuir seu próprio acesso individual.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
