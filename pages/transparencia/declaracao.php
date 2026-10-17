<?php
/**
 * Transparência: Declaração — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Declaração de Transparência';
$page_description = 'Declaração pública de transparência, compromisso ético e conformidade legal do Instituto Nacional de Políticas Públicas (INPP).';

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
            <span>Declaração</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Governança &bull; Ética &bull; MROSC &bull; Controle Social</span>
            <h1 class="qs-hero-title">Declaração de Transparência Institucional</h1>
            <p class="qs-hero-lead">
                O Instituto Nacional de Políticas Públicas (INPP) assume publicamente o compromisso ético e legal com a integridade, a publicidade de seus atos administrativos e o livre controle social na gestão de recursos em prol do desenvolvimento humano.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE TRANSPARÊNCIA -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Marco Regulatório</span>
                <strong class="qs-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="qs-fact-desc">Regime jurídico de parcerias</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Acesso à Informação</span>
                <strong class="qs-fact-val">Lei Federal nº 12.527/11</strong>
                <span class="qs-fact-desc">Publicidade ativa e irrestrita</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Integridade Administrativa</span>
                <strong class="qs-fact-val">100% dos Atos Auditáveis</strong>
                <span class="qs-fact-desc">Submissão aos órgãos de controle</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Trajetória Institucional</span>
                <strong class="qs-fact-val">Desde 1982 &bull; 44 Anos</strong>
                <span class="qs-fact-desc">Compromisso com o interesse público</span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA DINÂMICA -->
<?php $subnav_items = get_transparencia_subnav_items('declaracao'); ?>
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

<!-- SEÇÃO DE DIRETRIZES ÉTICAS E DE GOVERNANÇA -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Compromisso Público &amp; Governança</span>
            <h2 class="qs-title">Princípios da Transparência Ativa do INPP</h2>
            <p class="qs-subtitle">
                Nossa atuação fundamenta-se na estrita obediência aos preceitos constitucionais e nas melhores práticas de conformidade do Terceiro Setor.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Legalidade &amp; Impessoalidade</h3>
                <p>
                    Todas as decisões administrativas, compras e contratações do INPP seguem rigorosamente os parâmetros estabelecidos na legislação brasileira, vedada a concessão de privilégios ou preferências de qualquer natureza.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Prestação de Contas Rigorosa</h3>
                <p>
                    Apresentação contínua de relatórios de cumprimento de metas, balanços contábeis auditáveis e conciliações bancárias aos órgãos parceiros, tribunais de contas e à sociedade brasileira.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Publicidade &amp; Acesso Universal</h3>
                <p>
                    Disponibilização integral de estatutos, atas diretivas, termos de parceria, regulamentos de contratação e demonstrativos de despesas para livre fiscalização por qualquer cidadão.
                </p>
            </div>
        </div>
    </div>
</section>

<?php
$modulos = [
    'estatuto'    => ['titulo' => 'Estatuto Social', 'tag' => 'Marco Legal', 'meta' => 'Consolidado', 'desc' => 'Documento constitutivo fundamental que estabelece as finalidades sociais, estrutura organizacional, direitos associativos e diretrizes do INPP.', 'icon' => 'ph-file-text', 'footer_icon' => 'ph-shield-check', 'footer_text' => 'Registro Cartorial', 'btn' => 'Consultar Estatuto', 'url' => '/transparencia/estatuto'],
    'dirigentes'  => ['titulo' => 'Corpo Diretivo', 'tag' => 'Governança', 'meta' => 'Mandato Vigente', 'desc' => 'Identificação formal dos membros da Diretoria Executiva, Conselho Fiscal e ata oficial de eleição e posse dos representantes legais.', 'icon' => 'ph-users-three', 'footer_icon' => 'ph-identification-card', 'footer_text' => 'Representação Legal', 'btn' => 'Ver Dirigentes', 'url' => '/transparencia/dirigentes'],
    'financeiro'  => ['titulo' => 'Demonstrações Financeiras', 'tag' => 'Contabilidade', 'meta' => 'ITG 2002 / CFC', 'desc' => 'Balanços patrimoniais, demonstrações de resultados, relatórios de execução contábil e prestações de contas de convênios e termos.', 'icon' => 'ph-coins', 'footer_icon' => 'ph-file-arrow-down', 'footer_text' => 'Balanços e DREs', 'btn' => 'Ver Financeiro', 'url' => '/transparencia/financeiro'],
    'regulamento' => ['titulo' => 'Regulamento de Compras', 'tag' => 'Contratações', 'meta' => 'Art. 42 Lei 13.019', 'desc' => 'Normas e critérios objetivos para cotação prévia de preços, aquisição de bens, contratação de serviços e seleção de fornecedores.', 'icon' => 'ph-shopping-bag', 'footer_icon' => 'ph-scales', 'footer_text' => 'Cotação Prévia', 'btn' => 'Ver Regulamento', 'url' => '/transparencia/regulamento'],
    'termos'      => ['titulo' => 'Termos de Fomento', 'tag' => 'Parcerias', 'meta' => 'Poder Público', 'desc' => 'Relação de termos de fomento, termos de colaboração e acordos celebrados com ministérios, secretarias e entidades públicas parceiras.', 'icon' => 'ph-handshake', 'footer_icon' => 'ph-files', 'footer_text' => 'Instrumentos Jurídicos', 'btn' => 'Ver Termos', 'url' => '/transparencia/termos'],
    'painel'      => ['titulo' => 'Painel de Transferências', 'tag' => 'Orçamento', 'meta' => 'Dados Abertos', 'desc' => 'Extratos e relatórios de transferências legais e discricionárias do governo federal, acompanhados de planilhas públicas para download.', 'icon' => 'ph-chart-line-up', 'footer_icon' => 'ph-microsoft-excel-logo', 'footer_text' => 'Planilhas XLSX', 'btn' => 'Acessar Painel', 'url' => '/transparencia/painel'],
];
$modulos_ativos = array_filter($modulos, fn($m, $k) => is_transparencia_habilitada($k), ARRAY_FILTER_USE_BOTH);
$total_modulos = count($modulos_ativos);
?>

<?php if ($total_modulos > 0): ?>
<!-- SEÇÃO DE MÓDULOS DE TRANSPARÊNCIA -->
<section class="section fin-main-section" id="declaracao-modulos">
    <div class="container">
        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Estrutura de Controle Social</span>
                <h2 class="qs-title">Módulos Oficiais de Transparência</h2>
            </div>
            <span class="fin-count-pill"><?= $total_modulos ?> <?= $total_modulos === 1 ? 'área ativa de consulta' : 'áreas ativas de consulta' ?></span>
        </div>

        <div class="fin-docs-grid">
            <?php foreach ($modulos_ativos as $key => $mod): ?>
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph <?= e($mod['icon']) ?>"></i> <?= e($mod['tag']) ?>
                    </span>
                    <span class="fin-doc-date"><?= e($mod['meta']) ?></span>
                </div>
                <h3 class="fin-doc-title"><?= e($mod['titulo']) ?></h3>
                <p class="fin-doc-desc"><?= e($mod['desc']) ?></p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph <?= e($mod['footer_icon']) ?>"></i> <?= e($mod['footer_text']) ?></span>
                    <a href="<?= e($mod['url']) ?>" class="btn btn-outline btn-sm">
                        <?= e($mod['btn']) ?> <i class="ph ph-arrow-right"></i>
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
                <h3>Canal de Acesso à Informação e Ouvidoria</h3>
                <p>
                    O INPP disponibiliza canais diretos para que qualquer cidadão solicite esclarecimentos, informações complementares ou cópias de processos administrativos, em estrito atendimento à Lei Federal nº 12.527/2011.
                </p>
            </div>
            <div class="proj-transparency-links">
                <?php if (is_transparencia_habilitada('financeiro')): ?>
                <a href="/transparencia/financeiro" class="btn btn-outline">
                    Demonstrações Financeiras
                </a>
                <?php endif; ?>
                <?php if (is_transparencia_habilitada('termos')): ?>
                <a href="/transparencia/termos" class="btn btn-outline">
                    Termos de Fomento
                </a>
                <?php endif; ?>
                <?php if (is_transparencia_habilitada('projetos')): ?>
                <a href="/projetos" class="btn btn-outline">
                    Portfólio de Projetos
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

