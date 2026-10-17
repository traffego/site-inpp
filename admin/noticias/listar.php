<?php
/**
 * Admin: Listar Notícias
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Notícias';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Notícias'],
];

$por_pagina = 20;
$total     = db_count('SELECT COUNT(*) FROM tab_noticias');
$pag       = paginate($total, $por_pagina);
$noticias  = db_fetch_all('SELECT * FROM tab_noticias ORDER BY data_noticia DESC LIMIT ? OFFSET ?', [$por_pagina, $pag['offset']]);

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Comunicação Institucional</div>
            <h2 class="page-title">Notícias &amp; Artigos</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/noticias/criar.php') ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Nova Notícia
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title mb-0">
            Publicações
            <span class="badge bg-primary-lt ms-2 font-monospace"><?= $total ?></span>
        </h3>
        <div class="card-actions">
            <div class="input-icon" style="min-width: 260px;">
                <span class="input-icon-addon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="busca-tabela" class="form-control form-control-sm" placeholder="Filtrar manchetes...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped" id="tabela-noticias">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Manchete &amp; Resumo</th>
                    <th style="width: 140px;">Data</th>
                    <th style="width: 130px; text-align: center;">Visualizações</th>
                    <th style="width: 150px; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($noticias)): ?>
                <tr>
                    <td colspan="5" class="text-center text-secondary py-5">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-secondary mb-2 opacity-50"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <div>Nenhuma notícia cadastrada ainda.</div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($noticias as $n): ?>
                <tr>
                    <td><span class="text-secondary font-monospace small">#<?= (int)$n['id'] ?></span></td>
                    <td>
                        <div class="fw-medium text-reset mb-1"><?= e($n['manchete']) ?></div>
                        <?php if (!empty($n['subtitulo'])): ?>
                            <div class="text-secondary small text-truncate" style="max-width: 480px;"><?= e($n['subtitulo']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="text-secondary small"><?= $n['data_noticia'] ? format_date($n['data_noticia']) : '—' ?></span></td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt font-monospace"><?= number_format((int)($n['n_visitas'] ?? 0), 0, ',', '.') ?> acessos</span>
                    </td>
                    <td class="text-end">
                        <div class="btn-list justify-content-end">
                            <a href="<?= base_url('/admin/noticias/editar.php?id=' . $n['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>
                            <form method="POST" action="<?= base_url('/admin/noticias/excluir.php') ?>" class="d-inline" onsubmit="return confirm('Confirma a exclusão definitiva desta notícia?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-ghost-danger">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pag['total_pages'] > 1): ?>
        <div class="card-footer d-flex align-items-center justify-content-between">
            <p class="m-0 text-secondary small">Mostrando página <strong><?= $pag['current_page'] ?></strong> de <strong><?= $pag['total_pages'] ?></strong></p>
            <?= pagination_html($pag, base_url('/admin/noticias/listar.php')) ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('busca-tabela')?.addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabela-noticias tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
