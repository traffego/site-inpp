<?php
/**
 * Transparência: Financeiro — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Demonstrações Financeiras & Contábeis';
$page_description = 'Demonstrativos contábeis, balanços patrimoniais e prestações de contas oficiais do Instituto Nacional de Políticas Públicas (INPP).';

$docs = db_fetch_all('SELECT * FROM tab_documentos_financeiro WHERE habilitado = 1 ORDER BY data_documento DESC');
$total_docs = count($docs);
$ultimo_doc = !empty($docs) ? format_date($docs[0]['data_documento']) : 'Em atualização';

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
            <span>Financeiro</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Controle Social &bull; Prestação de Contas &bull; MROSC</span>
            <h1 class="qs-hero-title">Demonstrações Contábeis &amp; Relatórios Financeiros</h1>
            <p class="qs-hero-lead">
                Balanços patrimoniais, demonstrativos de repasses de recursos públicos, prestações de contas oficiais e relatórios de execução financeira disponibilizados para consulta pública e controle social.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE TRANSPARÊNCIA FINANCEIRA -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Documentos Publicados</span>
                <strong class="qs-fact-val"><?= $total_docs ?> <?= $total_docs === 1 ? 'Relatório' : 'Relatórios' ?></strong>
                <span class="qs-fact-desc">Disponíveis para download</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Regime Regulatório</span>
                <strong class="qs-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="qs-fact-desc">Contas vinculadas por projeto</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Padrão Contábil</span>
                <strong class="qs-fact-val">ITG 2002 / CFC</strong>
                <span class="qs-fact-desc">Normas para o terceiro setor</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Última Atualização</span>
                <strong class="qs-fact-val"><?= e($ultimo_doc) ?></strong>
                <span class="qs-fact-desc">Competência mais recente</span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA DINÂMICA -->
<?php $subnav_items = get_transparencia_subnav_items('financeiro'); ?>
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


<!-- SEÇÃO DE DIRETRIZES DE GESTÃO FINANCEIRA -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Gestão &amp; Integridade Fiscal</span>
            <h2 class="qs-title">Princípios da Gestão Contábil do INPP</h2>
            <p class="qs-subtitle">
                A administração dos recursos públicos e privados sob responsabilidade do instituto segue rigorosos padrões de conformidade contábil, fiscalização e segregação patrimonial.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Contas Bancárias Exclusivas</h3>
                <p>
                    Em estrita obediência à Lei nº 13.019/2014, cada termo de fomento ou parceria opera por meio de conta corrente bancária específica e exclusiva, vedada qualquer comistão de recursos.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Escrituração Técnica Regular</h3>
                <p>
                    Elaboração de balanços patrimoniais, DREs e conciliações contábeis auditáveis em conformidade com as normas do Conselho Federal de Contabilidade (ITG 2002 / CFC).
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Fiscalização e Prestação Pública</h3>
                <p>
                    Todos os demonstrativos são submetidos à análise dos órgãos concedentes, tribunais de contas competentes e disponibilizados para livre fiscalização de qualquer cidadão.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DE RELATÓRIOS E BALANÇOS -->
<section class="section fin-main-section" id="financeiro">
    <div class="container">

        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Acervo de Documentos Oficiais</span>
                <h2 class="qs-title">Demonstrativos &amp; Prestações de Contas</h2>
            </div>
            <span class="fin-count-pill"><?= $total_docs ?> <?= $total_docs === 1 ? 'documento catalogado' : 'documentos catalogados' ?></span>
        </div>

        <?php if (empty($docs)): ?>
        <div class="proj-empty-box">
            <i class="ph ph-folder-dashed"></i>
            <h3>Nenhum demonstrativo publicado no momento</h3>
            <p>Os demonstrativos contábeis e relatórios financeiros são atualizados e publicados conforme a conclusão das prestações de contas de cada exercício.</p>
            <a href="/contato" class="btn btn-outline">Solicitar Informações via Ouvidoria</a>
        </div>
        <?php else: ?>

        <!-- GRID DE DOCUMENTOS FINANCEIROS -->
        <div class="fin-docs-grid">
            <?php foreach ($docs as $doc): ?>
            <?php 
                $ext = strtolower(pathinfo($doc['arquivo'], PATHINFO_EXTENSION));
                $caminho_arq = UPLOAD_PATH . '/transparencia/' . $doc['arquivo'];
                $tamanho_kb = file_exists($caminho_arq) ? round(filesize($caminho_arq) / 1024, 1) : 0;
                $tamanho_txt = $tamanho_kb > 1024 ? round($tamanho_kb / 1024, 1) . ' MB' : ($tamanho_kb > 0 ? $tamanho_kb . ' KB' : strtoupper($ext ?: 'DOC'));
                
                $is_excel = in_array($ext, ['xlsx', 'xls', 'csv']);
                $badge_type_class = $is_excel ? 'fin-badge-sheet' : 'fin-badge-pdf';
                $badge_label = $is_excel ? 'Planilha XLSX' : 'Documento PDF';
            ?>
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge <?= $badge_type_class ?>">
                        <i class="ph <?= $is_excel ? 'ph-file-xls' : 'ph-file-pdf' ?>"></i> <?= $badge_label ?>
                    </span>
                    <span class="fin-doc-date">
                        <i class="ph ph-calendar-blank"></i> <?= format_date($doc['data_documento']) ?>
                    </span>
                </div>

                <h3 class="fin-doc-title"><?= e($doc['titulo']) ?></h3>

                <p class="fin-doc-desc">
                    <?= !empty($doc['resumo']) ? e($doc['resumo']) : 'Demonstrativo oficial de repasses de recursos públicos, despesas vinculadas e prestação de contas do INPP.' ?>
                </p>

                <div class="fin-doc-footer">
                    <span class="fin-doc-size">
                        <i class="ph ph-file-arrow-down"></i> <?= $tamanho_txt ?>
                    </span>
                    <a href="/uploads/transparencia/<?= e($doc['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                        Baixar Arquivo Oficial <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <!-- PAINEL DE TRANSPARÊNCIA E OUVIDORIA -->
        <div class="proj-transparency-box" style="margin-top:4rem;">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Transparência Ativa &amp; LAI</span>
                <h3>Acesso a Prestações de Contas Complementares</h3>
                <p>
                    Com fundamento na Lei de Acesso à Informação (Lei Federal nº 12.527/2011), qualquer interessado pode solicitar esclarecimentos ou certidões de prestação de contas de exercícios anteriores diretamente através de nossa Ouvidoria.
                </p>
            </div>
            <div class="proj-transparency-links">
                <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('painel')): ?>
                <a href="/transparencia/painel" class="btn btn-primary">
                    Painel de Transferências
                </a>
                <?php endif; ?>
                <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('termos')): ?>
                <a href="/transparencia/termos" class="btn btn-outline">
                    Termos de Fomento
                </a>
                <?php endif; ?>
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
