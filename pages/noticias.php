<?php
/**
 * Notícias — Instituto Nacional de Políticas Públicas (INPP)
 * Design System Editorial & Transparência
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Notícias & Coberturas Institucionais';
$page_description = 'Acompanhe as últimas notícias, comunicados oficiais, coberturas de projetos comunitários e relatórios de ações do Instituto Nacional de Políticas Públicas (INPP).';

// Filtro de busca
$busca = trim($_GET['busca'] ?? '');
$where = 'WHERE 1=1';
$params = [];

if (!empty($busca)) {
    $where .= ' AND (manchete LIKE ? OR resumo LIKE ? OR noticia LIKE ?)';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
}

// Estatísticas para a Régua
$total_geral  = db_count('SELECT COUNT(*) FROM tab_noticias');
$row_recente  = db_fetch('SELECT MAX(data_noticia) as max_data FROM tab_noticias');
$data_recente = $row_recente['max_data'] ?? null;

// Paginação
$total_noticias = db_count("SELECT COUNT(*) FROM tab_noticias $where", $params);
$por_pagina     = 9;
$paginacao      = paginate($total_noticias, $por_pagina);

$params_query = array_merge($params, [$por_pagina, $paginacao['offset']]);
$noticias = db_fetch_all(
    "SELECT * FROM tab_noticias $where ORDER BY data_noticia DESC, id DESC LIMIT ? OFFSET ?",
    $params_query
);

// Lógica de Destaque vs Grid:
// Na página 1 sem busca, a primeira notícia torna-se o card Destaque Principal
$destaque = null;
$grid_noticias = $noticias;

if ($paginacao['current_page'] === 1 && empty($busca) && !empty($noticias)) {
    $destaque = $noticias[0];
    $grid_noticias = array_slice($noticias, 1);
}

ob_start();
?>
<!-- HERO INSTITUCIONAL EDITORIAL -->
<section class="news-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Notícias &amp; Comunicação</span>
        </nav>
        
        <div class="news-hero-content">
            <span class="news-badge-press">
                <i class="ph ph-newspaper-clipping"></i> Comunicação &amp; Imprensa
            </span>
            <h1 class="news-hero-title">Notícias, Ações e Coberturas Institucionais</h1>
            <p class="news-hero-lead">
                Acompanhe os comunicados oficiais, coberturas de projetos comunitários, eventos cívicos e os desdobramentos das políticas públicas articuladas pelo INPP em prol da cidadania e da inclusão social.
            </p>
        </div>

        <!-- RÉGUA DE INDICADORES EDITORIAIS -->
        <div class="news-facts-bar">
            <div class="news-fact-item">
                <span class="news-fact-label">Acervo Publicado</span>
                <strong class="news-fact-val"><?= $total_geral ?> <?= $total_geral === 1 ? 'Matéria' : 'Matérias' ?></strong>
                <span class="news-fact-desc">Notícias e comunicados</span>
            </div>
            <div class="news-fact-item">
                <span class="news-fact-label">Linha Editorial</span>
                <strong class="news-fact-val">Transparência &amp; Ação</strong>
                <span class="news-fact-desc">Controle social e impacto</span>
            </div>
            <div class="news-fact-item">
                <span class="news-fact-label">Última Publicação</span>
                <strong class="news-fact-val"><?= !empty($data_recente) ? format_date($data_recente) : 'Atualizada' ?></strong>
                <span class="news-fact-desc">Competência mais recente</span>
            </div>
            <div class="news-fact-item">
                <span class="news-fact-label">Assessoria de Imprensa</span>
                <strong class="news-fact-val">Canal Aberto</strong>
                <span class="news-fact-desc">contato@inpp.org</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO PRINCIPAL DE NOTÍCIAS -->
<section class="news-main-section" id="conteudo-noticias">
    <div class="container">

        <!-- BARRA DE PESQUISA & CONTROLE -->
        <div class="news-filter-bar">
            <form method="GET" action="/noticias" class="news-search-form">
                <div class="news-search-input-wrap">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" name="busca" value="<?= e($busca) ?>" class="news-search-input" placeholder="Pesquisar por tema, projeto ou palavra-chave...">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    Buscar
                </button>
                <?php if (!empty($busca)): ?>
                <a href="/noticias" class="btn btn-outline btn-sm">
                    Limpar Filtro
                </a>
                <?php endif; ?>
            </form>

            <span class="news-count-pill">
                <?= $total_noticias ?> <?= $total_noticias === 1 ? 'publicação catalogada' : 'publicações catalogadas' ?>
            </span>
        </div>

        <?php if (empty($noticias)): ?>
        <!-- ESTADO VAZIO -->
        <div class="proj-empty-box">
            <i class="ph ph-newspaper"></i>
            <h3>Nenhuma notícia encontrada</h3>
            <p>
                <?= !empty($busca) 
                    ? 'Nenhum resultado correspondeu ao termo pesquisado ("' . e($busca) . '"). Experimente outros termos ou limpe o filtro.' 
                    : 'Em breve novos comunicados e coberturas de projetos serão publicados pela nossa assessoria.' ?>
            </p>
            <?php if (!empty($busca)): ?>
            <a href="/noticias" class="btn btn-primary btn-sm">Ver Todas as Notícias</a>
            <?php else: ?>
            <a href="/" class="btn btn-outline btn-sm">Voltar à Página Inicial</a>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <!-- NOTÍCIA EM DESTAQUE (CARD WIDESCREEN NA PÁGINA 1) -->
        <?php if ($destaque): ?>
        <?php
            $dest_img_path = !empty($destaque['imagem_inicio']) ? UPLOAD_PATH . '/noticias/' . $destaque['imagem_inicio'] : '';
            $tem_dest_img  = !empty($destaque['imagem_inicio']) && file_exists($dest_img_path);
            $dest_img_url  = $tem_dest_img ? '/uploads/noticias/' . rawurlencode($destaque['imagem_inicio']) : '/assets/img/hero_inpp_intergeracional.jpg';
        ?>
        <article class="news-featured-card">
            <a href="/noticia/<?= (int)$destaque['id'] ?>" class="news-featured-media" aria-label="<?= e($destaque['manchete']) ?>">
                <span class="news-featured-badge-overlay">
                    <i class="ph ph-star-fill"></i> Destaque Institucional
                </span>
                <img src="<?= e($dest_img_url) ?>" alt="<?= e($destaque['manchete']) ?>" loading="eager">
            </a>
            <div class="news-featured-content">
                <div class="news-featured-meta">
                    <span class="news-meta-point">
                        <i class="ph ph-calendar-blank"></i> <?= format_date($destaque['data_noticia']) ?>
                    </span>
                    <span class="news-meta-point">
                        <i class="ph ph-clock"></i> <?= calcular_tempo_leitura($destaque['noticia'] ?: $destaque['resumo']) ?>
                    </span>
                    <span class="news-meta-point">
                        <i class="ph ph-eye"></i> <?= number_format((int)$destaque['n_visitas'], 0, ',', '.') ?> visualizações
                    </span>
                </div>

                <h2 class="news-featured-title">
                    <a href="/noticia/<?= (int)$destaque['id'] ?>"><?= e($destaque['manchete']) ?></a>
                </h2>

                <p class="news-featured-desc">
                    <?= e(truncate($destaque['resumo'] ?: strip_tags($destaque['noticia']), 230)) ?>
                </p>

                <div class="news-featured-action">
                    <a href="/noticia/<?= (int)$destaque['id'] ?>" class="btn btn-primary">
                        Ler Matéria Completa <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>
        </article>
        <?php endif; ?>

        <!-- GRADE EDITORIAL DE NOTÍCIAS -->
        <?php if (!empty($grid_noticias)): ?>
        <div class="news-section-header">
            <div>
                <span class="qs-kicker">Acervo de Coberturas</span>
                <h3 class="qs-title" style="margin-bottom:0;"><?= $destaque ? 'Outras Notícias e Comunicados' : 'Notícias Publicadas' ?></h3>
            </div>
        </div>

        <div class="news-grid-editorial">
            <?php foreach ($grid_noticias as $n): ?>
            <?php
                $n_img_path = !empty($n['imagem_inicio']) ? UPLOAD_PATH . '/noticias/' . $n['imagem_inicio'] : '';
                $n_tem_img  = !empty($n['imagem_inicio']) && file_exists($n_img_path);
                $n_img_url  = $n_tem_img ? '/uploads/noticias/' . rawurlencode($n['imagem_inicio']) : '/assets/img/hero_inpp_intergeracional.jpg';
            ?>
            <article class="news-card-v2">
                <div class="news-card-media-wrap">
                    <span class="news-card-badge-top">Institucional</span>
                    <a href="/noticia/<?= (int)$n['id'] ?>" tabindex="-1" aria-hidden="true">
                        <img src="<?= e($n_img_url) ?>" alt="<?= e($n['manchete']) ?>" loading="lazy">
                    </a>
                </div>

                <div class="news-card-body-v2">
                    <div class="news-card-meta">
                        <span class="news-meta-point">
                            <i class="ph ph-calendar-blank"></i> <?= format_date($n['data_noticia']) ?>
                        </span>
                        <span>&bull;</span>
                        <span class="news-meta-point">
                            <i class="ph ph-clock"></i> <?= calcular_tempo_leitura($n['noticia'] ?: $n['resumo']) ?>
                        </span>
                    </div>

                    <h3 class="news-card-title-v2">
                        <a href="/noticia/<?= (int)$n['id'] ?>"><?= e($n['manchete']) ?></a>
                    </h3>

                    <p class="news-card-excerpt">
                        <?= e(truncate($n['resumo'] ?: strip_tags($n['noticia']), 130)) ?>
                    </p>

                    <div class="news-card-footer-v2">
                        <span class="news-meta-point" style="font-size:0.75rem; color:#64748b;">
                            <i class="ph ph-eye"></i> <?= number_format((int)$n['n_visitas'], 0, ',', '.') ?> acessos
                        </span>
                        <a href="/noticia/<?= (int)$n['id'] ?>" class="news-read-btn">
                            Acessar Notícia <i class="ph ph-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- PAGINAÇÃO -->
        <?php if ($paginacao['total_pages'] > 1): ?>
        <div class="pagination-wrap" style="margin-top: 3.5rem;">
            <?= pagination_html($paginacao, '/noticias' . (!empty($busca) ? '?busca=' . urlencode($busca) : '')) ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
