<?php
/**
 * Admin: Funções/Cargos (CRUD)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Funções/Cargos';
$breadcrumb = [
    ['label' => 'Dashboard',  'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Currículos', 'url' => base_url('/admin/curriculos/listar.php')],
    ['label' => 'Funções'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'criar') {
        $funcao = trim($_POST['funcao'] ?? '');
        if ($funcao) { db_insert('tab_curriculos_funcao', ['funcao' => $funcao, 'ativo' => 1]); flash('success', 'Função criada!'); }
    } elseif ($action === 'editar') {
        $fid    = (int)($_POST['id'] ?? 0);
        $funcao = trim($_POST['funcao'] ?? '');
        if ($fid && $funcao) { db_update('tab_curriculos_funcao', ['funcao' => $funcao], 'id = ?', [$fid]); flash('success', 'Função atualizada!'); }
    } elseif ($action === 'toggle') {
        $fid = (int)($_POST['id'] ?? 0);
        if ($fid) {
            $f = db_fetch('SELECT ativo FROM tab_curriculos_funcao WHERE id = ?', [$fid]);
            if ($f) db_update('tab_curriculos_funcao', ['ativo' => $f['ativo'] ? 0 : 1], 'id = ?', [$fid]);
            flash('success', 'Status alterado!');
        }
    }
    redirect(base_url('/admin/curriculos/funcoes.php'));
}

$funcoes = db_fetch_all('SELECT * FROM tab_curriculos_funcao ORDER BY funcao');
$editar_id = (int)($_GET['editar'] ?? 0);
$editar_funcao = $editar_id ? db_fetch('SELECT * FROM tab_curriculos_funcao WHERE id = ?', [$editar_id]) : null;

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Banco de Talentos &amp; Recursos Humanos</div>
            <h2 class="page-title">Catálogo de Funções &amp; Cargos</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/curriculos/listar.php') ?>" class="btn btn-outline-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Voltar aos Currículos
            </a>
        </div>
    </div>
</div>

<div class="row row-cards">
    <!-- Lista de Funções -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    Funções Cadastradas
                    <span class="badge bg-primary-lt ms-2 font-monospace"><?= count($funcoes) ?></span>
                </h3>
            </div>
            <?php if (empty($funcoes)): ?>
                <div class="card-body text-center text-secondary py-5">
                    Nenhuma função cadastrada ainda. Utilize o formulário ao lado para adicionar o primeiro cargo.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Nome da Função / Cargo</th>
                                <th style="width: 100px; text-align: center;">Status</th>
                                <th style="width: 150px; text-align: right;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($funcoes as $f): ?>
                            <tr>
                                <td>
                                    <div class="fw-medium text-reset"><?= e($f['funcao']) ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $f['ativo'] ? 'bg-success-lt' : 'bg-secondary-lt' ?>">
                                        <?= $f['ativo'] ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-list justify-content-end">
                                        <a href="?editar=<?= $f['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar nome">
                                            Editar
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                            <button type="submit" class="btn btn-sm <?= $f['ativo'] ? 'btn-ghost-warning' : 'btn-ghost-success' ?>" title="Alternar status de disponibilidade">
                                                <?= $f['ativo'] ? 'Desativar' : 'Ativar' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulário Criar/Editar -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $editar_funcao ? 'Editar Função' : 'Cadastrar Nova Função' ?>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <?php if ($editar_funcao): ?>
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" name="id" value="<?= $editar_funcao['id'] ?>">
                    <?php else: ?>
                        <input type="hidden" name="action" value="criar">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="funcao" class="form-label required">Título da Função / Especialidade</label>
                        <input type="text" id="funcao" name="funcao" class="form-control" required
                               value="<?= e($editar_funcao['funcao'] ?? '') ?>" placeholder="Ex: Coordenador Pedagógico">
                        <small class="form-hint">O nome que aparecerá nos formulários de inscrição e editais.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <?= $editar_funcao ? 'Salvar Alteração' : 'Criar Função' ?>
                        </button>
                        <?php if ($editar_funcao): ?>
                            <a href="<?= base_url('/admin/curriculos/funcoes.php') ?>" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
