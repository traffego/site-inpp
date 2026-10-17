<?php
/**
 * Admin: Documentos Financeiros (Transparência - Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$page_title = 'Admin — Documentos Financeiros';
$breadcrumb = [
    ['label' => 'Dashboard',     'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Transparência', 'url' => base_url('/admin/transparencia/painel.php')],
    ['label' => 'Financeiro'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $data   = $_POST['data_documento'] ?? '';
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        if (!$titulo || !$data) { flash('error', 'Título e data são obrigatórios.'); redirect(base_url('/admin/transparencia/financeiro.php')); }
        if (empty($_FILES['arquivo']['name'])) { flash('error', 'Selecione um arquivo.'); redirect(base_url('/admin/transparencia/financeiro.php')); }
        $up = upload_file($_FILES['arquivo'], UPLOAD_PATH . '/transparencia', ['pdf']);
        if (!$up['success']) { flash('error', 'Erro no upload: ' . $up['error']); redirect(base_url('/admin/transparencia/financeiro.php')); }
        db_insert('tab_documentos_financeiro', ['data_documento' => $data, 'titulo' => $titulo, 'resumo' => $resumo, 'arquivo' => $up['filename'], 'habilitado' => 1]);
        flash('success', 'Documento financeiro publicado com sucesso!');
    } elseif ($action === 'toggle_habilitado') {
        $did = (int)($_POST['doc_id'] ?? 0);
        if ($did) {
            $doc = db_fetch('SELECT habilitado FROM tab_documentos_financeiro WHERE id = ?', [$did]);
            if ($doc) {
                $novo_status = $doc['habilitado'] ? 0 : 1;
                db_update('tab_documentos_financeiro', ['habilitado' => $novo_status], 'id = ?', [$did]);
                flash('success', 'Status atualizado: ' . ($novo_status ? '🟢 Habilitado (Público)' : '⚪ Desabilitado (Oculto)'));
            }
        }
    } elseif ($action === 'delete') {
        $did = (int)($_POST['doc_id'] ?? 0);
        if ($did) {
            $doc = db_fetch('SELECT arquivo FROM tab_documentos_financeiro WHERE id = ?', [$did]);
            if ($doc) { @unlink(UPLOAD_PATH . '/transparencia/' . $doc['arquivo']); db_delete('tab_documentos_financeiro', 'id = ?', [$did]); flash('success', 'Documento removido.'); }
        }
    }
    redirect(base_url('/admin/transparencia/financeiro.php'));
}

$docs = db_fetch_all('SELECT * FROM tab_documentos_financeiro ORDER BY data_documento DESC');
$total_cadastrados = count($docs);
$total_ativos      = count(array_filter($docs, fn($d) => !empty($d['habilitado'])));

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Transparência Pública</div>
            <h2 class="page-title">Demonstrações Financeiras &amp; Balanços</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <span class="badge bg-primary-lt fs-6 px-3 py-2">
                <?= $total_ativos ?> de <?= $total_cadastrados ?> públicos no portal
            </span>
        </div>
    </div>
</div>

<!-- Abas de Navegação de Transparência -->
<div class="card mb-3">
    <div class="card-body py-2">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="/admin/transparencia/painel.php" class="nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    Painel Geral
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/transparencia/financeiro.php" class="nav-link active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Documentos Financeiros (<?= $total_cadastrados ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/transparencia/termos.php" class="nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    Termos de Colaboração
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="row row-cards">
    <!-- Lista de Documentos Financeiros (Esquerda: 8 Colunas) -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Documentos Cadastrados</h3>
                    <p class="card-subtitle mb-0">Demonstrativos contábeis e relatórios de auditoria</p>
                </div>
                <div class="card-actions">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="filtro-financeiro" class="form-control form-control-sm" placeholder="Filtrar títulos...">
                    </div>
                </div>
            </div>

            <?php if (empty($docs)): ?>
                <div class="card-body text-center text-secondary py-5">
                    Nenhum documento financeiro cadastrado ainda.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped" id="tabela-financeiro">
                        <thead>
                            <tr>
                                <th style="width:105px">Data</th>
                                <th>Título / Resumo</th>
                                <th style="width:110px; text-align:center;">Visibilidade</th>
                                <th style="width:85px">Arquivo</th>
                                <th style="width:80px; text-align:right;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docs as $d): ?>
                            <tr class="linha-doc">
                                <td class="text-secondary small font-monospace"><?= format_date($d['data_documento']) ?></td>
                                <td>
                                    <div class="font-weight-medium"><?= e($d['titulo']) ?></div>
                                    <?php if ($d['resumo']): ?>
                                        <div class="text-secondary small text-truncate" style="max-width: 320px;"><?= e($d['resumo']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="toggle_habilitado">
                                        <input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= !empty($d['habilitado']) ? 'btn-success' : 'btn-outline-secondary' ?>" title="Clique para alternar visibilidade pública">
                                            <?= !empty($d['habilitado']) ? '🟢 Ativo' : '⚪ Oculto' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="/uploads/transparencia/<?= e($d['arquivo']) ?>" target="_blank" class="btn btn-outline-danger btn-sm" title="Abrir PDF">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        PDF
                                    </a>
                                </td>
                                <td class="text-end">
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Confirma a exclusão deste documento financeiro?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="btn btn-ghost-danger btn-sm">Excluir</button>
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

    <!-- Adicionar Documento Financeiro (Direita: 4 Colunas) -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Adicionar Documento</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="data_documento" class="form-label">Data do Documento *</label>
                        <input type="date" id="data_documento" name="data_documento" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Documento *</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required placeholder="Ex: Balanço Patrimonial 2025">
                    </div>

                    <div class="mb-3">
                        <label for="resumo" class="form-label">Resumo / Descrição</label>
                        <textarea id="resumo" name="resumo" class="form-control" rows="3" placeholder="Descrição ou nota explicativa opcional..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="arquivo_fin" class="form-label">Arquivo PDF *</label>
                        <input type="file" id="arquivo_fin" name="arquivo" class="form-control" accept=".pdf" required>
                        <small class="form-hint">Formato obrigatório: .PDF</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Salvar Documento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('filtro-financeiro')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#tabela-financeiro tbody tr.linha-doc');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
