<?php
/**
 * Admin: Documentos do Projeto (Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$projeto_id = (int)($_GET['projeto_id'] ?? 0);
if (!$projeto_id) { flash('error', 'Projeto não especificado.'); redirect(base_url('/admin/projetos/listar.php')); }

$projeto = db_fetch('SELECT * FROM tab_projetos WHERE id = ?', [$projeto_id]);
if (!$projeto) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

$tipos_doc = db_fetch_all('SELECT * FROM tab_projetos_documentos_tipo ORDER BY id');

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    match ($action) {
        'add_group' => (function() use ($projeto_id) {
            $nome = trim($_POST['nome_grupo'] ?? '');
            $pos  = (int)($_POST['posicao'] ?? 0);
            if ($nome) {
                db_insert('tab_projetos_grupo_doc', ['id_projeto' => $projeto_id, 'nome_grupo' => $nome, 'posicao' => $pos]);
                flash('success', 'Grupo de documentos criado com sucesso!');
            }
        })(),

        'add_doc' => (function() use ($projeto_id) {
            $grupo_id = (int)($_POST['grupo_id'] ?? 0);
            $tipo_id  = (int)($_POST['tipo_id'] ?? 0);
            $nome_doc = trim($_POST['nome_documento'] ?? '');

            if (!$grupo_id || !$tipo_id || !$nome_doc) {
                flash('error', 'Preencha todos os campos do documento.'); return;
            }
            if (empty($_FILES['arquivo_doc']) || $_FILES['arquivo_doc']['error'] !== UPLOAD_ERR_OK) {
                flash('error', 'Selecione um arquivo PDF.'); return;
            }

            $upload = upload_file($_FILES['arquivo_doc'], UPLOAD_PATH . '/projetos', ['pdf']);
            if (!$upload['success']) { flash('error', 'Erro no upload: ' . $upload['error']); return; }

            db_insert('tab_projetos_documentos', [
                'id_projeto'         => $projeto_id,
                'id_grupo_documento' => $grupo_id,
                'id_tipo_documento'  => $tipo_id,
                'nome_documento'     => $nome_doc,
                'arquivo'            => $upload['filename'],
                'habilitado'         => 1,
            ]);
            flash('success', 'Documento publicado com sucesso!');
        })(),

        'toggle_habilitado' => (function() {
            $doc_id = (int)($_POST['doc_id'] ?? 0);
            if (!$doc_id) return;
            $doc = db_fetch('SELECT habilitado FROM tab_projetos_documentos WHERE id = ?', [$doc_id]);
            if ($doc) {
                $novo_status = $doc['habilitado'] ? 0 : 1;
                db_update('tab_projetos_documentos', ['habilitado' => $novo_status], 'id = ?', [$doc_id]);
                flash('success', 'Status atualizado: ' . ($novo_status ? '🟢 Habilitado (Público)' : '⚪ Desabilitado (Oculto)'));
            }
        })(),

        'delete_doc' => (function() {
            $doc_id = (int)($_POST['doc_id'] ?? 0);
            if (!$doc_id) return;
            $doc = db_fetch('SELECT arquivo FROM tab_projetos_documentos WHERE id = ?', [$doc_id]);
            if ($doc) {
                @unlink(UPLOAD_PATH . '/projetos/' . $doc['arquivo']);
                db_delete('tab_projetos_documentos', 'id = ?', [$doc_id]);
                flash('success', 'Documento removido.');
            }
        })(),

        'delete_group' => (function() {
            $grupo_id = (int)($_POST['grupo_id'] ?? 0);
            if (!$grupo_id) return;
            $docs = db_fetch_all('SELECT arquivo FROM tab_projetos_documentos WHERE id_grupo_documento = ?', [$grupo_id]);
            foreach ($docs as $d) @unlink(UPLOAD_PATH . '/projetos/' . $d['arquivo']);
            db_delete('tab_projetos_documentos', 'id_grupo_documento = ?', [$grupo_id]);
            db_delete('tab_projetos_grupo_doc', 'id = ?', [$grupo_id]);
            flash('success', 'Grupo e documentos associados removidos.');
        })(),

        default => null,
    };

    redirect(base_url('/admin/projetos/documentos.php?projeto_id=' . $projeto_id));
}

$grupos = db_fetch_all(
    'SELECT * FROM tab_projetos_grupo_doc WHERE id_projeto = ? ORDER BY posicao, id',
    [$projeto_id]
);

$page_title = 'Admin — Documentos do Projeto';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Projetos',  'url' => base_url('/admin/projetos/listar.php')],
    ['label' => 'Documentos: ' . truncate($projeto['nome_projeto'], 35)],
];

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-muted">Documentos &amp; Editais de Projeto</div>
            <h2 class="page-title"><?= e($projeto['nome_projeto']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/projetos/editar.php?id=' . $projeto_id) ?>" class="btn btn-outline-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Voltar ao Projeto
            </a>
        </div>
    </div>
</div>

<!-- GRUPOS EXISTENTES -->
<?php if (empty($grupos)): ?>
    <div class="card mb-4">
        <div class="card-body text-center text-muted py-5">
            Nenhum grupo de documentos cadastrado para este projeto. Utilize o formulário abaixo para criar o primeiro grupo.
        </div>
    </div>
<?php else: ?>
    <div class="row mb-4">
        <?php foreach ($grupos as $grupo): ?>
        <?php $docs = db_fetch_all('SELECT d.*, t.tipo_documento FROM tab_projetos_documentos d LEFT JOIN tab_projetos_documentos_tipo t ON d.id_tipo_documento = t.id WHERE d.id_grupo_documento = ? ORDER BY d.id', [$grupo['id']]); ?>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="text-primary me-2">📁</span>
                        <?= e($grupo['nome_grupo']) ?>
                        <span class="badge bg-secondary-lt ms-2 font-monospace">Ordem <?= (int)$grupo['posicao'] ?></span>
                    </h3>
                    <div class="card-actions">
                        <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este grupo e TODOS os seus documentos associados?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete_group">
                            <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Excluir Grupo</button>
                        </form>
                    </div>
                </div>

                <?php if (empty($docs)): ?>
                    <div class="card-body text-muted py-3 small">
                        Sem documentos vinculados a este grupo no momento.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th style="width:140px">Tipo</th>
                                    <th style="width:110px; text-align:center;">Visibilidade</th>
                                    <th style="width:90px">Arquivo</th>
                                    <th style="width:80px; text-align:right;">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($docs as $doc): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-medium"><?= e($doc['nome_documento']) ?></div>
                                        <div class="text-muted small"><?= e($doc['arquivo']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?= ($doc['tipo_documento'] ?? '') === 'Editais' ? 'bg-blue-lt' : 'bg-green-lt' ?>">
                                            <?= e($doc['tipo_documento'] ?? 'Geral') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle_habilitado">
                                            <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                            <button type="submit" class="btn btn-sm <?= !empty($doc['habilitado']) ? 'btn-success' : 'btn-outline-secondary' ?>" title="Alternar visibilidade pública">
                                                <?= !empty($doc['habilitado']) ? '🟢 Ativo' : '⚪ Oculto' ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="/uploads/projetos/<?= e($doc['arquivo']) ?>" target="_blank" class="btn btn-outline-danger btn-sm" title="Abrir PDF">
                                            PDF
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Remover este documento permanentemente?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_doc">
                                            <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                            <button type="submit" class="btn btn-ghost-danger btn-sm">Remover</button>
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
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- FORMULÁRIOS INFERIORES: NOVO GRUPO & NOVO DOCUMENTO -->
<div class="row row-cards">
    <!-- Adicionar Grupo -->
    <div class="col-12 col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Novo Grupo de Documentos</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add_group">
                    <div class="mb-3">
                        <label for="nome_grupo" class="form-label">Nome do Grupo *</label>
                        <input type="text" id="nome_grupo" name="nome_grupo" class="form-control" required placeholder="Ex: 001/2026 - Recursos Humanos">
                    </div>
                    <div class="mb-3">
                        <label for="posicao" class="form-label">Ordem / Posição</label>
                        <input type="number" id="posicao" name="posicao" class="form-control" value="<?= count($grupos) + 1 ?>" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Criar Grupo</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Documento -->
    <div class="col-12 col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Adicionar Documento ao Projeto</h3>
            </div>
            <div class="card-body">
                <?php if (empty($grupos)): ?>
                    <div class="text-muted py-4 text-center">
                        Crie ao menos um grupo de documentos primeiro para habilitar o upload.
                    </div>
                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add_doc">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="grupo_id" class="form-label">Grupo de Destino *</label>
                                <select id="grupo_id" name="grupo_id" class="form-select" required>
                                    <option value="">Selecione o grupo</option>
                                    <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= e($g['nome_grupo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_id" class="form-label">Tipo de Documento *</label>
                                <select id="tipo_id" name="tipo_id" class="form-select" required>
                                    <option value="">Selecione o tipo</option>
                                    <?php foreach ($tipos_doc as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= e($t['tipo_documento']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nome_documento" class="form-label">Nome do Documento *</label>
                            <input type="text" id="nome_documento" name="nome_documento" class="form-control" required placeholder="Ex: Edital de Seleção Pública nº 01/2026">
                        </div>
                        <div class="mb-3">
                            <label for="arquivo_doc" class="form-label">Arquivo PDF *</label>
                            <input type="file" id="arquivo_doc" name="arquivo_doc" class="form-control" accept=".pdf" required>
                            <small class="form-hint">Apenas arquivos no formato .PDF</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Publicar Documento</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
