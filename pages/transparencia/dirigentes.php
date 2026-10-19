<?php
/**
 * Transparência: Dirigentes — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Corpo Diretivo & Governança';
$page_description = 'Identificação dos dirigentes, órgãos colegiados e representação legal do Instituto Nacional de Políticas Públicas (INPP).';

$dirigentes  = db_fetch_all('SELECT * FROM tab_dirigente WHERE cod_org = 10001 ORDER BY posicao, id');
$total_dir   = count($dirigentes);

$doc_dir = db_fetch("SELECT * FROM tab_transparencia_arquivos WHERE tipo = 'dirigentes' AND habilitado = 1 LIMIT 1");
$tem_pdf = false;
$tamanho_txt = '';
if ($doc_dir) {
    $caminho_pdf = UPLOAD_PATH . '/transparencia/' . $doc_dir['arquivo'];
    if (file_exists($caminho_pdf)) {
        $tem_pdf = true;
        $tamanho_kb = round(filesize($caminho_pdf) / 1024, 1);
        $tamanho_txt = $tamanho_kb > 1024 ? round($tamanho_kb / 1024, 1) . ' MB' : ($tamanho_kb > 0 ? $tamanho_kb . ' KB' : 'PDF');
    }
}

$org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001 LIMIT 1') ?: [];

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
            <span>Dirigentes</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Governança &bull; Gestão Colegiada &bull; Estatuto</span>
            <h1 class="qs-hero-title">Corpo Diretivo &amp; Governança Institucional</h1>
            <p class="qs-hero-lead">
                Identificação formal dos dirigentes, gestores executivos e membros do Conselho Fiscal responsáveis pela condução estratégica, administrativa e legal do Instituto Nacional de Políticas Públicas.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE DIRIGENTES -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Registro Cartorial</span>
                <strong class="qs-fact-val">Ata de Eleição e Posse</strong>
                <span class="qs-fact-desc">Mandato vigente registrado</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Regime Estatutário</span>
                <strong class="qs-fact-val">Atuação Voluntária</strong>
                <span class="qs-fact-desc">Sem remuneração ou distribuição</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Instâncias Deliberativas</span>
                <strong class="qs-fact-val">Diretoria &amp; Conselho Fiscal</strong>
                <span class="qs-fact-desc">Duplo nível de controle e ética</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Documento Oficial</span>
                <strong class="qs-fact-val"><?= $tem_pdf ? 'Arquivo Oficial (' . $tamanho_txt . ')' : 'Em Atualização' ?></strong>
                <span class="qs-fact-desc"><?= $tem_pdf ? 'Disponível para download público' : 'Mandato registrado' ?></span>
            </div>
        </div>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA DINÂMICA -->
<?php $subnav_items = get_transparencia_subnav_items('dirigentes'); ?>
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


<!-- SEÇÃO DE DIRETRIZES DE GOVERNANÇA -->
<section class="section tc-guidelines-section" style="padding-top:1rem;">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Estrutura de Gestão</span>
            <h2 class="qs-title">Princípios da Governança Diretiva</h2>
            <p class="qs-subtitle">
                A liderança do INPP atua com base no Estatuto Social, na segregação de poderes e nas salvaguardas da Lei Federal nº 13.019/2014.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Segregação de Competências</h3>
                <p>
                    Separação inequívoca entre as deliberações de gestão executiva e a fiscalização contábil-orçamentária exercida de forma independente pelo Conselho Fiscal.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Atuação Voluntária Estatutária</h3>
                <p>
                    Os membros da Diretoria Executiva exercem seus mandatos em regime estatutário, vedada a remuneração por verbas de termos de fomento ou distribuição de sobras financeiras.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Responsabilidade Fiduciária</h3>
                <p>
                    Compromisso dos representantes legais com a probidade administrativa, conformidade com os órgãos de controle e fidelidade aos planos de trabalho aprovados.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO PRINCIPAL DE DIRIGENTES E DOCUMENTO OFICIAL -->
<section class="section fin-main-section" id="dirigentes">
    <div class="container">

        <!-- DESTAQUE OFICIAL: ATA NOTARIAL E RELAÇÃO DE DIRIGENTES -->
        <?php if ($tem_pdf): ?>
        <article class="fin-doc-card" style="margin-bottom: 3rem; border-left: 4px solid var(--color-primary); background: #ffffff;">
            <div class="fin-doc-top">
                <span class="fin-doc-badge fin-badge-pdf">
                    <i class="ph ph-file-pdf"></i> Documento Oficial Registrado
                </span>
                <span class="fin-doc-date">
                    <i class="ph ph-shield-check"></i> Cartório de Registro de Pessoas Jurídicas
                </span>
            </div>

            <h3 class="fin-doc-title" style="font-size:1.35rem; margin-bottom:0.5rem;">
                Ata Notarial de Eleição e Relação Oficial de Dirigentes
            </h3>

            <p class="fin-doc-desc" style="margin-bottom:1.5rem; max-width:850px;">
                Instrumento público registrado em cartório contendo a ata da Assembleia Geral Ordinária de eleição, o respectivo termo de posse e a qualificação completa dos membros da Diretoria Executiva e do Conselho Fiscal do INPP para o mandato vigente.
            </p>

            <div class="fin-doc-footer">
                <span class="fin-doc-size">
                    <i class="ph ph-file-arrow-down"></i> <?= $tamanho_txt ?> &bull; Formato PDF Oficial
                </span>
                <a href="/uploads/transparencia/dirigentes.pdf" class="btn btn-primary" target="_blank" rel="noopener">
                    Baixar Ata e Relação Oficial de Dirigentes <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </article>
        <?php endif; ?>

        <!-- RELAÇÃO DE MEMBROS NOMINAIS OU ESTRUTURA ESTATUTÁRIA -->
        <div class="fin-section-header">
            <div>
                <span class="qs-kicker">Organograma Institucional</span>
                <h2 class="qs-title">Instâncias de Administração e Controle</h2>
            </div>
            <span class="fin-count-pill">Estatuto Social &bull; Gestão Colegiada</span>
        </div>

        <?php if (!empty($dirigentes)): ?>
        <!-- GRID DE DIRIGENTES NOMINAIS CADASTRADOS -->
        <div class="fin-docs-grid">
            <?php foreach ($dirigentes as $d): ?>
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-user"></i> <?= e($d['cargo_diretor'] ?: 'Dirigente') ?>
                    </span>
                    <span class="fin-doc-date">Mandato Vigente</span>
                </div>
                <h3 class="fin-doc-title"><?= e($d['nome_diretor']) ?></h3>
                <p class="fin-doc-desc">
                    <?= !empty($d['resumo']) ? e($d['resumo']) : 'Membro integrante do corpo diretivo estatutário do Instituto Nacional de Políticas Públicas.' ?>
                </p>
                <div class="fin-doc-footer">
                    <?php if (!empty($d['telefone'])): ?>
                    <span class="fin-doc-size"><i class="ph ph-phone"></i> <?= e($d['telefone']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($d['e_mail'])): ?>
                    <a href="mailto:<?= e($d['e_mail']) ?>" class="btn btn-outline btn-sm">
                        <i class="ph ph-envelope"></i> Contato Oficial
                    </a>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>

        <!-- PAINEL EDITORIAL DE INSTÂNCIAS DE GOVERNANÇA ESTATUTÁRIAS -->
        <div class="fin-docs-grid">
            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-users"></i> Órgão Soberano
                    </span>
                    <span class="fin-doc-date">Deliberação Coletiva</span>
                </div>
                <h3 class="fin-doc-title">Assembleia Geral</h3>
                <p class="fin-doc-desc">
                    Instância deliberativa máxima do INPP, composta pelos associados fundadores e efetivos. Compete-lhe aprovar reformas estatutárias, eleger a Diretoria Executiva, avaliar relatórios de atividades e homologar as contas anuais.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-calendar-blank"></i> Reuniões Ordinárias Anuais</span>
                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('estatuto')): ?>
                    <a href="/transparencia/estatuto" class="btn btn-outline btn-sm">Ver no Estatuto <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>

            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-briefcase"></i> Gestão Executiva
                    </span>
                    <span class="fin-doc-date">Representação Legal</span>
                </div>
                <h3 class="fin-doc-title">Diretoria Executiva</h3>
                <p class="fin-doc-desc">
                    Responsável pela representação civil, jurídica e administrativa do instituto, celebração de parcerias e termos com o poder público, gestão financeira e supervisão geral das operações e projetos em âmbito nacional.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-shield-check"></i> Gestão Colegiada</span>
                    <?php if ($tem_pdf): ?>
                    <a href="/uploads/transparencia/<?= e($doc_dir['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Ata de Posse <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>

            <article class="fin-doc-card">
                <div class="fin-doc-top">
                    <span class="fin-doc-badge fin-badge-sheet">
                        <i class="ph ph-scales"></i> Controle Interno
                    </span>
                    <span class="fin-doc-date">Fiscalização Independente</span>
                </div>
                <h3 class="fin-doc-title">Conselho Fiscal</h3>
                <p class="fin-doc-desc">
                    Órgão autônomo encarregado da auditoria dos atos financeiros, exame de balancetes, conferência de livros contábeis e emissão anual de parecer técnico sobre a regularidade das contas submetidas à Assembleia Geral.
                </p>
                <div class="fin-doc-footer">
                    <span class="fin-doc-size"><i class="ph ph-check-circle"></i> Parecer Contábil</span>
                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('financeiro')): ?>
                    <a href="/transparencia/financeiro" class="btn btn-outline btn-sm">Balanços Contábeis <i class="ph ph-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </article>
        </div>

        <?php endif; ?>

        <!-- PAINEL DE CONTATO INSTITUCIONAL DA SEDE E OUVIDORIA -->
        <div class="proj-transparency-box" style="margin-top:4rem;">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Expediente Oficial &amp; Secretaria</span>
                <h3>Comunicação com a Diretoria e Órgãos Colegiados</h3>
                <p>
                    Requerimentos formais, notificações institucionais ou convites a assembleias podem ser encaminhados diretamente à Secretaria Executiva do INPP através dos canais de expediente oficiais da sede.
                </p>
            </div>
            <div class="proj-transparency-links">
                <a href="/contato" class="btn btn-primary">
                    Secretaria &amp; Expediente
                </a>
                <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('estatuto')): ?>
                <a href="/transparencia/estatuto" class="btn btn-outline">
                    Estatuto Social
                </a>
                <?php endif; ?>
                <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('declaracao')): ?>
                <a href="/transparencia/declaracao" class="btn btn-outline">
                    Declaração Institucional
                </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';

