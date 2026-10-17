<?php
/**
 * Notícias — Instituto Nacional de Políticas Públicas
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Notícias';
$page_description = 'Fique por dentro das últimas novidades e notícias do Instituto Nacional de Políticas Públicas (INPP).';

$por_pagina = 12;
$paginacao  = paginate(db_count('SELECT COUNT(*) FROM tab_noticias'), $por_pagina);

$noticias = db_fetch_all(
    'SELECT * FROM tab_noticias ORDER BY data_noticia DESC LIMIT ? OFFSET ?',
    [$por_pagina, $paginacao['offset']]
);

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Notícias</span>
        </nav>
        <h1 class="page-hero-title">Notícias</h1>
        <p class="page-hero-sub">As últimas novidades do Instituto Nacional de Políticas Públicas (INPP).</p>
    </div>
</section>

<section class="section" id="noticias">
    <div class="container">
        <?php if (empty($noticias)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon"><i data-lucide="newspaper"></i></div>
            <h3>Nenhuma notícia publicada</h3>
            <p>Em breve publicaremos novidades por aqui. Volte logo!</p>
        </div>
        <?php else: ?>
        <div class="news-grid">
            <?php foreach ($noticias as $n): ?>
            <article class="news-card fade-in-up">
                <?php
                $img_path = UPLOAD_PATH . '/noticias/' . $n['imagem_inicio'];
                $tem_img  = !empty($n['imagem_inicio']) && file_exists($img_path);
                ?>
                <a href="/noticia/<?= (int)$n['id'] ?>" class="news-card-img-link" tabindex="-1" aria-hidden="true">
                    <?php if ($tem_img): ?>
                    <img src="/uploads/noticias/<?= e($n['imagem_inicio']) ?>"
                         alt="<?= e($n['manchete']) ?>"
                         class="news-card-img" loading="lazy">
                    <?php else: ?>
                    <div class="news-card-img-placeholder">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <?php endif; ?>
                </a>
                <div class="news-card-body">
                    <?php if ($n['data_noticia']): ?>
                    <time class="news-date" datetime="<?= e($n['data_noticia']) ?>">
                        <?= format_date($n['data_noticia']) ?>
                    </time>
                    <?php endif; ?>
                    <h2 class="news-card-title">
                        <a href="/noticia/<?= (int)$n['id'] ?>"><?= e($n['manchete']) ?></a>
                    </h2>
                    <?php if ($n['resumo']): ?>
                    <p class="news-card-summary"><?= e(truncate($n['resumo'], 120)) ?></p>
                    <?php endif; ?>
                    <a href="/noticia/<?= (int)$n['id'] ?>" class="news-card-link">
                        Leia mais
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($paginacao['total_pages'] > 1): ?>
        <div class="pagination-wrap">
            <?= pagination_html($paginacao, '/noticias') ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
