<?php
/**
 * Transparência: Painel de Transferências — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Painel de Transferências Legais & Discricionárias';
$page_description = 'Demonstrativos públicos de transferências financeiras, comprovações bancárias e relatórios de execução orçamentária do INPP.';

// Buscar arquivos do painel de transferências registrados e habilitados no banco de dados
$db_arquivos = db_fetch_all(
    "SELECT * FROM tab_transparencia_arquivos WHERE tipo = 'painel' AND habilitado = 1 ORDER BY data_documento DESC, id DESC"
);

$pasta = UPLOAD_PATH . '/transparencia/';
$arquivos = [];

foreach ($db_arquivos as $item) {
    $arq = $item['arquivo'];
    $caminho_completo = $pasta . $arq;
    $ext = strtolower(pathinfo($arq, PATHINFO_EXTENSION));
    $tamanho_bytes = file_exists($caminho_completo) ? filesize($caminho_completo) : 0;
    
    $eh_imagem = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    $eh_excel  = in_array($ext, ['xlsx', 'xls', 'csv']);

    $data_exibicao = !empty($item['data_documento']) ? date('d/m/Y', strtotime($item['data_documento'])) : date('d/m/Y');

    $arquivos[] = [
        'id'           => $item['id'],
        'nome'         => $arq,
        'titulo'       => $item['titulo'] ?: ($eh_imagem ? 'Comprovante Visual de Transferência — ' . $data_exibicao : 'Planilha de Repasses & Transferências — ' . $data_exibicao),
        'ext'          => $ext,
        'path'         => '/uploads/transparencia/' . $arq,
        'tamanho_kb'   => round($tamanho_bytes / 1024, 1),
        'tamanho_txt'  => round($tamanho_bytes / 1024, 1) > 1024 ? round($tamanho_bytes / (1024 * 1024), 1) . ' MB' : round($tamanho_bytes / 1024, 1) . ' KB',
        'data'         => $data_exibicao,
        'timestamp'    => strtotime($item['data_documento'] ?? 'now'),
        'eh_imagem'    => $eh_imagem,
        'eh_excel'     => $eh_excel,
    ];
}

// Estatísticas do Painel
$total_documentos = count($arquivos);
$total_imagens = count(array_filter($arquivos, fn($a) => $a['eh_imagem']));
$total_planilhas = count(array_filter($arquivos, fn($a) => $a['eh_excel']));
$ultima_atualizacao = !empty($arquivos) ? $arquivos[0]['data'] : 'Em atualização';

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
            <span>Painel de Transferências</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Recursos Públicos &bull; Repasses Governamentais &bull; Dados Abertos</span>
            <h1 class="qs-hero-title">Painel de Transferências Legais &amp; Discricionárias</h1>
            <p class="qs-hero-lead">
                Registro público e contínuo das ordens bancárias, transferências de recursos públicos federais, demonstrativos do Tesouro Nacional e planilhas orçamentárias abertas para controle social.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE TRANSFERÊNCIAS -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Transferências Publicadas</span>
                <strong class="qs-fact-val"><?= $total_documentos ?> <?= $total_documentos === 1 ? 'Registro' : 'Registros' ?></strong>
                <span class="qs-fact-desc">Disponíveis no portal</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Planilhas Abertas</span>
                <strong class="qs-fact-val"><?= $total_planilhas ?> <?= $total_planilhas === 1 ? 'Relatório XLSX' : 'Relatórios XLSX' ?></strong>
                <span class="qs-fact-desc">Para auditoria e análise</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Comprovantes Oficiais</span>
                <strong class="qs-fact-val"><?= $total_imagens ?> Demonstrativos</strong>
                <span class="qs-fact-desc">Registros visuais de repasse</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Última Competência</span>
                <strong class="qs-fact-val"><?= e($ultima_atualizacao) ?></strong>
                <span class="qs-fact-desc">Atualização mais recente</span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <?php foreach (get_transparencia_subnav_items('painel') as $item): ?>
                <a href="<?= e($item['url']) ?>" class="t-subnav-link <?= $item['active'] ? 'active' : '' ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- SEÇÃO DE DIRETRIZES DO PAINEL -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Execução Orçamentária</span>
            <h2 class="qs-title">Princípios da Publicidade de Recursos Públicos</h2>
            <p class="qs-subtitle">
                O monitoramento dos repasses federais obedece ao padrão do Portal da Transparência do Governo Federal e à Lei de Responsabilidade Fiscal.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Rastreabilidade Siafi / Transferegov</h3>
                <p>
                    Identificação inequívoca do número da ordem bancária (OB), unidade orçamentária concedente, programa de trabalho e termo de parceria correspondente.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Padrão de Dados Abertos</h3>
                <p>
                    Disponibilização periódica de planilhas estruturadas em formato aberto (.xlsx) para que órgãos de controle e cidadãos possam tabular e cruzar dados livremente.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Conciliação Bancária Vinculada</h3>
                <p>
                    Confronto contínuo entre os repasses liberados pelo Poder Público e os extratos da conta corrente específica de cada projeto em execução.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DE DOCUMENTOS DO PAINEL -->
<section class="section fin-main-section" id="painel">
    <div class="container">

        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Acervo de Transferências</span>
                <h2 class="qs-title">Comprovantes &amp; Planilhas de Repasse</h2>
            </div>
            <span class="fin-count-pill"><?= $total_documentos ?> <?= $total_documentos === 1 ? 'registro catalogado' : 'registros catalogados' ?></span>
        </div>

        <?php if (empty($arquivos)): ?>
        <div class="proj-empty-box">
            <i class="ph ph-chart-line-up"></i>
            <h3>Painel de transferências em atualização</h3>
            <p>Os demonstrativos e planilhas de transferências serão publicados conforme a emissão dos relatórios periódicos dos órgãos concedentes.</p>
            <a href="/contato" class="btn btn-outline">Solicitar Informações via Ouvidoria</a>
        </div>
        <?php else: ?>

        <!-- GRID DE TRANSFERÊNCIAS -->
        <div class="fin-docs-grid">
            <?php foreach ($arquivos as $arq): ?>
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <?php if ($arq['eh_imagem']): ?>
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-image"></i> Painel Oficial
                    </span>
                    <?php elseif ($arq['eh_excel']): ?>
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-file-xls"></i> Planilha XLSX
                    </span>
                    <?php else: ?>
                    <span class="fin-doc-badge fin-badge-pdf">
                        <i class="ph ph-file-pdf"></i> Documento Oficial
                    </span>
                    <?php endif; ?>

                    <span class="fin-doc-date">
                        <i class="ph ph-calendar-blank"></i> <?= e($arq['data']) ?>
                    </span>
                </div>

                <?php if ($arq['eh_imagem']): ?>
                <div class="fin-doc-media">
                    <a href="<?= e($arq['path']) ?>" class="glightbox" data-gallery="painel-transferencias" data-title="<?= e($arq['titulo']) ?>" data-description="Instituto Nacional de Políticas Públicas — Repasses Governamentais">
                        <img src="<?= e($arq['path']) ?>" alt="<?= e($arq['titulo']) ?>" loading="lazy">
                        <div class="fin-doc-media-overlay">
                            <i class="ph ph-magnifying-glass-plus"></i> Ampliar Comprovante
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <h3 class="fin-doc-title"><?= e($arq['titulo']) ?></h3>

                <p class="fin-doc-desc">
                    <?= $arq['eh_imagem'] ? 'Registro visual oficial emitido pelo sistema de transferências do Governo Federal, comprovando os repasses da respectiva competência.' : 'Planilha analítica contendo detalhamento das transferências legais e discricionárias recebidas, datas de crédito e identificação de ordens bancárias.' ?>
                </p>

                <div class="fin-doc-footer">
                    <span class="fin-doc-size">
                        <i class="ph ph-file-arrow-down"></i> <?= e($arq['tamanho_txt']) ?>
                    </span>
                    <?php if ($arq['eh_imagem']): ?>
                    <a href="<?= e($arq['path']) ?>" class="btn btn-outline btn-sm glightbox" data-gallery="painel-transferencias" data-title="<?= e($arq['titulo']) ?>">
                        Visualizar <i class="ph ph-arrow-square-out"></i>
                    </a>
                    <?php else: ?>
                    <a href="<?= e($arq['path']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                        Baixar Planilha <i class="ph ph-arrow-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <!-- PAINEL DE TRANSPARÊNCIA E OUVIDORIA -->
        <div class="proj-transparency-box" style="margin-top:4rem;">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Transparência Ativa &amp; LAI</span>
                <h3>Demonstrações Contábeis &amp; Balanços Anuais</h3>
                <p>
                    Para consultar a escrituração contábil completa, balanços patrimoniais e pareceres técnicos de auditoria dos exercícios, acesse o módulo de finanças do INPP.
                </p>
            </div>
            <div class="proj-transparency-links">
                <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('financeiro')): ?>
                <a href="/transparencia/financeiro" class="btn btn-primary">
                    Demonstrações Financeiras
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

