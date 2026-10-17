<?php
/**
 * Admin: Painel de Transferências & Documentos de Transparência (Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$page_title = 'Admin — Repositório de Transparência & Painel';
$breadcrumb = [
    ['label' => 'Dashboard',     'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Transparência', 'url' => base_url('/admin/transparencia/painel.php')],
    ['label' => 'Painel de Arquivos'],
];

$pasta = UPLOAD_PATH . '/transparencia/';
if (!is_dir($pasta)) mkdir($pasta, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_habilitado') {
        $did = (int)($_POST['doc_id'] ?? 0);
        if ($did) {
            $doc = db_fetch('SELECT habilitado FROM tab_transparencia_arquivos WHERE id = ?', [$did]);
            if ($doc) {
                $novo_status = $doc['habilitado'] ? 0 : 1;
                db_update('tab_transparencia_arquivos', ['habilitado' => $novo_status], 'id = ?', [$did]);
                flash('success', 'Status do arquivo atualizado: ' . ($novo_status ? '🟢 Habilitado (Público)' : '⚪ Desabilitado (Oculto)'));
            }
        }
    } elseif ($action === 'upload') {
        if (empty($_FILES['arquivo']['name'])) { 
            flash('error', 'Selecione um arquivo.'); 
            redirect(base_url('/admin/transparencia/painel.php')); 
        }
        
        $tipo = $_POST['tipo'] ?? 'painel';
        $permitidos_tipos = ['painel', 'estatuto', 'dirigentes', 'regulamento'];
        if (!in_array($tipo, $permitidos_tipos)) $tipo = 'painel';

        $titulo = trim($_POST['titulo'] ?? '');
        $data_doc = !empty($_POST['data_documento']) ? $_POST['data_documento'] : date('Y-m-d');
        $prefixo = date('Y_m_d', strtotime($data_doc)) . '_';

        $up = upload_file($_FILES['arquivo'], $pasta, ['jpg', 'jpeg', 'png', 'xlsx', 'xls', 'pdf'], $prefixo);
        if (!$up['success']) { 
            flash('error', 'Erro no upload: ' . $up['error']); 
        } else {
            if (!$titulo) {
                $titulo = pathinfo($_FILES['arquivo']['name'], PATHINFO_FILENAME);
            }
            db_insert('tab_transparencia_arquivos', [
                'tipo'           => $tipo,
                'titulo'         => $titulo,
                'arquivo'        => $up['filename'],
                'data_documento' => $data_doc,
                'habilitado'     => 1,
            ]);
            flash('success', 'Arquivo publicado com sucesso!');
        }
    } elseif ($action === 'delete') {
        $did = (int)($_POST['doc_id'] ?? 0);
        if ($did) {
            $doc = db_fetch('SELECT arquivo FROM tab_transparencia_arquivos WHERE id = ?', [$did]);
            if ($doc) {
                @unlink($pasta . $doc['arquivo']);
                db_delete('tab_transparencia_arquivos', 'id = ?', [$did]);
                flash('success', 'Arquivo removido.');
            }
        }
    }
    redirect(base_url('/admin/transparencia/painel.php'));
}

// Buscar todos os arquivos do banco de dados
$arquivos_db = db_fetch_all('SELECT * FROM tab_transparencia_arquivos ORDER BY id DESC');

$total_cadastrados = count($arquivos_db);
$total_ativos      = count(array_filter($arquivos_db, fn($a) => !empty($a['habilitado'])));
$total_ocultos     = $total_cadastrados - $total_ativos;

$tipo_labels = [
    'painel'      => ['label' => 'Painel / Repasses', 'badge' => 'bg-blue-lt'],
    'estatuto'    => ['label' => 'Estatuto Social',   'badge' => 'bg-green-lt'],
    'dirigentes'  => ['label' => 'Dirigentes',         'badge' => 'bg-cyan-lt'],
    'regulamento' => ['label' => 'Regulamento',       'badge' => 'bg-yellow-lt'],
];

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Transparência Pública</div>
            <h2 class="page-title">Repositório de Arquivos &amp; Anexos</h2>
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
                <a href="/admin/transparencia/painel.php" class="nav-link active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    Painel Geral (<?= $total_cadastrados ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/transparencia/financeiro.php" class="nav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Documentos Financeiros
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
    <!-- Lista de arquivos com busca integrada (Esquerda: 8 Colunas) -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Arquivos Catalogados</h3>
                    <p class="card-subtitle mb-0">Controle individual de publicação no portal</p>
                </div>
                <div class="card-actions">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="filtro-painel" class="form-control form-control-sm" placeholder="Filtrar arquivos...">
                    </div>
                </div>
            </div>

            <?php if (empty($arquivos_db)): ?>
                <div class="card-body text-center text-secondary py-5">
                    Nenhum arquivo cadastrado no momento.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped" id="tabela-painel-arquivos">
                        <thead>
                            <tr>
                                <th>Arquivo / Título</th>
                                <th style="width:140px">Módulo</th>
                                <th style="width:110px; text-align:center;">Visibilidade</th>
                                <th style="width:85px">Tamanho</th>
                                <th style="width:110px; text-align:right;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arquivos_db as $a): ?>
                            <?php 
                                $caminho_arq = $pasta . $a['arquivo'];
                                $ext = strtolower(pathinfo($a['arquivo'], PATHINFO_EXTENSION));
                                $tam = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024) . ' KB' : '—';
                                $url = '/uploads/transparencia/' . $a['arquivo'];
                                $tipo_info = $tipo_labels[$a['tipo']] ?? ['label' => ucfirst($a['tipo']), 'badge' => 'bg-secondary-lt'];
                            ?>
                            <tr class="linha-arquivo">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                                            <span class="avatar avatar-sm me-2 rounded" style="background-image: url('<?= e($url) ?>');"></span>
                                        <?php else: ?>
                                            <span class="avatar avatar-sm me-2 bg-secondary-lt fw-bold font-monospace" style="font-size:0.65rem;"><?= strtoupper($ext) ?></span>
                                        <?php endif; ?>
                                        <div class="text-truncate" style="max-width: 280px;">
                                            <div class="font-weight-medium text-truncate"><?= e($a['titulo'] ?: $a['arquivo']) ?></div>
                                            <div class="text-secondary small text-truncate"><?= e($a['arquivo']) ?> &bull; <?= format_date($a['data_documento']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge <?= $tipo_info['badge'] ?>"><?= e($tipo_info['label']) ?></span></td>
                                <td class="text-center">
                                    <form method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="toggle_habilitado">
                                        <input type="hidden" name="doc_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= !empty($a['habilitado']) ? 'btn-success' : 'btn-outline-secondary' ?>" title="Clique para alternar visibilidade pública">
                                            <?= !empty($a['habilitado']) ? '🟢 Ativo' : '⚪ Oculto' ?>
                                        </button>
                                    </form>
                                </td>
                                <td><span class="text-secondary small"><?= $tam ?></span></td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <a href="<?= e($url) ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="Abrir">Ver</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Confirma a remoção permanente deste arquivo?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="doc_id" value="<?= $a['id'] ?>">
                                            <button type="submit" class="btn btn-ghost-danger btn-sm" title="Excluir">Excluir</button>
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

    <!-- Upload Card (Direita: 4 Colunas) -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Publicar Novo Arquivo</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload">
                    
                    <div class="mb-3">
                        <label for="tipo_arquivo" class="form-label">Módulo de Transparência *</label>
                        <select id="tipo_arquivo" name="tipo" class="form-select" required>
                            <option value="painel">Painel de Transferências / Repasses</option>
                            <option value="estatuto">Estatuto Social</option>
                            <option value="dirigentes">Relação de Dirigentes</option>
                            <option value="regulamento">Regulamento de Compras</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="titulo_arquivo" class="form-label">Título do Documento</label>
                        <input type="text" id="titulo_arquivo" name="titulo" class="form-control" placeholder="Ex: Planilha de Repasses 2026">
                    </div>

                    <div class="mb-3">
                        <label for="data_documento" class="form-label">Data de Competência *</label>
                        <input type="date" id="data_documento" name="data_documento" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="arquivo_painel" class="form-label">Arquivo *</label>
                        <input type="file" id="arquivo_painel" name="arquivo" class="form-control" required accept=".jpg,.jpeg,.png,.xlsx,.xls,.pdf">
                        <small class="form-hint">Formatos: PDF, XLSX, XLS, JPG, PNG</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        Enviar e Publicar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('filtro-painel')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#tabela-painel-arquivos tbody tr.linha-arquivo');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
