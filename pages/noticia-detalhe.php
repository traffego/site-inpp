<?php
/**
 * Detalhe da Notícia — Instituto Nacional de Políticas Públicas (INPP)
 * Design System Editorial & Tipografia Nobre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(404);
    $page_title = 'Notícia não encontrada';
    ob_start();
    ?>
    <section class="news-main-section">
        <div class="container">
            <div class="proj-empty-box">
                <i class="ph ph-warning-circle"></i>
                <h2>Notícia não encontrada</h2>
                <p>O identificador da notícia informado é inválido ou não foi fornecido.</p>
                <a href="/noticias" class="btn btn-primary">Voltar para o Acervo de Notícias</a>
            </div>
        </div>
    </section>
    <?php
    $content = ob_get_clean();
    require ROOT_PATH . '/templates/layout.php';
    exit;
}

$noticia = db_fetch('SELECT * FROM tab_noticias WHERE id = ?', [$id]);

if (!$noticia) {
    http_response_code(404);
    $page_title = 'Notícia não encontrada';
    ob_start();
    ?>
    <section class="news-main-section">
        <div class="container">
            <div class="proj-empty-box">
                <i class="ph ph-newspaper"></i>
                <h2>Notícia não encontrada</h2>
                <p>A publicação solicitada não existe, foi arquivada ou está temporariamente indisponível.</p>
                <a href="/noticias" class="btn btn-primary">Ver Todas as Notícias</a>
            </div>
        </div>
    </section>
    <?php
    $content = ob_get_clean();
    require ROOT_PATH . '/templates/layout.php';
    exit;
}

// Incrementar visitas
db_update('tab_noticias', ['n_visitas' => (int)$noticia['n_visitas'] + 1], 'id = ?', [$id]);

// Notícias recentes para a Sidebar
$recentes = db_fetch_all(
    'SELECT id, manchete, data_noticia, imagem_inicio FROM tab_noticias WHERE id != ? ORDER BY data_noticia DESC, id DESC LIMIT 4',
    [$id]
);

// Notícias recomendadas para a seção inferior
$relacionadas = db_fetch_all(
    'SELECT id, manchete, resumo, noticia, data_noticia, imagem_inicio, n_visitas FROM tab_noticias WHERE id != ? ORDER BY data_noticia DESC, id DESC LIMIT 3',
    [$id]
);

$page_title       = $noticia['manchete'];
$page_description = $noticia['resumo'] ? truncate($noticia['resumo'], 160) : truncate(strip_tags($noticia['noticia']), 160);

// Links de compartilhamento
$host = $_SERVER['HTTP_HOST'] ?? 'inpp.org.br';
$current_url = 'https://' . $host . '/noticia/' . (int)$noticia['id'];
$share_title = rawurlencode($noticia['manchete']);
$share_url   = rawurlencode($current_url);

// Imagem de capa
$img_capa_path = !empty($noticia['imagem_inicio']) ? UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio'] : '';
$tem_img_capa  = !empty($noticia['imagem_inicio']) && file_exists($img_capa_path);
$img_capa_url  = $tem_img_capa ? '/uploads/noticias/' . rawurlencode($noticia['imagem_inicio']) : '';

// Imagem secundária
$img_fim_path = !empty($noticia['imagem_final']) ? UPLOAD_PATH . '/noticias/' . $noticia['imagem_final'] : '';
$tem_img_fim  = !empty($noticia['imagem_final']) && file_exists($img_fim_path);
$img_fim_url  = $tem_img_fim ? '/uploads/noticias/' . rawurlencode($noticia['imagem_final']) : '';

ob_start();
?>
<!-- TOPO & CABEÇALHO EDITORIAL DO ARTIGO -->
<section class="article-header-section">
    <div class="container">
        
        <!-- NAVEGAÇÃO SUPERIOR CONTEXTUAL -->
        <div class="article-top-nav">
            <nav class="breadcrumb" aria-label="Navegação" style="margin-bottom:0;">
                <a href="/">Início</a>
                <span aria-hidden="true">›</span>
                <a href="/noticias">Notícias</a>
                <span aria-hidden="true">›</span>
                <span><?= e(truncate($noticia['manchete'], 45)) ?></span>
            </nav>

            <a href="/noticias" class="article-back-link">
                <i class="ph ph-arrow-left"></i> Voltar ao Acervo de Notícias
            </a>
        </div>

        <div class="article-header-container">
            <span class="article-badge-press">
                <i class="ph ph-shield-check"></i> Comunicado Oficial &bull; INPP
            </span>

            <h1 class="article-main-title"><?= e($noticia['manchete']) ?></h1>

            <?php if (!empty($noticia['resumo'])): ?>
            <p class="article-lead-text"><?= e($noticia['resumo']) ?></p>
            <?php endif; ?>

            <!-- BARRA DE METADADOS EDITORIAL -->
            <div class="article-meta-bar">
                <div class="article-meta-left">
                    <span class="article-meta-point">
                        <i class="ph ph-calendar-blank"></i>
                        <?= formatar_data_extenso($noticia['data_noticia']) ?>
                    </span>
                    <span class="article-meta-point">
                        <i class="ph ph-clock"></i>
                        <?= calcular_tempo_leitura($noticia['noticia'] ?: $noticia['resumo']) ?>
                    </span>
                    <span class="article-meta-point">
                        <i class="ph ph-eye"></i>
                        <?= number_format((int)$noticia['n_visitas'] + 1, 0, ',', '.') ?> visualizações
                    </span>
                </div>

                <div class="article-meta-right">
                    <span class="article-meta-point" style="font-weight:600; color:var(--color-primary-dark);">
                        <i class="ph ph-buildings"></i> Assessoria de Comunicação &amp; Imprensa
                    </span>
                </div>
            </div>

            <!-- CAPA CINEMATOGRÁFICA -->
            <?php if ($tem_img_capa): ?>
            <figure class="article-hero-media-wrap">
                <img src="<?= e($img_capa_url) ?>" alt="<?= e($noticia['manchete']) ?>" loading="eager">
                <figcaption class="article-hero-caption">
                    <i class="ph ph-camera"></i> Cobertura Institucional &bull; Instituto Nacional de Políticas Públicas
                </figcaption>
            </figure>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- CORPO PRINCIPAL DO ARTIGO & SIDEBAR -->
<section class="article-main-section">
    <div class="container">
        
        <div class="article-layout-grid">
            
            <!-- COLUNA PRINCIPAL: TEXTO DA MATÉRIA -->
            <main class="article-main-col">
                <div class="article-prose">
                    <?= $noticia['noticia'] /* HTML seguro formatado */ ?>
                </div>

                <!-- IMAGEM COMPLEMENTAR / FINAL -->
                <?php if ($tem_img_fim): ?>
                <figure class="article-secondary-figure">
                    <img src="<?= e($img_fim_url) ?>" alt="Registro fotográfico complementar — <?= e($noticia['manchete']) ?>" loading="lazy">
                    <figcaption class="article-hero-caption">
                        <i class="ph ph-image"></i> Registro fotográfico anexo
                    </figcaption>
                </figure>
                <?php endif; ?>

                <!-- BARRA DE COMPARTILHAMENTO E AÇÕES -->
                <div class="article-share-bar">
                    <div class="article-share-title">
                        <i class="ph ph-share-network" style="font-size:1.2rem;"></i> Compartilhe esta Notícia Oficial:
                    </div>

                    <div class="article-share-buttons">
                        <a href="https://api.whatsapp.com/send?text=<?= $share_title ?>%20<?= $share_url ?>" target="_blank" rel="noopener noreferrer" class="btn-share btn-share-whatsapp" title="Compartilhar no WhatsApp">
                            <i class="ph ph-whatsapp-logo"></i> WhatsApp
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $share_url ?>" target="_blank" rel="noopener noreferrer" class="btn-share btn-share-linkedin" title="Compartilhar no LinkedIn">
                            <i class="ph ph-linkedin-logo"></i> LinkedIn
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= $share_title ?>&url=<?= $share_url ?>" target="_blank" rel="noopener noreferrer" class="btn-share btn-share-twitter" title="Compartilhar no X (Twitter)">
                            <i class="ph ph-x-logo"></i> X
                        </a>
                        <button type="button" class="btn-share btn-share-copy" onclick="navigator.clipboard.writeText('<?= $current_url ?>'); this.innerHTML = '<i class=\'ph ph-check\'></i> Copiado!'; setTimeout(() => this.innerHTML = '<i class=\'ph ph-copy\'></i> Copiar Link', 2000);">
                            <i class="ph ph-copy"></i> Copiar Link
                        </button>
                    </div>
                </div>

                <!-- BOTÃO DE VOLTAR -->
                <div style="margin-top: 2rem;">
                    <a href="/noticias" class="btn btn-outline">
                        <i class="ph ph-arrow-left"></i> Retornar a Todas as Notícias
                    </a>
                </div>
            </main>

            <!-- COLUNA LATERAL: SIDEBAR EDITORIAL -->
            <aside class="article-sidebar">
                
                <!-- CARD: SOBRE O INPP -->
                <div class="article-side-card">
                    <div class="article-side-header">
                        <i class="ph ph-bank" style="color:var(--color-primary); font-size:1.25rem;"></i>
                        <h4 class="article-side-title">Sobre o INPP</h4>
                    </div>
                    <p class="article-side-text">
                        Fundado em 1982, o Instituto Nacional de Políticas Públicas é uma organização sem fins lucrativos comprometida com a formulação, governança e execução de projetos de impacto socioeducativo em regime de mútua cooperação (MROSC).
                    </p>
                    <a href="/quem-somos" class="btn btn-outline btn-sm w-100" style="justify-content:center;">
                        Conheça Nossa História <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- CARD: ASSESSORIA & IMPRENSA -->
                <div class="article-side-card">
                    <div class="article-side-header">
                        <i class="ph ph-chat-teardrop-text" style="color:var(--color-primary); font-size:1.25rem;"></i>
                        <h4 class="article-side-title">Assessoria &amp; Ouvidoria</h4>
                    </div>
                    <p class="article-side-text">
                        Veículos de comunicação, jornalistas e cidadãos podem solicitar esclarecimentos, notas oficiais e agendamento de entrevistas com os dirigentes.
                    </p>
                    <div style="background:var(--bg-surface-hover); padding:0.75rem; border-radius:8px; margin-bottom:1rem; font-size:0.8rem; word-break:break-all;">
                        <strong>E-mail de Imprensa:</strong><br>
                        <a href="mailto:contato@inpp.org" style="color:var(--color-primary); font-weight:600;">contato@inpp.org</a>
                    </div>
                    <a href="/contato" class="btn btn-primary btn-sm w-100" style="justify-content:center;">
                        Fale Conosco
                    </a>
                </div>

                <!-- CARD: OUTRAS NOTÍCIAS RECENTES -->
                <?php if (!empty($recentes)): ?>
                <div class="article-side-card">
                    <div class="article-side-header">
                        <i class="ph ph-clock-counter-clockwise" style="color:var(--color-primary); font-size:1.25rem;"></i>
                        <h4 class="article-side-title">Recentes</h4>
                    </div>

                    <div class="article-side-news-list">
                        <?php foreach ($recentes as $r): ?>
                        <?php
                            $r_img_path = !empty($r['imagem_inicio']) ? UPLOAD_PATH . '/noticias/' . $r['imagem_inicio'] : '';
                            $r_tem_img  = !empty($r['imagem_inicio']) && file_exists($r_img_path);
                            $r_img_url  = $r_tem_img ? '/uploads/noticias/' . rawurlencode($r['imagem_inicio']) : '/assets/img/hero_inpp_intergeracional.jpg';
                        ?>
                        <article class="article-side-news-item">
                            <img src="<?= e($r_img_url) ?>" alt="<?= e($r['manchete']) ?>" class="article-side-thumb" loading="lazy">
                            <div class="article-side-item-content">
                                <time class="article-side-item-date" datetime="<?= e($r['data_noticia']) ?>">
                                    <?= format_date($r['data_noticia']) ?>
                                </time>
                                <a href="/noticia/<?= (int)$r['id'] ?>" class="article-side-item-title">
                                    <?= e(truncate($r['manchete'], 65)) ?>
                                </a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </aside>

        </div>

        <!-- SEÇÃO: MATÉRIAS RECOMENDADAS / RELACIONADAS -->
        <?php if (!empty($relacionadas)): ?>
        <section class="article-related-section">
            <div class="news-section-header">
                <div>
                    <span class="qs-kicker">Continue Navegando</span>
                    <h3 class="qs-title" style="margin-bottom:0;">Outras Publicações e Ações</h3>
                </div>
                <a href="/noticias" class="btn btn-outline btn-sm">
                    Ver Todas as Notícias <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            <div class="news-grid-editorial">
                <?php foreach ($relacionadas as $rel): ?>
                <?php
                    $rel_img_path = !empty($rel['imagem_inicio']) ? UPLOAD_PATH . '/noticias/' . $rel['imagem_inicio'] : '';
                    $rel_tem_img  = !empty($rel['imagem_inicio']) && file_exists($rel_img_path);
                    $rel_img_url  = $rel_tem_img ? '/uploads/noticias/' . rawurlencode($rel['imagem_inicio']) : '/assets/img/hero_inpp_intergeracional.jpg';
                ?>
                <article class="news-card-v2">
                    <div class="news-card-media-wrap">
                        <span class="news-card-badge-top">Institucional</span>
                        <a href="/noticia/<?= (int)$rel['id'] ?>" tabindex="-1" aria-hidden="true">
                            <img src="<?= e($rel_img_url) ?>" alt="<?= e($rel['manchete']) ?>" loading="lazy">
                        </a>
                    </div>

                    <div class="news-card-body-v2">
                        <div class="news-card-meta">
                            <span class="news-meta-point">
                                <i class="ph ph-calendar-blank"></i> <?= format_date($rel['data_noticia']) ?>
                            </span>
                            <span>&bull;</span>
                            <span class="news-meta-point">
                                <i class="ph ph-clock"></i> <?= calcular_tempo_leitura($rel['noticia'] ?: $rel['resumo']) ?>
                            </span>
                        </div>

                        <h3 class="news-card-title-v2">
                            <a href="/noticia/<?= (int)$rel['id'] ?>"><?= e($rel['manchete']) ?></a>
                        </h3>

                        <p class="news-card-excerpt">
                            <?= e(truncate($rel['resumo'] ?: strip_tags($rel['noticia']), 120)) ?>
                        </p>

                        <div class="news-card-footer-v2">
                            <a href="/noticia/<?= (int)$rel['id'] ?>" class="news-read-btn">
                                Ler Artigo <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
