<?php
/**
 * Admin: Editar Notícia (Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$id      = (int)($_GET['id'] ?? 0);
$noticia = $id ? db_fetch('SELECT * FROM tab_noticias WHERE id = ?', [$id]) : null;
if (!$noticia) { flash('error', 'Notícia não encontrada.'); redirect(base_url('/admin/noticias/listar.php')); }

$page_title = 'Admin — Editar Notícia';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Notícias',  'url' => base_url('/admin/noticias/listar.php')],
    ['label' => 'Editar'],
];

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'update';

    if ($action === 'delete_img_inicio') {
        if ($noticia['imagem_inicio']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio']);
        db_update('tab_noticias', ['imagem_inicio' => ''], 'id = ?', [$id]);
        flash('success', 'Imagem superior removida.');
        redirect(base_url('/admin/noticias/editar.php?id=' . $id));
    }
    if ($action === 'delete_img_final') {
        if ($noticia['imagem_final']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final']);
        db_update('tab_noticias', ['imagem_final' => ''], 'id = ?', [$id]);
        flash('success', 'Imagem inferior removida.');
        redirect(base_url('/admin/noticias/editar.php?id=' . $id));
    }

    // Update normal
    $dados = [
        'manchete'     => trim($_POST['manchete'] ?? ''),
        'resumo'       => trim($_POST['resumo'] ?? ''),
        'noticia'      => $_POST['noticia'] ?? '',
        'data_noticia' => $_POST['data_noticia'] ?: date('Y-m-d'),
    ];

    if (empty($dados['manchete'])) $erros[] = 'A manchete é obrigatória.';

    if (empty($erros)) {
        if (!empty($_FILES['imagem_inicio']['name'])) {
            if ($noticia['imagem_inicio']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio']);
            $up = upload_image($_FILES['imagem_inicio'], UPLOAD_PATH . '/noticias');
            if ($up['success']) $dados['imagem_inicio'] = $up['filename'];
        }
        if (!empty($_FILES['imagem_final']['name'])) {
            if ($noticia['imagem_final']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final']);
            $up = upload_image($_FILES['imagem_final'], UPLOAD_PATH . '/noticias');
            if ($up['success']) $dados['imagem_final'] = $up['filename'];
        }
        db_update('tab_noticias', $dados, 'id = ?', [$id]);
        flash('success', 'Notícia atualizada com sucesso!');
        redirect(base_url('/admin/noticias/listar.php'));
    }
    $noticia = array_merge($noticia, $dados);
}

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Comunicação Institucional</div>
            <h2 class="page-title text-truncate">Editar: <?= e($noticia['manchete']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/noticias/listar.php') ?>" class="btn btn-outline-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Voltar às Notícias
            </a>
        </div>
    </div>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <h4 class="alert-title mb-1">Por favor, corrija os erros abaixo:</h4>
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

<form method="POST" enctype="multipart/form-data" id="form-noticia">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update">
    
    <div class="row row-cards">
        <!-- COLUNA PRINCIPAL: TEXTOS E CONTEÚDO (8 Colunas) -->
        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Conteúdo da Notícia</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="manchete" class="form-label required">Título / Manchete</label>
                        <input type="text" id="manchete" name="manchete" class="form-control form-control-lg" required value="<?= e($noticia['manchete']) ?>" placeholder="Título principal da publicação...">
                    </div>

                    <div class="mb-3">
                        <label for="resumo" class="form-label">Linha Fina / Resumo</label>
                        <textarea id="resumo" name="resumo" class="form-control" rows="2" placeholder="Breve introdução exibida nos cards de notícias..."><?= e($noticia['resumo'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="noticia" class="form-label">Texto Completo da Matéria</label>
                        <textarea id="noticia" name="noticia" class="form-control tinymce-editor" rows="12"><?= $noticia['noticia'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUNA LATERAL: METADADOS E IMAGENS (4 Colunas) -->
        <div class="col-12 col-lg-4">
            <!-- Data de Publicação -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Publicação</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="data_noticia" class="form-label required">Data de Publicação</label>
                        <input type="date" id="data_noticia" name="data_noticia" class="form-control" required value="<?= e($noticia['data_noticia']) ?>">
                    </div>
                </div>
            </div>

            <!-- Imagens em Destaque -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Imagens da Matéria</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imagem_inicio" class="form-label">Imagem Principal (Capa)</label>
                        <?php if (!empty($noticia['imagem_inicio'])): ?>
                            <div class="d-flex align-items-center gap-2 mb-2 p-2 border rounded">
                                <img src="/uploads/noticias/<?= e($noticia['imagem_inicio']) ?>" alt="Capa" style="height:48px; border-radius:4px;">
                                <div class="text-truncate small flex-grow-1"><?= e($noticia['imagem_inicio']) ?></div>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="imagem_inicio" name="imagem_inicio" class="form-control" accept="image/*">
                        <small class="form-hint">Formatos: JPG, PNG, WebP</small>
                    </div>

                    <div class="mb-3">
                        <label for="imagem_final" class="form-label">Imagem Secundária (Rodapé)</label>
                        <?php if (!empty($noticia['imagem_final'])): ?>
                            <div class="d-flex align-items-center gap-2 mb-2 p-2 border rounded">
                                <img src="/uploads/noticias/<?= e($noticia['imagem_final']) ?>" alt="Rodapé" style="height:48px; border-radius:4px;">
                                <div class="text-truncate small flex-grow-1"><?= e($noticia['imagem_final']) ?></div>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="imagem_final" name="imagem_final" class="form-control" accept="image/*">
                        <small class="form-hint">Opcional: exibida no final do texto</small>
                    </div>
                </div>
            </div>

            <!-- Card de Ações -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Salvar Alterações
                    </button>
                    <a href="<?= base_url('/admin/noticias/listar.php') ?>" class="btn btn-outline-secondary w-100">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
