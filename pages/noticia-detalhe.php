<?php
/**
 * Detalhe da Notícia — Instituto Nacional de Políticas Públicas
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(404);
    $page_title = 'Notícia não encontrada';
    ob_start();
    echo '<section class="section"><div class="container"><div class="empty-state"><h2>Notícia não encontrada</h2><a href="/noticias" class="btn btn-primary">Ver todas as notícias</a></div></div></section>';
    $content = ob_get_clean();
    require ROOT_PATH . '/templates/layout.php';
    exit;
}

$noticia = db_fetch('SELECT * FROM tab_noticias WHERE id = ?', [$id]);

if (!$noticia) {
    http_response_code(404);
    $page_title = 'Notícia não encontrada';
    ob_start();
    echo '<section class="section"><div class="container"><div class="empty-state"><h2>Notícia não encontrada</h2><p>A notícia solicitada não existe ou foi removida.</p><a href="/noticias" class="btn btn-primary">Ver todas as notícias</a></div></div></section>';
    $content = ob_get_clean();
    require ROOT_PATH . '/templates/layout.php';
    exit;
}

// Incrementar visitas
db_update('tab_noticias', ['n_visitas' => (int)$noticia['n_visitas'] + 1], 'id = ?', [$id]);

$page_title       = $noticia['manchete'];
$page_description = $noticia['resumo'] ? truncate($noticia['resumo'], 160) : truncate(strip_tags($noticia['noticia']), 160);

ob_start();
?>
<section class="page-hero page-hero-sm">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/noticias">Notícias</a>
            <span aria-hidden="true">›</span>
            <span><?= e(truncate($noticia['manchete'], 50)) ?></span>
        </nav>
    </div>
</section>

<article class="section article-detail" id="noticia-<?= (int)$noticia['id'] ?>">
    <div class="container container-article">

        <!-- Imagem superior -->
        <?php if (!empty($noticia['imagem_inicio']) && file_exists(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio'])): ?>
        <figure class="article-img-hero">
            <img src="/uploads/noticias/<?= e($noticia['imagem_inicio']) ?>"
                 alt="<?= e($noticia['manchete']) ?>"
                 class="article-img-full">
        </figure>
        <?php endif; ?>

        <header class="article-header fade-in-up">
            <?php if ($noticia['data_noticia']): ?>
            <time class="article-date" datetime="<?= e($noticia['data_noticia']) ?>">
                <?= format_date($noticia['data_noticia'], 'd \d\e F \d\e Y') ?>
            </time>
            <?php endif; ?>
            <h1 class="article-title"><?= e($noticia['manchete']) ?></h1>
            <?php if ($noticia['resumo']): ?>
            <p class="article-lead"><?= e($noticia['resumo']) ?></p>
            <?php endif; ?>
            <div class="article-meta">
                <span class="article-views">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <?= number_format((int)$noticia['n_visitas'] + 1, 0, ',', '.') ?> visualizações
                </span>
            </div>
        </header>

        <div class="article-body fade-in-up">
            <?= $noticia['noticia'] /* HTML confiável gerado pelo admin */ ?>
        </div>

        <!-- Imagem inferior -->
        <?php if (!empty($noticia['imagem_final']) && file_exists(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final'])): ?>
        <figure class="article-img-footer">
            <img src="/uploads/noticias/<?= e($noticia['imagem_final']) ?>"
                 alt="Imagem complementar — <?= e($noticia['manchete']) ?>"
                 class="article-img-full">
        </figure>
        <?php endif; ?>

        <footer class="article-footer fade-in-up">
            <a href="/noticias" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Voltar para Notícias
            </a>
        </footer>
    </div>
</article>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
