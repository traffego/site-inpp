<?php
/**
 * Editais & Dossiê de Projetos — Instituto Nacional de Políticas Públicas (INPP)
 * Design System Editorial & MROSC Transparência Ativa
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$filtro_projeto = (int)($_GET['projeto'] ?? 0);
$busca          = trim($_GET['busca'] ?? '');

// Mapeamento institucional de classes de status
$status_class = [
    'Concluído'              => 'proj-status-concluido',
    'Em Execução'            => 'proj-status-execucao',
    'Em Prestação de Contas' => 'proj-status-prestacao',
    'Assinado'               => 'proj-status-execucao',
    'Proposta Aprovada'      => 'proj-status-execucao',
];

// Projetos que possuem documentos ativos para o dropdown de filtro
$todos_projetos = db_fetch_all(
    'SELECT p.id, p.nome_projeto, p.num_proposta, p.termo_fomento 
     FROM tab_projetos p 
     WHERE p.ativo = 1 
       AND p.id IN (SELECT DISTINCT id_projeto FROM tab_projetos_documentos WHERE habilitado = 1)
     ORDER BY p.id DESC'
);

// MODO A: Dossiê Específico de um Projeto (?projeto=ID)
$projeto_dossie = null;
if ($filtro_projeto > 0) {
    $projeto_dossie = db_fetch(
        "SELECT p.*, COALESCE(ps.projetos_status, 'Em Execução') as status_nome
         FROM tab_projetos p
         LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
         WHERE p.id = ? AND p.ativo = 1",
        [$filtro_projeto]
    );
}

// Se o projeto do dossiê foi encontrado
if ($projeto_dossie):
    $page_title       = 'Dossiê do Projeto: ' . $projeto_dossie['nome_projeto'];
    $page_description = 'Consulte a documentação completa, editais, termos de fomento e prestações de contas oficiais do projeto ' . $projeto_dossie['nome_projeto'] . ' do INPP.';

    // Buscar todos os documentos habilitados do projeto
    $where_docs = 'WHERE d.id_projeto = ? AND d.habilitado = 1';
    $params_docs = [$projeto_dossie['id']];
    if (!empty($busca)) {
        $where_docs .= ' AND (d.nome_documento LIKE ? OR g.nome_grupo LIKE ?)';
        $params_docs[] = '%' . $busca . '%';
        $params_docs[] = '%' . $busca . '%';
    }

    $todos_docs = db_fetch_all(
        "SELECT d.*, t.tipo_documento, g.nome_grupo
         FROM tab_projetos_documentos d
         LEFT JOIN tab_projetos_documentos_tipo t ON d.id_tipo_documento = t.id
         LEFT JOIN tab_projetos_grupo_doc g ON d.id_grupo_documento = g.id
         $where_docs
         ORDER BY COALESCE(g.posicao, 999), d.id_grupo_documento, d.id",
        $params_docs
    );

    // Agrupar documentos por grupo
    $docs_por_grupo = [];
    foreach ($todos_docs as $doc) {
        $gname = !empty($doc['nome_grupo']) ? $doc['nome_grupo'] : 'Editais & Chamamentos Gerais';
        $docs_por_grupo[$gname][] = $doc;
    }

    $st_nome = $projeto_dossie['status_nome'];
    $st_cls = $status_class[$st_nome] ?? 'proj-status-execucao';

    ob_start();
?>
<!-- HERO DO DOSSIÊ DO PROJETO -->
<section class="proj-dossier-hero">
    <div class="container">
        
        <div class="proj-dossier-nav">
            <nav class="breadcrumb" aria-label="Navegação" style="margin-bottom:0;">
                <a href="/">Início</a>
                <span aria-hidden="true">›</span>
                <a href="/projetos">Projetos &amp; Parcerias</a>
                <span aria-hidden="true">›</span>
                <span>Dossiê &amp; Editais</span>
            </nav>

            <a href="/projetos" class="proj-back-btn">
                <i class="ph ph-arrow-left"></i> Voltar ao Portfólio de Projetos
            </a>
        </div>

        <div class="proj-dossier-badges">
            <span class="proj-badge-mrosc">
                <i class="ph ph-seal-check"></i> Parceria Pública &bull; MROSC Lei 13.019/14
            </span>
            <span class="proj-status-pill <?= $st_cls ?>">
                <i class="ph ph-activity"></i> <?= e($st_nome) ?>
            </span>
        </div>

        <h1 class="proj-dossier-title"><?= e($projeto_dossie['nome_projeto']) ?></h1>

        <!-- RÉGUA DE FICHA TÉCNICA -->
        <div class="proj-dossier-facts-grid">
            <div class="proj-dossier-fact-item">
                <span class="proj-dossier-fact-label">Nº Proposta / SICONV</span>
                <strong class="proj-dossier-fact-val"><?= !empty($projeto_dossie['num_proposta']) ? e($projeto_dossie['num_proposta']) : 'Catalogado' ?></strong>
                <span class="proj-dossier-fact-desc">Identificador federal</span>
            </div>

            <div class="proj-dossier-fact-item">
                <span class="proj-dossier-fact-label">Termo de Fomento</span>
                <strong class="proj-dossier-fact-val"><?= !empty($projeto_dossie['termo_fomento']) ? e($projeto_dossie['termo_fomento']) : 'MROSC' ?></strong>
                <span class="proj-dossier-fact-desc">Instrumento formal</span>
            </div>

            <div class="proj-dossier-fact-item">
                <span class="proj-dossier-fact-label">Valor do Repasse</span>
                <strong class="proj-dossier-fact-val"><?= !empty($projeto_dossie['valor']) ? e($projeto_dossie['valor']) : 'Sob demanda' ?></strong>
                <span class="proj-dossier-fact-desc">Recurso vinculado</span>
            </div>

            <div class="proj-dossier-fact-item">
                <span class="proj-dossier-fact-label">Período de Vigência</span>
                <strong class="proj-dossier-fact-val">
                    <?php if (!empty($projeto_dossie['inicio_vigencia']) && !empty($projeto_dossie['termino_vigencia'])): ?>
                        <?= format_date($projeto_dossie['inicio_vigencia'], 'm/Y') ?> &ndash; <?= format_date($projeto_dossie['termino_vigencia'], 'm/Y') ?>
                    <?php else: ?>
                        Exercício Vigente
                    <?php endif; ?>
                </strong>
                <span class="proj-dossier-fact-desc">Cronograma oficial</span>
            </div>

            <div class="proj-dossier-fact-item">
                <span class="proj-dossier-fact-label">Prestação de Contas</span>
                <strong class="proj-dossier-fact-val">100% Auditável</strong>
                <span class="proj-dossier-fact-desc">Controle social aberto</span>
            </div>
        </div>

    </div>
</section>

<!-- SEÇÃO PRINCIPAL DO DOSSIÊ: CONTEÚDO E DOCUMENTOS -->
<section class="proj-dossier-section">
    <div class="container">
        
        <div class="proj-dossier-layout">
            
            <!-- COLUNA PRINCIPAL: DETALHAMENTO & ARQUIVOS -->
            <main class="proj-dossier-main">
                
                <!-- CARD: OBJETO & ESCOPO DO PROJETO -->
                <div class="proj-dossier-about-card">
                    <h3 class="proj-dossier-about-title">
                        <i class="ph ph-target"></i> Objeto e Apresentação Institucional
                    </h3>
                    <?php if (!empty($projeto_dossie['objeto'])): ?>
                    <p class="proj-dossier-about-text">
                        <strong>Objeto:</strong> <?= nl2br(e($projeto_dossie['objeto'])) ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($projeto_dossie['apresentacao'])): ?>
                    <p class="proj-dossier-about-text">
                        <?= nl2br(e($projeto_dossie['apresentacao'])) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- CABEÇALHO DA SEÇÃO DE DOCUMENTOS -->
                <div class="news-section-header">
                    <div>
                        <span class="qs-kicker">Transparência Ativa &amp; LAI</span>
                        <h3 class="qs-title" style="margin-bottom:0;">Editais, Chamamentos &amp; Documentos</h3>
                    </div>
                    <span class="news-count-pill">
                        <?= count($todos_docs) ?> <?= count($todos_docs) === 1 ? 'documento oficial' : 'documentos oficiais' ?>
                    </span>
                </div>

                <!-- BARRA DE BUSCA RÁPIDA DE ARQUIVOS NO DOSSIÊ -->
                <div class="news-filter-bar" style="margin-bottom:2rem;">
                    <form method="GET" action="/editais" class="news-search-form" style="max-width:100%;">
                        <input type="hidden" name="projeto" value="<?= (int)$projeto_dossie['id'] ?>">
                        <div class="news-search-input-wrap">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" name="busca" value="<?= e($busca) ?>" class="news-search-input" placeholder="Pesquisar nos editais e arquivos deste projeto...">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                        <?php if (!empty($busca)): ?>
                        <a href="/editais?projeto=<?= (int)$projeto_dossie['id'] ?>" class="btn btn-outline btn-sm">Limpar</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- LISTAGEM DE DOCUMENTOS POR GRUPOS TEMÁTICOS -->
                <?php if (empty($todos_docs)): ?>
                <div class="proj-empty-box">
                    <i class="ph ph-folder-dashed"></i>
                    <h3>Nenhum documento encontrado neste critério</h3>
                    <p>
                        <?= !empty($busca) 
                            ? 'Nenhum edital correspondeu ao termo pesquisado ("' . e($busca) . '").' 
                            : 'A documentação deste projeto está em processo de homologação e upload pelos órgãos competentes.' ?>
                    </p>
                    <?php if (!empty($busca)): ?>
                    <a href="/editais?projeto=<?= (int)$projeto_dossie['id'] ?>" class="btn btn-primary btn-sm">Ver Todos os Documentos do Projeto</a>
                    <?php endif; ?>
                </div>
                <?php else: ?>

                <div class="proj-groups-wrap">
                    <?php foreach ($docs_por_grupo as $nome_grupo => $lista_docs): ?>
                    <div class="proj-group-card">
                        <div class="proj-group-header">
                            <h4 class="proj-group-title">
                                <i class="ph ph-folders"></i> <?= e($nome_grupo) ?>
                            </h4>
                            <span class="proj-group-badge">
                                <?= count($lista_docs) ?> <?= count($lista_docs) === 1 ? 'arquivo anexado' : 'arquivos anexados' ?>
                            </span>
                        </div>

                        <div class="proj-docs-list-v2">
                            <?php foreach ($lista_docs as $doc): ?>
                            <?php
                                $nome_arq = !empty($doc['arquivo']) ? $doc['arquivo'] : ((int)$doc['id'] . '.pdf');
                                $caminho_arq = UPLOAD_PATH . '/projetos/' . $nome_arq;
                                $tamanho_kb = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024, 1) : 0;
                                $tamanho_txt = $tamanho_kb > 1024 ? round($tamanho_kb / 1024, 1) . ' MB' : ($tamanho_kb > 0 ? $tamanho_kb . ' KB' : 'PDF');
                                $ext = strtolower(pathinfo($nome_arq, PATHINFO_EXTENSION));
                                $is_excel = in_array($ext, ['xlsx', 'xls', 'csv']);
                                $tipo_nome = !empty($doc['tipo_documento']) ? $doc['tipo_documento'] : 'Edital';
                            ?>
                            <div class="proj-doc-item-v2">
                                <div class="proj-doc-item-left">
                                    <div class="proj-doc-icon-wrap <?= $is_excel ? 'excel' : '' ?>">
                                        <i class="ph <?= $is_excel ? 'ph-file-xls' : 'ph-file-pdf' ?>"></i>
                                    </div>

                                    <div class="proj-doc-info">
                                        <h5 class="proj-doc-title-v2"><?= e($doc['nome_documento']) ?></h5>
                                        <div class="proj-doc-meta-row">
                                            <span class="proj-doc-badge-type"><?= e($tipo_nome) ?></span>
                                            <span>&bull;</span>
                                            <span><i class="ph ph-hard-drive"></i> <?= $tamanho_txt ?></span>
                                            <?php if (!empty($doc['created_at'])): ?>
                                            <span>&bull;</span>
                                            <span><i class="ph ph-calendar-blank"></i> Publicado em <?= format_date(substr($doc['created_at'], 0, 10)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <a href="/uploads/projetos/<?= e($nome_arq) ?>" target="_blank" rel="noopener noreferrer" class="btn-download-doc">
                                    <i class="ph ph-file-arrow-down"></i> Baixar Documento Oficial
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; ?>

            </main>

            <!-- COLUNA LATERAL: CONFORMIDADE & CONTROLE SOCIAL -->
            <aside class="proj-dossier-sidebar">
                
                <!-- CARD 1: MARCO REGULATÓRIO MROSC -->
                <div class="proj-compliance-card">
                    <div class="proj-compliance-header">
                        <i class="ph ph-shield-check" style="color:var(--color-primary); font-size:1.35rem;"></i>
                        <h4 class="proj-compliance-title">Marco Regulatório &bull; MROSC</h4>
                    </div>
                    <p class="proj-compliance-text">
                        Todas as contratações, termos de referência, homologações e seleções deste projeto cumprem rigorosamente os preceitos da Lei Federal nº 13.019/2014 e o Regulamento Próprio de Compras do INPP.
                    </p>
                    <a href="/transparencia/regulamento" class="btn btn-outline btn-sm w-100" style="justify-content:center;">
                        Ver Regulamento de Compras <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- CARD 2: OUVIDORIA & LEI DE ACESSO À INFORMAÇÃO -->
                <div class="proj-compliance-card">
                    <div class="proj-compliance-header">
                        <i class="ph ph-chat-teardrop-text" style="color:var(--color-primary); font-size:1.35rem;"></i>
                        <h4 class="proj-compliance-title">Acesso à Informação</h4>
                    </div>
                    <p class="proj-compliance-text">
                        Qualquer cidadão ou órgão fiscalizador pode solicitar certidões ou esclarecimentos sobre os atos deste projeto por meio da nossa Ouvidoria Geral.
                    </p>
                    <a href="/contato" class="btn btn-primary btn-sm w-100" style="justify-content:center;">
                        Fale com a Ouvidoria
                    </a>
                </div>

                <!-- CARD 3: PORTFÓLIO GERAL -->
                <div class="proj-compliance-card">
                    <div class="proj-compliance-header">
                        <i class="ph ph-folders" style="color:var(--color-primary); font-size:1.35rem;"></i>
                        <h4 class="proj-compliance-title">Outros Projetos</h4>
                    </div>
                    <p class="proj-compliance-text">
                        Conheça outras parcerias executadas pelo Instituto Nacional de Políticas Públicas em todo o território nacional.
                    </p>
                    <a href="/projetos" class="btn btn-outline btn-sm w-100" style="justify-content:center;">
                        Ver Todos os Projetos
                    </a>
                </div>

            </aside>

        </div>

    </div>
</section>

<?php
    $content = ob_get_clean();
    require ROOT_PATH . '/templates/layout.php';
    exit;
endif;

// ==============================================================================
// MODO B: Listagem Geral de Editais & Documentos (/editais)
// ==============================================================================

$page_title       = 'Editais & Transparência Documental';
$page_description = 'Consulte os editais públicos, processos seletivos e documentos de prestação de contas dos projetos do Instituto Nacional de Políticas Públicas (INPP).';

$where_geral = 'WHERE p.ativo = 1 AND p.id IN (SELECT DISTINCT id_projeto FROM tab_projetos_documentos WHERE habilitado = 1)';
$params_geral = [];

if (!empty($busca)) {
    $where_geral .= ' AND (p.nome_projeto LIKE ? OR p.num_proposta LIKE ? OR p.termo_fomento LIKE ?)';
    $params_geral[] = '%' . $busca . '%';
    $params_geral[] = '%' . $busca . '%';
    $params_geral[] = '%' . $busca . '%';
}

$projetos_lista = db_fetch_all(
    "SELECT p.*, COALESCE(ps.projetos_status, 'Em Execução') as status_nome
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     $where_geral
     ORDER BY p.id DESC",
    $params_geral
);

// Estatísticas
$total_editais_publicados = db_count("SELECT COUNT(*) FROM tab_projetos_documentos WHERE habilitado = 1");
$total_projetos_ativos    = count($projetos_lista);

ob_start();
?>
<!-- HERO EDITORIAL DE EDITAIS -->
<section class="news-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Editais &amp; Chamamentos</span>
        </nav>

        <div class="news-hero-content">
            <span class="news-badge-press">
                <i class="ph ph-file-check"></i> Transparência Ativa &bull; MROSC
            </span>
            <h1 class="news-hero-title">Editais, Chamamentos &amp; Documentos de Projetos</h1>
            <p class="news-hero-lead">
                Consulte os editais de chamamento público, homologações, processos seletivos e atos formais de execução dos projetos apoiados por parcerias públicas no INPP.
            </p>
        </div>

        <!-- RÉGUA DE INDICADORES -->
        <div class="news-facts-bar">
            <div class="news-fact-item">
                <span class="news-fact-label">Editais &amp; Atos Publicados</span>
                <strong class="news-fact-val"><?= $total_editais_publicados ?> <?= $total_editais_publicados === 1 ? 'Documento' : 'Documentos' ?></strong>
                <span class="news-fact-desc">Disponíveis para download</span>
            </div>
            <div class="news-fact-item">
                <span class="news-fact-label">Projetos com Editais</span>
                <strong class="news-fact-val"><?= $total_projetos_ativos ?> <?= $total_projetos_ativos === 1 ? 'Projeto' : 'Projetos' ?></strong>
                <span class="news-fact-desc">Parcerias com arquivos ativos</span>
            </div>
            <div class="news-fact-item">
                <span class="news-fact-label">Marco Regulatório</span>
                <strong class="news-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="news-fact-desc">Publicidade de atos e despesas</span>
            </div>
            <div class="news-fact-item">
                <span class="news-fact-label">Controle Social</span>
                <strong class="news-fact-val">100% Auditável</strong>
                <span class="news-fact-desc">Acesso público irrestrito</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO PRINCIPAL DE EDITAIS -->
<section class="news-main-section">
    <div class="container">
        
        <!-- BARRA DE FILTROS & SELEÇÃO DE PROJETO -->
        <div class="news-filter-bar">
            <form method="GET" action="/editais" class="news-search-form" style="max-width:100%; display:flex; gap:1rem; flex-wrap:wrap; width:100%;">
                
                <div style="flex:1; min-width:240px;">
                    <select name="projeto" class="news-search-input" onchange="this.form.submit()" style="padding-left:1rem; cursor:pointer;">
                        <option value="">Selecione um projeto específico...</option>
                        <?php foreach ($todos_projetos as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= e($p['nome_projeto']) ?> <?= !empty($p['num_proposta']) ? '(Nº ' . e($p['num_proposta']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="news-search-input-wrap" style="flex:1; min-width:240px;">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" name="busca" value="<?= e($busca) ?>" class="news-search-input" placeholder="Buscar por termo ou número de proposta...">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>

                <?php if (!empty($busca)): ?>
                <a href="/editais" class="btn btn-outline btn-sm">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($projetos_lista)): ?>
        <div class="proj-empty-box">
            <i class="ph ph-file-x"></i>
            <h3>Nenhum edital encontrado</h3>
            <p>
                <?= !empty($busca) 
                    ? 'Nenhum projeto ou edital correspondeu à busca ("' . e($busca) . '").' 
                    : 'Não há editais disponíveis para consulta pública no momento.' ?>
            </p>
            <a href="/editais" class="btn btn-primary btn-sm">Ver Todos</a>
        </div>
        <?php else: ?>

        <!-- LISTA DE PROJETOS E SEUS EDITAIS -->
        <div style="display:flex; flex-direction:column; gap:2.5rem;">
            <?php foreach ($projetos_lista as $proj): ?>
            <?php
                $docs = db_fetch_all(
                    "SELECT d.*, t.tipo_documento, g.nome_grupo
                     FROM tab_projetos_documentos d
                     LEFT JOIN tab_projetos_documentos_tipo t ON d.id_tipo_documento = t.id
                     LEFT JOIN tab_projetos_grupo_doc g ON d.id_grupo_documento = g.id
                     WHERE d.id_projeto = ? AND d.habilitado = 1
                     ORDER BY COALESCE(g.posicao, 999), d.id_grupo_documento, d.id",
                    [$proj['id']]
                );

                $st_n = $proj['status_nome'];
                $st_c = $status_class[$st_n] ?? 'proj-status-execucao';
            ?>
            <article class="proj-group-card">
                <div class="proj-group-header">
                    <div>
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.35rem;">
                            <span class="proj-badge-mrosc" style="font-size:0.7rem; padding:0.2rem 0.6rem;">
                                Parceria Pública
                            </span>
                            <span class="proj-status-pill <?= $st_c ?>" style="font-size:0.7rem; padding:0.2rem 0.6rem;">
                                <?= e($st_n) ?>
                            </span>
                        </div>
                        <h3 style="font-size:1.25rem; font-weight:800; color:#07598c; margin:0;">
                            <a href="/editais?projeto=<?= (int)$proj['id'] ?>" style="color:inherit; text-decoration:none;">
                                <?= e($proj['nome_projeto']) ?>
                            </a>
                        </h3>
                    </div>

                    <a href="/editais?projeto=<?= (int)$proj['id'] ?>" class="btn btn-outline btn-sm">
                        Abrir Dossiê Completo <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <div class="proj-docs-list-v2">
                    <?php foreach ($docs as $doc): ?>
                    <?php
                        $nome_arq = !empty($doc['arquivo']) ? $doc['arquivo'] : ((int)$doc['id'] . '.pdf');
                        $caminho_arq = UPLOAD_PATH . '/projetos/' . $nome_arq;
                        $tamanho_kb = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024, 1) : 0;
                        $tamanho_txt = $tamanho_kb > 1024 ? round($tamanho_kb / 1024, 1) . ' MB' : ($tamanho_kb > 0 ? $tamanho_kb . ' KB' : 'PDF');
                        $ext = strtolower(pathinfo($nome_arq, PATHINFO_EXTENSION));
                        $is_excel = in_array($ext, ['xlsx', 'xls', 'csv']);
                        $tipo_nome = !empty($doc['tipo_documento']) ? $doc['tipo_documento'] : 'Edital';
                    ?>
                    <div class="proj-doc-item-v2">
                        <div class="proj-doc-item-left">
                            <div class="proj-doc-icon-wrap <?= $is_excel ? 'excel' : '' ?>">
                                <i class="ph <?= $is_excel ? 'ph-file-xls' : 'ph-file-pdf' ?>"></i>
                            </div>

                            <div class="proj-doc-info">
                                <h5 class="proj-doc-title-v2"><?= e($doc['nome_documento']) ?></h5>
                                <div class="proj-doc-meta-row">
                                    <span class="proj-doc-badge-type"><?= e($tipo_nome) ?></span>
                                    <span>&bull;</span>
                                    <span><i class="ph ph-hard-drive"></i> <?= $tamanho_txt ?></span>
                                    <?php if (!empty($doc['nome_grupo'])): ?>
                                    <span>&bull;</span>
                                    <span><i class="ph ph-folder"></i> <?= e($doc['nome_grupo']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <a href="/uploads/projetos/<?= e($nome_arq) ?>" target="_blank" rel="noopener noreferrer" class="btn-download-doc">
                            <i class="ph ph-file-arrow-down"></i> Baixar Arquivo
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
