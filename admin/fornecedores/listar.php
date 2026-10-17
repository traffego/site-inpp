<?php
/**
 * Admin: Listar Fornecedores (Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Fornecedores';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Fornecedores'],
];

$por_pagina   = 20;
$busca        = trim($_GET['busca'] ?? '');
$where        = '1=1';
$params       = [];
if ($busca) {
    $where    = '(nome_fantasia LIKE ? OR razao_social LIKE ? OR cnpj LIKE ?)';
    $like     = '%' . $busca . '%';
    $params   = [$like, $like, $like];
}
$total        = db_count("SELECT COUNT(*) FROM tab_fornecedores WHERE $where", $params);
$pag          = paginate($total, $por_pagina);
$fornecedores = db_fetch_all(
    "SELECT * FROM tab_fornecedores WHERE $where ORDER BY razao_social LIMIT ? OFFSET ?",
    [...$params, $por_pagina, $pag['offset']]
);

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Compras &amp; Contratações</div>
            <h2 class="page-title">Cadastro Geral de Fornecedores</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <span class="badge bg-primary-lt fs-6 px-3 py-2">
                <?= $total ?> fornecedor(es) parceiro(s)
            </span>
        </div>
    </div>
</div>

<!-- BUSCA E FILTROS -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" name="busca" class="form-control" value="<?= e($busca) ?>" placeholder="Buscar por Razão Social, Nome Fantasia ou CNPJ...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
            <?php if ($busca): ?>
            <div class="col-auto">
                <a href="<?= base_url('/admin/fornecedores/listar.php') ?>" class="btn btn-outline-secondary">Limpar Busca</a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Razão Social / Nome Fantasia</th>
                    <th style="width:160px">CNPJ</th>
                    <th>Pessoa de Contato</th>
                    <th>Telefone</th>
                    <th style="width:60px">UF</th>
                    <th style="width:110px">Cadastro</th>
                    <th style="width:50px; text-align:center;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fornecedores)): ?>
                <tr><td colspan="7" class="text-center text-secondary py-5">Nenhum fornecedor cadastrado.</td></tr>
                <?php else: ?>
                <?php foreach ($fornecedores as $f): ?>
                <tr class="row-expandable" data-expand-id="expand-<?= $f['id'] ?>" style="cursor: pointer;" title="Clique para expandir dados de contato e endereço">
                    <td>
                        <div class="font-weight-medium"><?= e($f['razao_social'] ?: ($f['nome_fantasia'] ?: 'Empresa sem nome')) ?></div>
                        <?php if ($f['nome_fantasia'] && $f['razao_social']): ?>
                            <div class="text-secondary small"><?= e($f['nome_fantasia']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-secondary small font-monospace"><?= e($f['cnpj'] ?: '—') ?></td>
                    <td>
                        <div><?= e($f['contato_nome'] ?: '—') ?></div>
                        <?php if ($f['contato_cargo']): ?><div class="text-secondary small"><?= e($f['contato_cargo']) ?></div><?php endif; ?>
                    </td>
                    <td class="text-secondary small"><?= e($f['contato_telefone'] ?: '—') ?></td>
                    <td>
                        <?php if (!empty($f['estado'])): ?>
                            <span class="badge bg-secondary-lt"><?= e($f['estado']) ?></span>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><span class="text-secondary small font-monospace"><?= $f['created_at'] ? format_date(substr($f['created_at'], 0, 10)) : '—' ?></span></td>
                    <td class="text-center text-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="chevron-icon"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </td>
                </tr>
                <!-- Linha de detalhes expansível -->
                <tr class="row-details" id="expand-<?= $f['id'] ?>" style="display:none; background-color: var(--tblr-bg-surface-secondary);">
                    <td colspan="7" class="p-3">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="text-secondary small mb-1 fw-bold">LOCALIZAÇÃO &amp; SEDE:</div>
                                <div class="small">
                                    📍 <?= e($f['endereco'] ?: 'Endereço não informado') ?><?= !empty($f['bairro']) ? ', ' . e($f['bairro']) : '' ?><?= !empty($f['cidade']) ? ' — ' . e($f['cidade']) : '' ?><?= !empty($f['estado']) ? '/' . e($f['estado']) : '' ?><?= !empty($f['cep']) ? ' &bull; CEP ' . e($f['cep']) : '' ?>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="text-secondary small mb-1 fw-bold">CANAL DE CONTATO:</div>
                                <div class="small">
                                    ✉️ <?= !empty($f['contato_email']) ? '<a href="mailto:' . e($f['contato_email']) . '" class="text-reset text-decoration-none fw-medium">' . e($f['contato_email']) . '</a>' : 'E-mail não cadastrado' ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pag['total_pages'] > 1): ?>
        <div class="card-footer d-flex align-items-center py-2">
            <?= pagination_html($pag, base_url('/admin/fornecedores/listar.php') . '?busca=' . urlencode($busca)) ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.row-expandable').forEach(row => {
    row.addEventListener('click', () => {
        const targetId = row.dataset.expandId;
        const details = document.getElementById(targetId);
        const chevron = row.querySelector('.chevron-icon');
        if (details) {
            const isHidden = details.style.display === 'none';
            details.style.display = isHidden ? 'table-row' : 'none';
            if (chevron) {
                chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                chevron.style.transition = 'transform 0.2s ease';
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
