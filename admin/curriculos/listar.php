<?php
/**
 * Admin: Listar Currículos (Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Currículos';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Currículos'],
];

// Filtros
$filtro_funcao   = (int)($_GET['funcao_id'] ?? 0);
$filtro_projeto  = (int)($_GET['projeto_id'] ?? 0);

$where  = '1=1';
$params = [];
if ($filtro_funcao)  { $where .= ' AND c.id_funcao = ?';  $params[] = $filtro_funcao; }
if ($filtro_projeto) { $where .= ' AND c.id_projeto = ?'; $params[] = $filtro_projeto; }

$por_pagina = 25;
$total = db_count("SELECT COUNT(*) FROM tab_curriculos c WHERE $where", $params);
$pag   = paginate($total, $por_pagina);

$curriculos = db_fetch_all(
    "SELECT c.*, f.funcao, p.nome_projeto
     FROM tab_curriculos c
     LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
     LEFT JOIN tab_projetos p ON c.id_projeto = p.id
     WHERE $where
     ORDER BY c.created_at DESC
     LIMIT ? OFFSET ?",
    [...$params, $por_pagina, $pag['offset']]
);

$funcoes  = db_fetch_all('SELECT * FROM tab_curriculos_funcao ORDER BY funcao');
$projetos = db_fetch_all('SELECT id, nome_projeto FROM tab_projetos ORDER BY nome_projeto');

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Gestão de Pessoas &amp; Oportunidades</div>
            <h2 class="page-title">Banco de Talentos &amp; Inscrições</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <span class="badge bg-primary-lt fs-6 px-3 py-2">
                <?= $total ?> currículo(s) registrado(s)
            </span>
        </div>
    </div>
</div>

<!-- FILTROS ERGONÔMICOS -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" id="form-filtro">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" id="busca-tabela" class="form-control" placeholder="Filtrar por nome nesta página...">
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <select name="funcao_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas as funções</option>
                        <?php foreach ($funcoes as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= $filtro_funcao === (int)$f['id'] ? 'selected' : '' ?>><?= e($f['funcao']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <select name="projeto_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos os projetos</option>
                        <?php foreach ($projetos as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filtro_projeto === (int)$p['id'] ? 'selected' : '' ?>><?= e(truncate($p['nome_projeto'], 45)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-2 text-md-end">
                    <?php if ($filtro_funcao || $filtro_projeto): ?>
                        <a href="<?= base_url('/admin/curriculos/listar.php') ?>" class="btn btn-outline-secondary btn-sm">Limpar Filtros</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped" id="tabela-curriculos">
            <thead>
                <tr>
                    <th>Candidato</th>
                    <th>Telefone</th>
                    <th>Função</th>
                    <th>Projeto de Interesse</th>
                    <th style="width:110px">Inscrição</th>
                    <th style="width:160px; text-align:right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($curriculos)): ?>
                <tr><td colspan="6" class="text-center text-secondary py-5">Nenhum currículo encontrado para os filtros selecionados.</td></tr>
                <?php else: ?>
                <?php foreach ($curriculos as $c): ?>
                <?php 
                    $iniciais = strtoupper(substr($c['nome'] ?? 'C', 0, 1));
                    $arq = !empty($c['arquivo_curriculo']) ? $c['arquivo_curriculo'] : ($c['id'] . '.pdf');
                ?>
                <tr class="linha-candidato">
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2 bg-blue-lt fw-bold"><?= $iniciais ?></span>
                            <div>
                                <div class="font-weight-medium">
                                    <a href="<?= base_url('/admin/curriculos/visualizar.php?id=' . (int)$c['id']) ?>" class="text-reset text-decoration-none">
                                        <?= e($c['nome']) ?>
                                    </a>
                                </div>
                                <?php if (!empty($c['e_mail'])): ?>
                                    <div class="text-secondary small"><?= e($c['e_mail']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-secondary small"><?= e($c['telefone_1'] ?: '—') ?></td>
                    <td><span class="badge bg-blue-lt"><?= e($c['funcao'] ?? 'Geral') ?></span></td>
                    <td class="text-secondary small text-truncate" style="max-width: 220px;"><?= e($c['nome_projeto'] ?? 'Geral') ?></td>
                    <td><span class="text-secondary small font-monospace"><?= $c['created_at'] ? format_date(substr($c['created_at'], 0, 10)) : '—' ?></span></td>
                    <td class="text-end">
                        <div class="btn-list flex-nowrap justify-content-end">
                            <a href="<?= base_url('/admin/curriculos/visualizar.php?id=' . (int)$c['id']) ?>" class="btn btn-outline-secondary btn-sm" title="Ver detalhes completos">
                                Detalhes
                            </a>
                            <?php if (!empty($arq)): ?>
                            <a href="/uploads/curriculos/<?= e($arq) ?>" target="_blank" class="btn btn-outline-danger btn-sm" title="Abrir PDF">
                                PDF
                            </a>
                            <?php endif; ?>
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
            <?= pagination_html($pag, base_url('/admin/curriculos/listar.php')) ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('busca-tabela')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#tabela-curriculos tbody tr.linha-candidato');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
