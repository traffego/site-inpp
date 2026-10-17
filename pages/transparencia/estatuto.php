<?php
/**
 * Transparência: Estatuto Social — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Estatuto Social Consolidado';
$page_description = 'Estatuto Social do Instituto Nacional de Políticas Públicas (INPP) — documento constituinte registrado em cartório que rege a entidade.';

$doc_est = db_fetch("SELECT * FROM tab_transparencia_arquivos WHERE tipo = 'estatuto' AND habilitado = 1 LIMIT 1");
$tem_pdf = false;
$tamanho_txt = '';
if ($doc_est) {
    $caminho_pdf = UPLOAD_PATH . '/transparencia/' . $doc_est['arquivo'];
    if (file_exists($caminho_pdf)) {
        $tem_pdf = true;
        $tamanho_mb = round(filesize($caminho_pdf) / (1024 * 1024), 1);
        $tamanho_txt = $tamanho_mb > 0 ? $tamanho_mb . ' MB' : 'PDF';
    }
}

ob_start();
?>
<!-- HERO INSTITUCIONAL -->
<section class="qs-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Estatuto Social</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Ato Constitutivo &bull; Marco Legal &bull; Terceiro Setor</span>
            <h1 class="qs-hero-title">Estatuto Social Consolidado</h1>
            <p class="qs-hero-lead">
                Instrumento jurídico supremo que rege a constituição, as finalidades de interesse coletivo, a governança institucional, os direitos e deveres associativos e o regime patrimonial do INPP.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DO ESTATUTO -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Natureza Jurídica</span>
                <strong class="qs-fact-val">Associação Privada (OSC)</strong>
                <span class="qs-fact-desc">Sem fins econômicos ou lucrativos</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Trajetória Histórica</span>
                <strong class="qs-fact-val">Desde 1982 &bull; 44 Anos</strong>
                <span class="qs-fact-desc">Registro cartorial consolidado</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Âmbito de Atuação</span>
                <strong class="qs-fact-val">Nacional &bull; Brasil</strong>
                <span class="qs-fact-desc">Sede e projetos em todo o país</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Documento Oficial</span>
                <strong class="qs-fact-val"><?= $tem_pdf ? 'Arquivo Oficial (' . $tamanho_txt . ')' : 'Em Atualização' ?></strong>
                <span class="qs-fact-desc"><?= $tem_pdf ? 'Disponível para download público' : 'Consolidado em cartório' ?></span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA DINÂMICA -->
<?php $subnav_items = get_transparencia_subnav_items('estatuto'); ?>
<?php if (!empty($subnav_items)): ?>
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <?php foreach ($subnav_items as $item): ?>
            <a href="<?= e($item['url']) ?>" class="t-subnav-link <?= $item['active'] ? 'active' : '' ?>">
                <?= e($item['label']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- SEÇÃO DE DIRETRIZES FUNDAMENTAIS -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Cláusulas Pétreas</span>
            <h2 class="qs-title">Pilares do Regime Jurídico e Patrimonial</h2>
            <p class="qs-subtitle">
                O Estatuto do INPP estabelece salvaguardas permanentes para assegurar a perenidade social e a integridade de sua missão.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Finalidade de Interesse Coletivo</h3>
                <p>
                    Dedicação exclusiva a iniciativas de utilidade pública, formulação e execução de políticas sociais, educação, capacitação profissional, pesquisa e cidadania em favor da população.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Vedação à Distribuição de Sobras</h3>
                <p>
                    Proibição estatutária expressa de partilha ou distribuição de dividendos, lucros, bonificações ou parcelas do patrimônio sob qualquer título entre associados ou dirigentes.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Destinação Pública do Patrimônio</h3>
                <p>
                    Na hipótese eventual de dissolução da entidade, todo o acervo patrimonial líquido remanescente será compulsoriamente revertido a outra instituição sem fins lucrativos congênere qualificada.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DO DOCUMENTO OFICIAL E CAPÍTULOS -->
<section class="section fin-main-section" id="estatuto">
    <div class="container">

        <!-- DESTAQUE OFICIAL: ESTATUTO SOCIAL CONSOLIDADO -->
        <?php if ($tem_pdf): ?>
        <article class="fin-doc-card" style="margin-bottom: 3.5rem; border-left: 4px solid var(--color-primary); background: #ffffff;">
            <div class="fin-doc-top">
                <span class="fin-doc-badge fin-badge-pdf">
                    <i class="ph ph-file-pdf"></i> Instrumento Constitutivo Notarial
                </span>
                <span class="fin-doc-date">
                    <i class="ph ph-seal-check"></i> Registrado em Cartório de Registro Civil de Pessoas Jurídicas
                </span>
            </div>

            <h3 class="fin-doc-title" style="font-size:1.4rem; margin-bottom:0.5rem;">
                Estatuto Social Consolidado do Instituto Nacional de Políticas Públicas
            </h3>

            <p class="fin-doc-desc" style="margin-bottom:1.5rem; max-width:850px;">
                Instrumento original e consolidado contendo a totalidade dos artigos, competências institucionais, regime disciplinar, poderes de representação e salvaguardas de conformidade jurídica do INPP, certificado pelo Oficial de Registro de Pessoas Jurídicas.
            </p>

            <div class="fin-doc-footer">
                <span class="fin-doc-size">
                    <i class="ph ph-file-arrow-down"></i> <?= $tamanho_txt ?> &bull; Documento Completo Digitalizado
                </span>
                <a href="/uploads/transparencia/estatuto.pdf" class="btn btn-primary" target="_blank" rel="noopener">
                    Baixar Estatuto Social (PDF) <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </article>
        <?php endif; ?>

        <!-- ESTRUTURA DOS CAPÍTULOS ESTATUTÁRIOS -->
        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Síntese Estrutural</span>
                <h2 class="qs-title">Capítulos e Matérias do Estatuto</h2>
            </div>
            <span class="fin-count-pill">Norma Fundamental &bull; Vigente</span>
        </div>

        <div class="fin-docs-grid">
            <!-- CAPÍTULO I -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-bookmarks"></i> Capítulo I
                    </span>
                    <span class="fin-doc-date">Artigos 1º a 4º</span>
                </div>
                <h3 class="fin-doc-title">Denominação, Sede e Duração</h3>
                <p class="fin-doc-desc">
                    Estabelece a constituição do INPP como pessoa jurídica de direito privado sem fins lucrativos, fixação de sua sede administrativa e filial, autonomia orçamentária e prazo de duração indeterminado.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Autonomia Institucional</span>
                    <?php if ($tem_pdf): ?>
                    <a href="/uploads/transparencia/<?= e($doc_est['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Ver no PDF <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>

            <!-- CAPÍTULO II -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-bookmarks"></i> Capítulo II
                    </span>
                    <span class="fin-doc-date">Artigos 5º a 8º</span>
                </div>
                <h3 class="fin-doc-title">Finalidades &amp; Objetivos Sociais</h3>
                <p class="fin-doc-desc">
                    Elenca as áreas de atuação do instituto: execução e apoio a políticas públicas de educação, capacitação profissional, proteção social, esportes formativos, cidadania, direitos humanos e preservação ambiental.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Missão Social</span>
                    <?php if ($tem_pdf): ?>
                    <a href="/uploads/transparencia/<?= e($doc_est['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Ver no PDF <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>

            <!-- CAPÍTULO III -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-bookmarks"></i> Capítulo III
                    </span>
                    <span class="fin-doc-date">Artigos 9º a 16º</span>
                </div>
                <h3 class="fin-doc-title">Do Quadro Social &amp; Assembleia</h3>
                <p class="fin-doc-desc">
                    Regula as categorias de associados, critérios de admissão, direitos políticos e deliberativos, deveres regimentais e o rito de convocação e funcionamento das Assembleias Gerais Ordinárias e Extraordinárias.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Gestão Democrática</span>
                    <?php if ($tem_pdf): ?>
                    <a href="/uploads/transparencia/<?= e($doc_est['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Ver no PDF <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>

            <!-- CAPÍTULO IV -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-bookmarks"></i> Capítulo IV
                    </span>
                    <span class="fin-doc-date">Artigos 17º a 28º</span>
                </div>
                <h3 class="fin-doc-title">Governança, Fiscalização e Contas</h3>
                <p class="fin-doc-desc">
                    Composição e mandatos da Diretoria Executiva e do Conselho Fiscal, competências privativas, rito de escrituração contábil auditável e regras para celebração de parcerias com o Poder Público.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Fiscalização Contábil</span>
                    <?php if ($tem_pdf): ?>
                    <a href="/uploads/transparencia/<?= e($doc_est['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Ver no PDF <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>
        </div>

        <!-- PAINEL DE TRANSPARÊNCIA E OUVIDORIA -->
        <div class="proj-transparency-box" style="margin-top:4rem;">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Transparência Ativa &amp; LAI</span>
                <h3>Dúvidas sobre o Estatuto ou Solicitação de Certidões</h3>
                <p>
                    Cópias autenticadas, certidões de breve relatório ou esclarecimentos formais sobre o Estatuto Social podem ser requisitados diretamente junto à nossa Ouvidoria Geral.
                </p>
            </div>
            <div class="proj-transparency-links">
                <a href="/transparencia/dirigentes" class="btn btn-primary">
                    Corpo Diretivo
                </a>
                <a href="/transparencia/declaracao" class="btn btn-outline">
                    Declaração Institucional
                </a>
                <a href="/contato" class="btn btn-outline">
                    Fale com a Ouvidoria
                </a>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';

