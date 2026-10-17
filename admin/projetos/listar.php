<?php
// admin/projetos/listar.php — Lista todos os projetos com ações (Tabler UI)
declare(strict_types=1);

require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

auth_require();

// ── Toggle ativo/inativo ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_ativo') {
    csrf_verify();

    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $projeto = db_fetch('SELECT id, ativo FROM tab_projetos WHERE id = ?', [$id]);
        if ($projeto) {
            $novo_valor = $projeto['ativo'] ? 0 : 1;
            db_update('tab_projetos', ['ativo' => $novo_valor], 'id = ?', [$id]);
            $msg = $novo_valor ? 'Projeto ativado com sucesso.' : 'Projeto desativado com sucesso.';
            flash('success', $msg);
        }
    }
    redirect('/admin/projetos/listar.php');
}

// ── Status para Filtro ────────────────────────────────────────────────────────
$status_list = [];
try {
    $status_list = db_fetch_all('SELECT id, projetos_status FROM tab_projetos_status ORDER BY id ASC');
} catch (Throwable $e) {
    $status_list = [];
}

// ── Listagem com filtros ──────────────────────────────────────────────────────
$projetos = db_fetch_all(
    'SELECT p.*, ps.projetos_status AS nome_status
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     ORDER BY p.ativo DESC, p.nome_projeto ASC'
);

$page_title = 'Admin — Projetos';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/admin/dashboard.php'],
    ['label' => 'Projetos', 'url' => '']
];

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Planejamento &amp; Execução</div>
            <h2 class="page-title">Projetos Sociais &amp; Esportivos</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <!-- Alternador de Visualização Cards / Tabela -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary active" id="btn-view-grid" title="Visualização em Cards">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-view-table" title="Visualização em Tabela">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    </button>
                </div>

                <a href="/admin/projetos/criar.php" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Novo Projeto
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Filtros Interativa -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" id="filtro-busca" class="form-control" placeholder="Pesquisar por nome, proposta ou termo...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select id="filtro-status" class="form-select">
                    <option value="">Todos os status</option>
                    <?php foreach ($status_list as $st): ?>
                        <option value="<?= e(strtolower($st['projetos_status'])) ?>"><?= e($st['projetos_status']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="filtro-ativo" class="form-select">
                    <option value="">Todos (Ativos / Inativos)</option>
                    <option value="1">Somente Ativos</option>
                    <option value="0">Somente Inativos</option>
                </select>
            </div>
            <div class="col-12 col-md-2 text-md-end text-secondary small">
                <span id="contador-projetos" class="fw-medium"><?= count($projetos) ?> projeto(s)</span>
            </div>
        </div>
    </div>
</div>

<!-- Visualização em Cards (Grid Tabler) -->
<div class="row row-cards" id="container-grid">
    <?php if (empty($projetos)): ?>
        <div class="col-12">
            <div class="card card-body text-center text-secondary py-5">
                Nenhum projeto cadastrado até o momento.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($projetos as $proj): ?>
            <div class="col-md-6 col-lg-4 item-projeto" 
                 data-nome="<?= e(strtolower($proj['nome_projeto'] . ' ' . ($proj['termo_fomento'] ?? '') . ' ' . ($proj['num_proposta'] ?? ''))) ?>"
                 data-status="<?= e(strtolower($proj['nome_status'] ?? '')) ?>"
                 data-ativo="<?= $proj['ativo'] ? '1' : '0' ?>">
                
                <div class="card h-100 d-flex flex-column justify-content-between">
                    <div class="card-header pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($proj['nome_status'])): ?>
                                <span class="badge bg-blue-lt"><?= e($proj['nome_status']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary-lt">Geral</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-actions">
                            <?php if ($proj['ativo']): ?>
                                <span class="badge bg-green-lt">🟢 Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-lt">⚪ Inativo</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <h3 class="card-title mb-2 text-truncate" title="<?= e($proj['nome_projeto']) ?>" style="font-size: 1.05rem; line-height: 1.35;">
                            <?= e($proj['nome_projeto']) ?>
                        </h3>
                        
                        <div class="text-secondary small mb-3">
                            <?php if (!empty($proj['num_proposta'])): ?>
                                <div><span class="fw-semibold">Proposta:</span> <?= e($proj['num_proposta']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($proj['termo_fomento'])): ?>
                                <div><span class="fw-semibold">Termo:</span> <?= e($proj['termo_fomento']) ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($proj['valor'])): ?>
                            <div class="d-flex align-items-baseline gap-1 mt-auto">
                                <span class="text-secondary small">Orçamento:</span>
                                <span class="h3 mb-0 text-primary fw-bold font-monospace"><?= e($proj['valor']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer bg-transparent py-2">
                        <div class="d-flex align-items-center justify-content-between gap-1 flex-wrap">
                            <div class="btn-group btn-group-sm">
                                <a href="/admin/projetos/editar.php?id=<?= $proj['id'] ?>" class="btn btn-outline-secondary" title="Editar informações">Editar</a>
                                <a href="/admin/projetos/documentos.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-outline-secondary" title="Gerenciar Documentos">Docs</a>
                                <a href="/admin/projetos/funcoes.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-outline-secondary" title="Funções do Projeto">Funções</a>
                            </div>

                            <form method="POST" action="/admin/projetos/listar.php" class="d-inline" onsubmit="return confirm('Confirma a alteração de status deste projeto?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="toggle_ativo">
                                <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $proj['ativo'] ? 'btn-ghost-danger' : 'btn-success' ?>" title="Alternar status ativo/inativo">
                                    <?= $proj['ativo'] ? 'Desativar' : 'Ativar' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Visualização em Tabela (Modo Alternativo) -->
<div class="card" id="container-table" style="display:none;">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped" id="tabela-projetos">
            <thead>
                <tr>
                    <th>Nome do Projeto</th>
                    <th>Nº Proposta</th>
                    <th>Valor Orçado</th>
                    <th>Status</th>
                    <th>Visibilidade</th>
                    <th class="text-end" style="width: 240px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projetos as $proj): ?>
                    <tr class="item-projeto-linha"
                        data-nome="<?= e(strtolower($proj['nome_projeto'] . ' ' . ($proj['termo_fomento'] ?? '') . ' ' . ($proj['num_proposta'] ?? ''))) ?>"
                        data-status="<?= e(strtolower($proj['nome_status'] ?? '')) ?>"
                        data-ativo="<?= $proj['ativo'] ? '1' : '0' ?>">
                        <td>
                            <div class="font-weight-medium"><?= e($proj['nome_projeto']) ?></div>
                            <?php if (!empty($proj['termo_fomento'])): ?>
                                <div class="text-secondary small"><?= e($proj['termo_fomento']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary small font-monospace"><?= e($proj['num_proposta'] ?? '—') ?></td>
                        <td><strong class="text-primary font-monospace"><?= !empty($proj['valor']) ? e($proj['valor']) : '—' ?></strong></td>
                        <td>
                            <?php if (!empty($proj['nome_status'])): ?>
                                <span class="badge bg-blue-lt"><?= e($proj['nome_status']) ?></span>
                            <?php else: ?>
                                <span class="text-secondary">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($proj['ativo']): ?>
                                <span class="badge bg-green-lt">🟢 Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-lt">⚪ Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-list flex-nowrap justify-content-end">
                                <a href="/admin/projetos/editar.php?id=<?= $proj['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <a href="/admin/projetos/documentos.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-outline-secondary">Docs</a>
                                <a href="/admin/projetos/funcoes.php?projeto_id=<?= $proj['id'] ?>" class="btn btn-sm btn-outline-secondary">Funções</a>
                                <form method="POST" action="/admin/projetos/listar.php" class="d-inline" onsubmit="return confirm('Confirma a alteração?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_ativo">
                                    <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $proj['ativo'] ? 'btn-ghost-danger' : 'btn-success' ?>">
                                        <?= $proj['ativo'] ? 'Desativar' : 'Ativar' ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputBusca   = document.getElementById('filtro-busca');
    const selectStatus = document.getElementById('filtro-status');
    const selectAtivo  = document.getElementById('filtro-ativo');
    const contador     = document.getElementById('contador-projetos');

    const btnGrid  = document.getElementById('btn-view-grid');
    const btnTable = document.getElementById('btn-view-table');
    const gridView = document.getElementById('container-grid');
    const tableView = document.getElementById('container-table');

    // Alternar modos de visualização
    btnGrid?.addEventListener('click', () => {
        btnGrid.classList.add('active');
        btnTable.classList.remove('active');
        gridView.style.display = 'flex';
        tableView.style.display = 'none';
    });

    btnTable?.addEventListener('click', () => {
        btnTable.classList.add('active');
        btnGrid.classList.remove('active');
        gridView.style.display = 'none';
        tableView.style.display = 'block';
    });

    // Função de Filtragem Interativa
    function aplicarFiltros() {
        const busca  = inputBusca.value.toLowerCase().trim();
        const status = selectStatus.value.toLowerCase().trim();
        const ativo  = selectAtivo.value.trim();

        const cards  = document.querySelectorAll('.item-projeto');
        const linhas = document.querySelectorAll('.item-projeto-linha');

        let visiveis = 0;

        cards.forEach((card, idx) => {
            const cardNome   = card.dataset.nome || '';
            const cardStatus = card.dataset.status || '';
            const cardAtivo  = card.dataset.ativo || '';

            const matchBusca  = !busca  || cardNome.includes(busca);
            const matchStatus = !status || cardStatus.includes(status);
            const matchAtivo  = !ativo  || cardAtivo === ativo;

            const visivel = matchBusca && matchStatus && matchAtivo;

            card.style.display = visivel ? 'block' : 'none';
            if (linhas[idx]) linhas[idx].style.display = visivel ? 'table-row' : 'none';

            if (visivel) visiveis++;
        });

        if (contador) contador.innerText = `${visiveis} projeto(s)`;
    }

    inputBusca?.addEventListener('input', aplicarFiltros);
    selectStatus?.addEventListener('change', aplicarFiltros);
    selectAtivo?.addEventListener('change', aplicarFiltros);
});
</script>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
