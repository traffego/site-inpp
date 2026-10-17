<?php
/**
 * Admin: Funções do Projeto
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$projeto_id = (int)($_GET['projeto_id'] ?? 0);
if (!$projeto_id) { flash('error', 'Projeto não especificado.'); redirect(base_url('/admin/projetos/listar.php')); }

$projeto = db_fetch('SELECT * FROM tab_projetos WHERE id = ?', [$projeto_id]);
if (!$projeto) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    if ($action === 'link') {
        $funcao_id = (int)($_POST['funcao_id'] ?? 0);
        if ($funcao_id) {
            // Evitar duplicata
            $existe = db_count('SELECT COUNT(*) FROM tab_projetos_funcao WHERE id_projeto = ? AND id_funcao = ?', [$projeto_id, $funcao_id]);
            if (!$existe) {
                db_insert('tab_projetos_funcao', [
                    'id_projeto'    => $projeto_id,
                    'id_funcao'     => $funcao_id,
                    'cadastrado_por'=> auth_user()['id'],
                    'data_cadastro' => date('Y-m-d'),
                ]);
                flash('success', 'Função vinculada com sucesso!');
            } else {
                flash('error', 'Esta função já está vinculada ao projeto.');
            }
        }
    } elseif ($action === 'unlink') {
        $funcao_id = (int)($_POST['funcao_id'] ?? 0);
        if ($funcao_id) {
            db_delete('tab_projetos_funcao', 'id_projeto = ? AND id_funcao = ?', [$projeto_id, $funcao_id]);
            flash('success', 'Função desvinculada.');
        }
    }
    redirect(base_url('/admin/projetos/funcoes.php?projeto_id=' . $projeto_id));
}

$funcoes_vinculadas = db_fetch_all(
    'SELECT DISTINCT f.id, f.funcao, f.ativo FROM tab_curriculos_funcao f
     INNER JOIN tab_projetos_funcao pf ON f.id = pf.id_funcao
     WHERE pf.id_projeto = ? ORDER BY f.funcao',
    [$projeto_id]
);

$ids_vinculados = array_column($funcoes_vinculadas, 'id');

$funcoes_disponiveis = db_fetch_all(
    'SELECT * FROM tab_curriculos_funcao WHERE ativo = 1 ORDER BY funcao'
);
$funcoes_disponiveis = array_filter($funcoes_disponiveis, fn($f) => !in_array($f['id'], $ids_vinculados));

$page_title = 'Admin — Funções do Projeto';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Projetos',  'url' => base_url('/admin/projetos/listar.php')],
    ['label' => 'Funções: ' . truncate($projeto['nome_projeto'], 35)],
];

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Vínculo de Cargos &amp; Equipe</div>
            <h2 class="page-title text-truncate">Funções do Projeto: <?= e($projeto['nome_projeto']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="<?= base_url('/admin/projetos/documentos.php?projeto_id=' . $projeto_id) ?>" class="btn btn-outline-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Documentos
                </a>
                <a href="<?= base_url('/admin/projetos/editar.php?id=' . $projeto_id) ?>" class="btn btn-outline-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Voltar ao Projeto
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <!-- Funções vinculadas -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    Funções Vinculadas
                    <span class="badge bg-primary-lt ms-2 font-monospace"><?= count($funcoes_vinculadas) ?></span>
                </h3>
            </div>
            <?php if (empty($funcoes_vinculadas)): ?>
                <div class="card-body text-center text-secondary py-5">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-secondary mb-2 opacity-50"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <div>Nenhuma função profissional vinculada a este projeto ainda.</div>
                    <div class="small text-secondary">Utilize o painel ao lado para associar cargos para este edital/projeto.</div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Nome da Função</th>
                                <th style="width: 100px; text-align: center;">Status</th>
                                <th style="width: 90px; text-align: right;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($funcoes_vinculadas as $f): ?>
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
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Desvincular esta função do projeto?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="unlink">
                                        <input type="hidden" name="funcao_id" value="<?= $f['id'] ?>">
                                        <button type="submit" class="btn btn-ghost-danger btn-sm" title="Desvincular do projeto">
                                            Remover
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Vincular nova função -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Vincular Nova Função</h3>
            </div>
            <div class="card-body">
                <?php if (empty($funcoes_disponiveis)): ?>
                    <div class="text-secondary text-center py-4">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-success mb-2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <p class="mb-2">Todas as funções ativas cadastradas já estão vinculadas a este projeto.</p>
                        <a href="<?= base_url('/admin/curriculos/funcoes.php') ?>" class="btn btn-sm btn-outline-secondary">
                            Cadastrar Novos Cargos
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-secondary small mb-3">
                        Selecione um cargo do banco de funções para habilitar candidaturas e seleção para este projeto.
                    </p>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="link">
                        <div class="mb-3">
                            <label for="funcao_id" class="form-label required">Selecione o Cargo/Função</label>
                            <select id="funcao_id" name="funcao_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($funcoes_disponiveis as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= e($f['funcao']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Vincular ao Projeto
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= base_url('/admin/curriculos/funcoes.php') ?>" class="small text-secondary d-flex align-items-center">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Gerenciar catálogo global de cargos
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
