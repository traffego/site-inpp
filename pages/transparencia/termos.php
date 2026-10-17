<?php
/**
 * Transparência: Termos de Fomento & Colaboração — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Termos de Fomento & Colaboração';
$page_description = 'Termos de fomento, colaboração e acordos de cooperação celebrados pelo Instituto Nacional de Políticas Públicas (INPP) sob o regime do MROSC.';

$termos = db_fetch_all('SELECT * FROM tab_documentos_termo_colaboracao WHERE habilitado = 1 ORDER BY data_documento DESC');
$total_termos = count($termos);
$ultimo_termo = !empty($termos) ? format_date($termos[0]['data_documento']) : 'Em atualização';

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
            <span>Termos de Colaboração</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Parcerias Institucionais &bull; Fomento &bull; MROSC</span>
            <h1 class="qs-hero-title">Termos de Fomento, Colaboração &amp; Parcerias</h1>
            <p class="qs-hero-lead">
                Relação oficial de termos de fomento, acordos de cooperação e instrumentos congêneres celebrados pelo INPP com ministérios, secretarias estaduais e prefeituras municipais em prol do desenvolvimento social.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE TERMOS -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Termos Publicados</span>
                <strong class="qs-fact-val"><?= $total_termos ?> <?= $total_termos === 1 ? 'Instrumento' : 'Instrumentos' ?></strong>
                <span class="qs-fact-desc">Disponíveis para consulta</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Marco Regulatório</span>
                <strong class="qs-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="qs-fact-desc">Regime jurídico de parcerias</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Esfera dos Parceiros</span>
                <strong class="qs-fact-val">União, Estados &amp; Municípios</strong>
                <span class="qs-fact-desc">Órgãos concedentes e sociedade</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Último Instrumento</span>
                <strong class="qs-fact-val"><?= e($ultimo_termo) ?></strong>
                <span class="qs-fact-desc">Competência mais recente</span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <?php foreach (get_transparencia_subnav_items('termos') as $item): ?>
                <a href="<?= e($item['url']) ?>" class="t-subnav-link <?= $item['active'] ? 'active' : '' ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- SEÇÃO DE DIRETRIZES DE PARCERIAS -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Relações Institucionais</span>
            <h2 class="qs-title">Princípios da Formalização de Parcerias</h2>
            <p class="qs-subtitle">
                A formalização e condução de termos com o Poder Público atende a elevados critérios de planejamento, controle de metas e rastreabilidade orçamentária.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Planos de Trabalho Aprovados</h3>
                <p>
                    Cada termo é celebrado com base em plano de trabalho minucioso contendo justificativa de interesse público, objetivos pactuados, metas mensuráveis e cronograma físico-financeiro de desembolso.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Contas Bancárias Específicas</h3>
                <p>
                    Os recursos repassados operam com exclusividade em conta corrente bancária vinculada, aberta em instituição financeira oficial, com movimentação exclusivamente eletrônica rastreável.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Monitoramento &amp; Controle Social</h3>
                <p>
                    Submissão obrigatória a comissões permanentes de monitoramento e avaliação do poder público e livre disponibilização de todos os instrumentos para auditoria cidadã.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DE ACERVO DE TERMOS -->
<section class="section fin-main-section" id="termos">
    <div class="container">

        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Instrumentos Jurídicos</span>
                <h2 class="qs-title">Acervo de Termos &amp; Acordos de Cooperação</h2>
            </div>
            <span class="fin-count-pill"><?= $total_termos ?> <?= $total_termos === 1 ? 'instrumento catalogado' : 'instrumentos catalogados' ?></span>
        </div>

        <?php if (empty($termos)): ?>
        <div class="proj-empty-box">
            <i class="ph ph-handshake"></i>
            <h3>Nenhum termo de fomento publicado no momento</h3>
            <p>Os termos de colaboração, acordos e instrumentos de fomento celebrados serão listados publicamente nesta seção assim que formalizados.</p>
            <a href="/contato" class="btn btn-outline">Consultar Parcerias via Ouvidoria</a>
        </div>
        <?php else: ?>

        <!-- GRID DE TERMOS -->
        <div class="fin-docs-grid">
            <?php foreach ($termos as $t): ?>
            <?php 
                $ext = strtolower(pathinfo($t['arquivo'], PATHINFO_EXTENSION));
                $caminho_arq = UPLOAD_PATH . '/transparencia/' . $t['arquivo'];
                $tamanho_kb = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024, 1) : 0;
                $tamanho_txt = $tamanho_kb > 1024 ? round($tamanho_kb / 1024, 1) . ' MB' : ($tamanho_kb > 0 ? $tamanho_kb . ' KB' : strtoupper($ext ?: 'PDF'));
                
                $is_excel = in_array($ext, ['xlsx', 'xls', 'csv']);
                $badge_type_class = $is_excel ? 'fin-badge-sheet' : 'fin-badge-pdf';
                $badge_label = $is_excel ? 'Planilha XLSX' : 'Documento Oficial PDF';
            ?>
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge <?= $badge_type_class ?>">
                        <i class="ph <?= $is_excel ? 'ph-file-xls' : 'ph-file-pdf' ?>"></i> <?= $badge_label ?>
                    </span>
                    <span class="fin-doc-date">
                        <i class="ph ph-calendar-blank"></i> <?= format_date($t['data_documento']) ?>
                    </span>
                </div>

                <h3 class="fin-doc-title"><?= e($t['titulo']) ?></h3>

                <p class="fin-doc-desc">
                    <?= !empty($t['resumo']) ? e($t['resumo']) : 'Instrumento formal de parceria, termo de fomento ou colaboração celebrado pelo INPP em conformidade com o MROSC.' ?>
                </p>

                <div class="fin-doc-footer">
                    <span class="fin-doc-size">
                        <i class="ph ph-file-arrow-down"></i> <?= $tamanho_txt ?>
                    </span>
                    <a href="/uploads/transparencia/<?= e($t['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                        Baixar Documento Oficial <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <!-- PAINEL DE PROJETOS E OUVIDORIA -->
        <div class="proj-transparency-box" style="margin-top:4rem;">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Transparência Ativa &amp; LAI</span>
                <h3>Execução Física dos Projetos Vinculados</h3>
                <p>
                    Para acompanhar os resultados concretos, metas executadas e o impacto dos projetos apoiados por esses termos na vida das comunidades atendidas, consulte nosso portfólio completo de projetos.
                </p>
            </div>
            <div class="proj-transparency-links">
                <?php if (is_transparencia_habilitada('projetos')): ?>
                <a href="/projetos" class="btn btn-outline">
                    Portfólio de Projetos
                </a>
                <?php endif; ?>
                <?php if (is_transparencia_habilitada('painel')): ?>
                <a href="/transparencia/painel" class="btn btn-outline">
                    Painel de Transferências
                </a>
                <?php endif; ?>
                <a href="/contato" class="btn btn-primary">
                    Fale com a Ouvidoria
                </a>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';

