<?php
/**
 * Projetos & Termos de Fomento — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Projetos e Termos de Fomento';
$page_description = 'Relação pública de projetos, parcerias e termos de fomento executados pelo Instituto Nacional de Políticas Públicas (INPP).';

// Mapeamento institucional de status
$status_map = [
    1 => 'Concluído',
    2 => 'Em Execução',
    3 => 'Em Prestação de Contas',
    4 => 'Assinado',
    5 => 'Proposta Aprovada',
];

$status_badge_class = [
    'Concluído'              => 'proj-badge-concluido',
    'Em Execução'            => 'proj-badge-execucao',
    'Em Prestação de Contas' => 'proj-badge-prestacao',
    'Assinado'               => 'proj-badge-assinado',
    'Proposta Aprovada'      => 'proj-badge-aprovado',
];

// Buscar todos os projetos ativos que possuem documentos habilitados
$projetos = [];
try {
    $projetos = db_fetch_all(
        "SELECT p.*, COALESCE(ps.projetos_status, '') as status_db
         FROM tab_projetos p
         LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
         WHERE p.ativo = 1 
           AND p.id IN (SELECT DISTINCT id_projeto FROM tab_projetos_documentos WHERE habilitado = 1)
         ORDER BY p.id DESC"
    );
} catch (Throwable $e) {
    $projetos = [];
}

// Contagens para os filtros
$count_todos = count($projetos);
$count_execucao = 0;
$count_concluido = 0;
$count_prestacao = 0;

foreach ($projetos as $pr) {
    $st_id = (int)($pr['projeto_status'] ?? 2);
    if ($st_id === 2) $count_execucao++;
    elseif ($st_id === 1) $count_concluido++;
    elseif ($st_id === 3) $count_prestacao++;
}

ob_start();
?>
<!-- HERO INSTITUCIONAL DO PORTFÓLIO -->
<section class="qs-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Projetos & Parcerias</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Transparência Ativa &bull; MROSC Lei 13.019/14</span>
            <h1 class="qs-hero-title">Projetos e Termos de Fomento</h1>
            <p class="qs-hero-lead">
                Iniciativas e parcerias públicas desenvolvidas pelo Instituto Nacional de Políticas Públicas, estruturadas com método técnico, metas mensuráveis e prestação de contas integral à sociedade.
            </p>
        </div>

        <!-- RÉGUA DE INDICADORES DO PORTFÓLIO -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Total de Iniciativas</span>
                <strong class="qs-fact-val"><?= $count_todos ?> Parcerias</strong>
                <span class="qs-fact-desc">Registros históricos e ativos</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Regime Jurídico</span>
                <strong class="qs-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="qs-fact-desc">Termos de fomento e colaboração</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Controle Social</span>
                <strong class="qs-fact-val">Transparência Ativa</strong>
                <span class="qs-fact-desc">Editais e documentos abertos</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Prestação de Contas</span>
                <strong class="qs-fact-val">100% Auditável</strong>
                <span class="qs-fact-desc">Conformidade com órgãos públicos</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO PRINCIPAL DE PROJETOS -->
<section class="section proj-main-section">
    <div class="container">

        <!-- BARRA DE FILTRO E BUSCA -->
        <div class="proj-filter-bar">
            <div class="proj-filter-tabs" role="tablist">
                <button type="button" class="proj-tab active" data-filter="all">
                    Todos <span><?= $count_todos ?></span>
                </button>
                <button type="button" class="proj-tab" data-filter="execucao">
                    Em Execução <span><?= $count_execucao ?></span>
                </button>
                <button type="button" class="proj-tab" data-filter="concluido">
                    Concluídos <span><?= $count_concluido ?></span>
                </button>
                <button type="button" class="proj-tab" data-filter="prestacao">
                    Prestação de Contas <span><?= $count_prestacao ?></span>
                </button>
            </div>

            <div class="proj-search-wrap">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" id="proj-search-input" placeholder="Buscar por nome, proposta ou objeto..." aria-label="Buscar projetos">
            </div>
        </div>

        <?php if (empty($projetos)): ?>
        <div class="proj-empty-box">
            <i class="ph ph-folder-dashed"></i>
            <h3>Nenhum projeto registrado</h3>
            <p>Não há registros de projetos cadastrados no momento.</p>
            <a href="/editais" class="btn btn-primary">Acessar Editais Públicos</a>
        </div>
        <?php else: ?>

        <!-- GRID DE CARDS DE PROJETOS -->
        <div class="proj-grid" id="proj-items-grid">
            <?php foreach ($projetos as $p): ?>
            <?php
                $st_id = (int)($p['projeto_status'] ?? 2);
                $st_name = $status_map[$st_id] ?? 'Em Execução';
                $badge_cls = $status_badge_class[$st_name] ?? 'proj-badge-execucao';
                
                $filter_cat = 'outro';
                if ($st_id === 2) $filter_cat = 'execucao';
                elseif ($st_id === 1) $filter_cat = 'concluido';
                elseif ($st_id === 3) $filter_cat = 'prestacao';

                $proposta_num = $p['num_proposta'] ?? '';
                $termo_num = $p['termo_fomento'] ?? '';
                $objeto_txt = $p['objeto'] ?? $p['apresentacao'] ?? '';
                
                // Contar documentos anexados ativos
                $total_docs = 0;
                try {
                    $total_docs = db_count('SELECT COUNT(*) FROM tab_projetos_documentos WHERE id_projeto = ? AND habilitado = 1', [$p['id']]);
                } catch (Throwable $e) {}
            ?>
            <article class="proj-card" data-category="<?= $filter_cat ?>" data-search="<?= strtolower(e($p['nome_projeto'] . ' ' . $proposta_num . ' ' . $termo_num . ' ' . strip_tags($objeto_txt))) ?>">
                <div class="proj-card-top">
                    <span class="proj-status-badge <?= $badge_cls ?>"><?= e($st_name) ?></span>
                    <?php if (!empty($proposta_num) || !empty($termo_num)): ?>
                    <span class="proj-ref-num">
                        <?= !empty($termo_num) ? 'Termo nº ' . e($termo_num) : 'Proposta nº ' . e($proposta_num) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <h3 class="proj-card-title"><?= e($p['nome_projeto']) ?></h3>

                <!-- Tabela de Metadados -->
                <div class="proj-meta-table">
                    <?php if (!empty($p['valor'])): ?>
                    <div class="proj-meta-row">
                        <span class="proj-meta-label">Valor Global:</span>
                        <strong class="proj-meta-val"><?= e($p['valor']) ?></strong>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($p['inicio_vigencia']) && !empty($p['termino_vigencia'])): ?>
                    <div class="proj-meta-row">
                        <span class="proj-meta-label">Período de Vigência:</span>
                        <span class="proj-meta-val"><?= e(format_date($p['inicio_vigencia'])) ?> até <?= e(format_date($p['termino_vigencia'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($objeto_txt)): ?>
                <p class="proj-card-desc">
                    <?= e(truncate(strip_tags($objeto_txt), 180)) ?>
                </p>
                <?php endif; ?>

                <?php if ($total_docs > 0): ?>
                <div class="proj-card-footer">
                    <span class="proj-docs-count">
                        <i class="ph ph-file-text"></i> <?= $total_docs ?> <?= $total_docs === 1 ? 'documento oficial' : 'documentos oficiais' ?>
                    </span>
                    <a href="/editais?projeto=<?= (int)$p['id'] ?>" class="btn btn-outline btn-sm">
                        Ver Documentos & Editais <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <!-- PAINEL DE TRANSPARÊNCIA E AUDITORIA -->
        <div class="proj-transparency-box">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Conformidade e Transparência</span>
                <h3>Documentação e Fiscalização Pública</h3>
                <p>
                    Em consonância com as diretrizes do Marco Regulatório das Organizações da Sociedade Civil (Lei Federal nº 13.019/2014) e com a Lei de Acesso à Informação (Lei nº 12.527/2011), todos os planos de trabalho, relatórios de execução, cotações e prestações de contas do INPP estão disponíveis para consulta livre.
                </p>
            </div>
            <div class="proj-transparency-links">
                <a href="/transparencia/termos" class="btn btn-outline">
                    Termos de Fomento
                </a>
                <a href="/transparencia/financeiro" class="btn btn-outline">
                    Relatórios Financeiros
                </a>
                <a href="/transparencia/painel" class="btn btn-primary">
                    Painel de Transferências
                </a>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.proj-tab');
    const searchInput = document.getElementById('proj-search-input');
    const items = document.querySelectorAll('.proj-card');
    
    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        let visibleCount = 0;
        items.forEach(card => {
            const cat = card.getAttribute('data-category');
            const searchData = card.getAttribute('data-search') || '';
            
            const matchFilter = (currentFilter === 'all') || (cat === currentFilter);
            const matchSearch = (!currentSearch) || searchData.includes(currentSearch);
            
            if (matchFilter && matchSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        let emptyMsg = document.getElementById('proj-empty-filter-msg');
        if (visibleCount === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.id = 'proj-empty-filter-msg';
                emptyMsg.className = 'proj-empty-box';
                emptyMsg.innerHTML = '<i class="ph ph-magnifying-glass"></i><h3>Nenhum projeto encontrado</h3><p>Não há registros correspondentes aos critérios de busca selecionados.</p>';
                const grid = document.getElementById('proj-items-grid');
                if (grid) grid.after(emptyMsg);
            }
            emptyMsg.style.display = 'block';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentSearch = this.value.trim().toLowerCase();
            applyFilters();
        });
    }
});
</script>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
