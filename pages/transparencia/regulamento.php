<?php
/**
 * Transparência: Regulamento de Compras — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Regulamento de Compras & Contratações';
$page_description = 'Regulamento próprio de compras, cotações e contratações do Instituto Nacional de Políticas Públicas (INPP) conforme a Lei Federal nº 13.019/2014.';

$doc_reg = db_fetch("SELECT * FROM tab_transparencia_arquivos WHERE tipo = 'regulamento' AND habilitado = 1 LIMIT 1");
$tem_pdf = false;
$tamanho_txt = '';
if ($doc_reg) {
    $caminho_pdf = UPLOAD_PATH . '/transparencia/' . $doc_reg['arquivo'];
    if (file_exists($caminho_pdf)) {
        $tem_pdf = true;
        $tamanho_kb = round(filesize($caminho_pdf) / 1024, 1);
        $tamanho_txt = $tamanho_kb > 1024 ? round($tamanho_kb / 1024, 1) . ' MB' : ($tamanho_kb > 0 ? $tamanho_kb . ' KB' : 'PDF');
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
            <span>Regulamento de Compras</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Contratações Públicas &bull; MROSC &bull; Cotação Prévia</span>
            <h1 class="qs-hero-title">Regulamento Próprio de Compras &amp; Contratações</h1>
            <p class="qs-hero-lead">
                Diretrizes normativas, procedimentos de cotação prévia de preços e critérios objetivos para a aquisição de bens, contratação de serviços e locações com recursos de parcerias sob o regime da Lei nº 13.019/2014.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE COMPRAS -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Base Normativa</span>
                <strong class="qs-fact-val">Art. 42 da Lei 13.019/14</strong>
                <span class="qs-fact-desc">Regulamento próprio da OSC</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Procedimento Obrigatório</span>
                <strong class="qs-fact-val">Cotação Prévia de Preços</strong>
                <span class="qs-fact-desc">Mínimo de 3 propostas válidas</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Critérios de Julgamento</span>
                <strong class="qs-fact-val">Menor Preço &amp; Técnica</strong>
                <span class="qs-fact-desc">Impessoalidade e eficiência</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Documento Oficial</span>
                <strong class="qs-fact-val"><?= $tem_pdf ? 'Arquivo Oficial (' . $tamanho_txt . ')' : 'Em Atualização' ?></strong>
                <span class="qs-fact-desc"><?= $tem_pdf ? 'Disponível para download público' : 'Regulamento aprovado' ?></span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA DINÂMICA -->
<?php $subnav_items = get_transparencia_subnav_items('regulamento'); ?>
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


<!-- SEÇÃO DE DIRETRIZES DE CONTRATAÇÃO -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Integridade em Compras</span>
            <h2 class="qs-title">Princípios Norteadores das Aquisições</h2>
            <p class="qs-subtitle">
                O INPP conduz seus processos de contratação com base na transparência ativa, no melhor aproveitamento dos recursos e na livre concorrência.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Pesquisa Prévia Mercadológica</h3>
                <p>
                    Obrigatoriedade de consulta a fornecedores idôneos com no mínimo 3 orçamentos independentes ou comparação com sistemas oficiais de preços públicos (painéis de compras federais).
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Impessoalidade &amp; Moralidade</h3>
                <p>
                    Vedação categórica à contratação de dirigentes da entidade, seus parentes até terceiro grau ou empresas nas quais mantenham participação societária, prevenindo conflitos de interesse.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Rastreabilidade &amp; Conformidade Fiscal</h3>
                <p>
                    Todos os pagamentos são efetuados exclusivamente via transferência bancária eletrônica identificada, vinculados a notas fiscais eletrônicas e relatórios de ateste de recebimento.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DO DOCUMENTO OFICIAL E CICLO OPERACIONAL -->
<section class="section fin-main-section" id="regulamento">
    <div class="container">

        <!-- DESTAQUE OFICIAL: REGULAMENTO PRÓPRIO DE COMPRAS -->
        <?php if ($tem_pdf): ?>
        <article class="fin-doc-card" style="margin-bottom: 3.5rem; border-left: 4px solid var(--color-primary); background: #ffffff;">
            <div class="fin-doc-top">
                <span class="fin-doc-badge fin-badge-pdf">
                    <i class="ph ph-file-pdf"></i> Norma Interna Homologada
                </span>
                <span class="fin-doc-date">
                    <i class="ph ph-check-circle"></i> Aprovado pela Diretoria &bull; Conforme Lei 13.019/14
                </span>
            </div>

            <h3 class="fin-doc-title" style="font-size:1.4rem; margin-bottom:0.5rem;">
                Regulamento Próprio de Compras e Contratações do INPP
            </h3>

            <p class="fin-doc-desc" style="margin-bottom:1.5rem; max-width:850px;">
                Instrumento normativo formal que define as alçadas de decisão, limites de dispensa de cotação por valor, critérios de julgamento de propostas, minutas padronizadas de contratos e procedimentos de sanções administrativas.
            </p>

            <div class="fin-doc-footer">
                <span class="fin-doc-size">
                    <i class="ph ph-file-arrow-down"></i> <?= $tamanho_txt ?> &bull; Documento Oficial em PDF
                </span>
                <a href="/uploads/transparencia/regulamento_compras.pdf" class="btn btn-primary" target="_blank" rel="noopener">
                    Baixar Regulamento de Compras (PDF) <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </article>
        <?php endif; ?>

        <!-- CICLO OPERACIONAL DE COMPRAS -->
        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Rito Processual</span>
                <h2 class="qs-title">Etapas do Processo de Aquisição</h2>
            </div>
            <span class="fin-count-pill">Fluxo Padronizado</span>
        </div>

        <div class="fin-docs-grid">
            <!-- ETAPA 1 -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-magnifying-glass"></i> Etapa 01
                    </span>
                    <span class="fin-doc-date">Fase Preparatória</span>
                </div>
                <h3 class="fin-doc-title">Pesquisa Mercadológica &amp; Cotação</h3>
                <p class="fin-doc-desc">
                    Elaboração de Termo de Referência com especificação técnica detalhada, justificativa da necessidade vinculada ao projeto e tomada de preços junto ao mercado com solicitação de 3 ou mais orçamentos.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Pesquisa de Mercado</span>
                    <a href="/fornecedores" class="btn btn-outline btn-sm">Cadastro de Fornecedores <i class="ph ph-arrow-right"></i></a>
                </div>
            </article>

            <!-- ETAPA 2 -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-scales"></i> Etapa 02
                    </span>
                    <span class="fin-doc-date">Fase de Seleção</span>
                </div>
                <h3 class="fin-doc-title">Julgamento Objetivo &amp; Habilitação</h3>
                <p class="fin-doc-desc">
                    Verificação da regularidade fiscal e trabalhista do fornecedor (CND Federal, FGTS, CNDT), confronto de valores com a planilha orçamentária do termo de parceria e emissão do mapa comparativo de preços.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Menor Preço e Qualidade</span>
                    <?php if ($tem_pdf): ?>
                    <a href="/uploads/transparencia/<?= e($doc_reg['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Ver Critérios <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>

            <!-- ETAPA 3 -->
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-file-text"></i> Etapa 03
                    </span>
                    <span class="fin-doc-date">Fase Contratual</span>
                </div>
                <h3 class="fin-doc-title">Contratação, Entrega &amp; Prestação</h3>
                <p class="fin-doc-desc">
                    Formalização por instrumento contratual ou ordem de fornecimento, recebimento do bem ou serviço com ateste da coordenação técnica, liquidação da despesa e guarda da documentação para auditoria.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check"></i> Ateste e Prestação de Contas</span>
                    <a href="/transparencia/financeiro" class="btn btn-outline btn-sm">Prestação de Contas <i class="ph ph-arrow-right"></i></a>
                </div>
            </article>
        </div>

        <!-- PAINEL DE CADASTRAMENTO DE FORNECEDORES E OUVIDORIA -->
        <div class="proj-transparency-box" style="margin-top:4rem;">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Fornecedores &amp; Prestadores</span>
                <h3>Deseja fornecer bens ou prestar serviços ao INPP?</h3>
                <p>
                    Empresas idôneas interessadas em participar dos processos de cotação prévia podem se cadastrar no Banco de Fornecedores do INPP ou enviar propostas diretamente ao setor de compras.
                </p>
            </div>
            <div class="proj-transparency-links">
                <a href="/fornecedores" class="btn btn-primary">
                    Portal de Fornecedores
                </a>
                <a href="/editais" class="btn btn-outline">
                    Editais e Cotações
                </a>
                <a href="/contato" class="btn btn-outline">
                    Setor de Compras
                </a>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';

