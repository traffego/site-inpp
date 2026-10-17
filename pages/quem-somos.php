<?php
/**
 * Quem Somos — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Quem Somos';
$page_description = 'Trajetória, princípios institucionais e modelo de atuação do Instituto Nacional de Políticas Públicas (INPP). Fundado em 1982.';

ob_start();
?>
<!-- HERO INSTITUCIONAL -->
<section class="qs-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Quem Somos</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Fundado em 1982 &bull; 44 Anos de Trajetória</span>
            <h1 class="qs-hero-title">Compromisso com o Desenvolvimento Social e a Gestão Pública</h1>
            <p class="qs-hero-lead">
                Há mais de quatro décadas, o Instituto Nacional de Políticas Públicas atua na formulação, articulação e execução de soluções que transformam realidades comunitárias em todo o Brasil.
            </p>
        </div>

        <!-- FICHA TÉCNICA INSTITUCIONAL -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Ano de Fundação</span>
                <strong class="qs-fact-val">1982</strong>
                <span class="qs-fact-desc">44 anos de história</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Natureza Jurídica</span>
                <strong class="qs-fact-val">OSC / Sem Fins Lucrativos</strong>
                <span class="qs-fact-desc">Sociedade civil organizada</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Regime Regulatório</span>
                <strong class="qs-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="qs-fact-desc">Parcerias e fomento público</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Abrangência Operacional</span>
                <strong class="qs-fact-val">Nacional</strong>
                <span class="qs-fact-desc">Projetos em múltiplas regiões</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO 1: TRAJETÓRIA E RETOMADA HISTÓRICA -->
<section class="section qs-history-section" id="historia">
    <div class="container">
        <div class="qs-editorial-grid">
            <div class="qs-editorial-text">
                <div class="qs-section-header">
                    <span class="qs-kicker">Origem & Trajetória</span>
                    <h2 class="qs-title">Uma história construída junto às comunidades brasileiras</h2>
                </div>
                
                <p class="qs-paragraph-lead">
                    O <strong>Instituto Nacional de Políticas Públicas (INPP)</strong> nasceu em 1982 da convicção de que o desenvolvimento social duradouro exige a combinação de sensibilidade humana, método técnico e rigor na aplicação dos recursos públicos.
                </p>
                
                <p>
                    Ao longo de mais de quatro décadas, a organização participou de iniciativas que apoiaram famílias, capacitaram jovens e adultos e estruturaram projetos sociais de base. Após um período de pausa e reestruturação estatutária, o INPP ingressa em uma nova etapa institucional, pautada pela modernização de seus processos, governança e conformidade total com o Marco Regulatório das Organizações da Sociedade Civil (Lei Federal nº 13.019/2014).
                </p>

                <p>
                    Hoje, o instituto consolida sua atuação como ponte técnica e executiva entre as necessidades reais da população e a efetivação das políticas públicas nos municípios e estados brasileiros, abrangendo todas as etapas da vida — da infância à melhor idade.
                </p>
            </div>

            <div class="qs-timeline-wrap">
                <h3 class="qs-timeline-heading">Marcos Institucionais</h3>
                <div class="qs-timeline">
                    <div class="qs-timeline-step">
                        <span class="qs-timeline-year">1982</span>
                        <div class="qs-timeline-body">
                            <h4>Constituição e Primeiras Iniciativas</h4>
                            <p>Criação da entidade e início de projetos sociais e de incentivo à cidadania em âmbito comunitário.</p>
                        </div>
                    </div>

                    <div class="qs-timeline-step">
                        <span class="qs-timeline-year">2000 — 2012</span>
                        <div class="qs-timeline-body">
                            <h4>Expansão de Projetos e Parcerias</h4>
                            <p>Ampliação do escopo de atuação para programas educacionais, formação técnica e desenvolvimento comunitário integrado.</p>
                        </div>
                    </div>

                    <div class="qs-timeline-step">
                        <span class="qs-timeline-year">2014</span>
                        <div class="qs-timeline-body">
                            <h4>Marco Regulatório (MROSC)</h4>
                            <p>Adaptação das práticas operacionais, controles e governança aos parâmetros da Lei Federal nº 13.019/2014.</p>
                        </div>
                    </div>

                    <div class="qs-timeline-step qs-timeline-step-current">
                        <span class="qs-timeline-year">2026</span>
                        <div class="qs-timeline-body">
                            <h4>Reorganização e Retomada</h4>
                            <p>Nova fase institucional: expansão de eixos transversais, controle social e execução de políticas públicas integradas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO 2: MÉTODO E COMO ATUAMOS -->
<section class="section qs-method-section" id="metodologia">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Metodologia de Gestão</span>
            <h2 class="qs-title">Como o INPP Estrutura Suas Intervenções</h2>
            <p class="qs-subtitle">Um ciclo operacional completo que assegura legitimidade, controle de metas e prestação de contas transparente.</p>
        </div>

        <div class="qs-method-grid">
            <div class="qs-method-item">
                <span class="qs-step-number">01</span>
                <h3>Diagnóstico Territorial</h3>
                <p>Mapeamento das vulnerabilidades e das oportunidades territoriais com levantamento de dados e diálogo com a comunidade.</p>
            </div>

            <div class="qs-method-item">
                <span class="qs-step-number">02</span>
                <h3>Modelagem Técnica</h3>
                <p>Planejamento de metas, cronogramas, matriz de riscos e alinhamento estrito às exigências legais e orçamentárias do MROSC.</p>
            </div>

            <div class="qs-method-item">
                <span class="qs-step-number">03</span>
                <h3>Execução no Território</h3>
                <p>Mobilização de equipes qualificadas, gestão transparente de suprimentos e atendimento direto às populações beneficiárias.</p>
            </div>

            <div class="qs-method-item">
                <span class="qs-step-number">04</span>
                <h3>Controle & Transparência</h3>
                <p>Auditoria permanente, relatórios fotográficos, comprovação de metas e prestação de contas aberta à sociedade.</p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO 3: PRINCÍPIOS E PILARES ESTRATÉGICOS -->
<section class="section qs-pillars-section" id="principios">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Fundamentos da Organização</span>
            <h2 class="qs-title">Propósito, Visão e Diretrizes Éticas</h2>
        </div>

        <div class="qs-pillars-grid">
            <div class="qs-pillar-card">
                <span class="qs-pillar-tag">Missão</span>
                <h3>Propósito de Atuação</h3>
                <p>
                    Contribuir para a redução das desigualdades e a emancipação cidadã, implementando projetos sociais e políticas públicas com excelência técnica, eficácia e respeito aos direitos fundamentais.
                </p>
            </div>

            <div class="qs-pillar-card">
                <span class="qs-pillar-tag">Visão</span>
                <h3>Horizonte Estratégico</h3>
                <p>
                    Consolidar-se nacionalmente como uma organização de referência na concepção e execução de projetos públicos e privados que gerem transformações mensuráveis em comunidades brasileiras.
                </p>
            </div>

            <div class="qs-pillar-card">
                <span class="qs-pillar-tag">Valores</span>
                <h3>Diretrizes Inegociáveis</h3>
                <ul class="qs-pillar-list">
                    <li><strong>Integridade e Ética Pública:</strong> probidade na gestão de cada recurso.</li>
                    <li><strong>Primazia Técnica:</strong> decisões pautadas em evidências e planejamento.</li>
                    <li><strong>Controle Social:</strong> transparência irrestrita a órgãos e cidadãos.</li>
                    <li><strong>Dignidade Humana:</strong> centralidade das pessoas em qualquer projeto.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO 4: GOVERNANÇA, COMPLIANCE E TRANSPARÊNCIA -->
<section class="section qs-governance-section" id="governanca">
    <div class="container">
        <div class="qs-governance-box">
            <div class="qs-gov-text">
                <span class="qs-kicker">Governança & MROSC</span>
                <h2 class="qs-title">Prestação de Contas Aberta e Transparência Pública</h2>
                <p>
                    Como Organização da Sociedade Civil, o INPP mantém suas informações societárias, fiscais e financeiras acessíveis a parceiros, órgãos de controle e à sociedade em geral, em estrito cumprimento à Lei nº 13.019/2014 e à Lei de Acesso à Informação (Lei nº 12.527/2011).
                </p>
                <div class="qs-gov-actions">
                    <a href="/transparencia/estatuto" class="btn btn-outline">
                        Estatuto Social
                    </a>
                    <a href="/transparencia/dirigentes" class="btn btn-outline">
                        Quadro de Dirigentes
                    </a>
                    <a href="/transparencia/financeiro" class="btn btn-primary">
                        Portal da Transparência
                    </a>
                </div>
            </div>
            <div class="qs-gov-aside">
                <div class="qs-gov-stat">
                    <strong>100%</strong>
                    <span>Conformidade com o MROSC (Lei 13.019/14)</span>
                </div>
                <div class="qs-gov-stat">
                    <strong>44 Anos</strong>
                    <span>De história e existência jurídica</span>
                </div>
                <div class="qs-gov-stat">
                    <strong>Público</strong>
                    <span>Acesso universal a atas, balanços e termos</span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
