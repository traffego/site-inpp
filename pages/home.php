<?php
/**
 * Home — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Início';
$page_description = 'Instituto Nacional de Políticas Públicas (INPP) — 44 anos construindo caminhos e transformando vidas.';

// Documentos e transparência pública
$total_docs = 0;
try {
    if (function_exists('db_count')) {
        $total_docs = db_count('SELECT COUNT(*) FROM tab_projetos_documentos');
    }
} catch (Throwable $e) {
    $total_docs = 0;
}

ob_start();
?>
<!-- HERO PRINCIPAL -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content fade-in-up">
        <span class="hero-badge">
            <i class="ph-bold ph-shield-check" style="font-size:14px; margin-right:4px;"></i> 
            44 Anos de Trajetória &amp; Cidadania
        </span>
        <h1 class="hero-title">Instituto Nacional de<br><span class="text-gradient">Políticas Públicas</span></h1>
        <p class="hero-subtitle">
            Construindo caminhos e gerando oportunidades para todas as fases da vida — da infância à melhor idade — com integridade técnica, governança e responsabilidade social.
        </p>
        <div class="hero-actions">
            <a href="#parcerias" class="btn btn-primary btn-lg">
                <i class="ph ph-handshake"></i> Propor Parceria
            </a>
            <a href="#eixos" class="btn btn-outline btn-lg">
                <i class="ph ph-squares-four"></i> Eixos de Atuação
            </a>
            <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('transparencia')): ?>
            <a href="/transparencia/declaracao" class="btn btn-accent btn-lg">
                <i class="ph-duotone ph-file-text"></i> Transparência
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-scroll-hint" aria-hidden="true">
        <span></span>
    </div>
</section>

<!-- EIXOS ESTRUTURANTES DE ATUAÇÃO -->
<section class="section" id="eixos">
    <div class="container">
        <div class="section-header fade-in-up">
            <span class="section-tag">Nossos Eixos de Impacto</span>
            <h2 class="section-title">Onde o INPP Desenvolve Soluções</h2>
            <p class="section-subtitle">
                Atuamos na formulação, gestão e articulação de iniciativas transversais desenhadas para atender comunidades e órgãos públicos com rigor técnico.
            </p>
        </div>

        <div class="eixos-grid">
            <!-- EIXO 1: EDUCAÇÃO -->
            <div class="eixo-card theme-blue fade-in-up">
                <div>
                    <div class="eixo-icon">
                        <i class="ph-duotone ph-graduation-cap"></i>
                    </div>
                    <h3 class="eixo-title">Educação &amp; Capacitação</h3>
                    <p class="eixo-desc">
                        Programas de qualificação profissional, inclusão digital, contraturno escolar e estímulo ao primeiro emprego para jovens e adultos.
                    </p>
                </div>
                <div class="eixo-footer">
                    <span class="eixo-footer-lbl">Público-alvo</span>
                    <span class="eixo-tag">Jovens e Adultos</span>
                </div>
            </div>

            <!-- EIXO 2: DESENVOLVIMENTO SOCIAL -->
            <div class="eixo-card theme-green fade-in-up" style="animation-delay:.1s">
                <div>
                    <div class="eixo-icon">
                        <i class="ph-duotone ph-heart-straight"></i>
                    </div>
                    <h3 class="eixo-title">Desenvolvimento Social</h3>
                    <p class="eixo-desc">
                        Geração de autonomia, combate às vulnerabilidades, projetos intergeracionais e fortalecimento dos vínculos comunitários e familiares.
                    </p>
                </div>
                <div class="eixo-footer">
                    <span class="eixo-footer-lbl">Público-alvo</span>
                    <span class="eixo-tag">Famílias &amp; Comunidades</span>
                </div>
            </div>

            <!-- EIXO 3: CULTURA, ESPORTE & BEM-ESTAR -->
            <div class="eixo-card theme-cyan fade-in-up" style="animation-delay:.2s">
                <div>
                    <div class="eixo-icon">
                        <i class="ph-duotone ph-heartbeat"></i>
                    </div>
                    <h3 class="eixo-title">Cultura, Esporte &amp; Bem-Estar</h3>
                    <p class="eixo-desc">
                        Incentivo à prática de atividades saudáveis, recreação formativa, integração cultural e promoção da saúde preventiva para todas as idades.
                    </p>
                </div>
                <div class="eixo-footer">
                    <span class="eixo-footer-lbl">Público-alvo</span>
                    <span class="eixo-tag">Infância &amp; Melhor Idade</span>
                </div>
            </div>

            <!-- EIXO 4: GOVERNANÇA & POLÍTICAS PÚBLICAS -->
            <div class="eixo-card theme-navy fade-in-up" style="animation-delay:.3s">
                <div>
                    <div class="eixo-icon">
                        <i class="ph-duotone ph-buildings"></i>
                    </div>
                    <h3 class="eixo-title">Governança &amp; Políticas Públicas</h3>
                    <p class="eixo-desc">
                        Estruturação técnica de termos de parceria MROSC, monitoramento contínuo de metas sociais, conformidade jurídica e transparência pública.
                    </p>
                </div>
                <div class="eixo-footer">
                    <span class="eixo-footer-lbl">Público-alvo</span>
                    <span class="eixo-tag">Poder Público &amp; Convênios</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NOSSA TRAJETÓRIA & REORGANIZAÇÃO (44 ANOS) -->
<section class="section" id="historia" style="background: var(--bg-surface); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); padding: 4.5rem 0;">
    <div class="container">
        <div class="about-grid fade-in-up">
            <div class="about-text">
                <span class="section-tag">Nossa História &amp; Propósito</span>
                <h2 class="section-title">44 anos construindo caminhos e transformando vidas</h2>
                
                <p>
                    Com mais de quatro décadas de história, o <strong>Instituto Nacional de Políticas Públicas (INPP)</strong> consolidou sua trajetória na promoção do desenvolvimento social, da cidadania e da dignidade humana.
                </p>
                
                <p>
                    Após uma pausa e um rigoroso processo de reorganização institucional, o INPP retoma suas atividades inaugurando uma nova fase, com projetos modernos e parcerias públicas e privadas de alto impacto.
                </p>
                
                <p>
                    Atuamos de forma abrangente em todas as fases da vida — da infância à melhor idade —, gerando oportunidades em educação, cultura, capacitação, esporte e cidadania, unindo a maturidade de <strong>44 anos de experiência</strong> à força de um novo recomeço.
                </p>

                <div style="margin-top: 2rem;">
                    <a href="/quem-somos" class="btn btn-primary btn-lg">
                        <i class="ph ph-book-open"></i> Conheça a História Completa
                    </a>
                </div>
            </div>

            <div class="about-visual">
                <div class="inpp-feature-card">
                    <div class="feature-logo-wrap">
                        <img src="<?= asset('assets/img/logo_inpp.png') ?>" alt="INPP - Instituto Nacional de Políticas Públicas" class="feature-logo-img">
                        <span class="feature-badge">
                            <i class="ph-bold ph-shield-check"></i> 44 Anos de Trajetória &amp; Governança
                        </span>
                    </div>
                    
                    <p class="feature-summary">
                        Formulação e gestão de iniciativas públicas com rigor técnico, integridade institucional e foco em resultados para a sociedade.
                    </p>

                    <div class="feature-metrics-grid">
                        <div class="feature-metric-card">
                            <span class="feature-metric-num">44</span>
                            <span class="feature-metric-title">Anos de História</span>
                            <span class="feature-metric-sub">1982 — 2026</span>
                        </div>
                        <div class="feature-metric-card">
                            <span class="feature-metric-num">100%</span>
                            <span class="feature-metric-title">Compromisso Social</span>
                            <span class="feature-metric-sub">Governança &amp; MROSC</span>
                        </div>
                    </div>

                    <div class="feature-trust-badge">
                        <i class="ph-bold ph-check-circle"></i>
                        <span>Em estrita conformidade com a Lei Federal nº 13.019/14</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SEGURANÇA JURÍDICA & GOVERNANÇA (MROSC) -->
<section class="section" id="governanca">
    <div class="container">
        <div class="governanca-box fade-in-up">
            <span class="section-tag" style="color: var(--color-primary-light); background: rgba(255,255,255,0.1); padding: 0.25rem 0.75rem; border-radius: var(--radius-full); display: inline-block; margin-bottom: 1rem;">
                Governança &amp; Integridade
            </span>
            <h3>Segurança jurídica e impacto mensurável para gestores públicos</h3>
            <p>
                Nossa atuação respeita integralmente as diretrizes do Marco Regulatório das Organizações da Sociedade Civil (Lei Federal nº 13.019/2014). Cada cooperação técnica é construída sobre planos de trabalho com metas objetivas, controle de custos, auditoria contínua e foco em indicadores reais de benefício à sociedade.
            </p>

            <div class="governanca-pills">
                <div class="gov-pill">
                    <span class="gov-pill-num">1982—2026</span>
                    <span class="gov-pill-lbl">44 anos de trajetória no terceiro setor e na gestão cívica.</span>
                </div>
                <div class="gov-pill">
                    <span class="gov-pill-num" style="color: #4ade80;">Lei 13.019/14</span>
                    <span class="gov-pill-lbl">Conformidade estrita com o MROSC e prestação de contas.</span>
                </div>
                <div class="gov-pill">
                    <span class="gov-pill-num">Transparência</span>
                    <span class="gov-pill-lbl">Acesso público a estatutos, dirigentes e demonstrativos fiscais.</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA & CAPTAÇÃO DE PARCERIAS -->
<section class="section" id="parcerias" style="margin-bottom: 4rem;">
    <div class="container">
        <div class="parceria-grid fade-in-up">
            <div class="parceria-info">
                <span class="section-tag">Novo Capítulo</span>
                <h2 class="section-title">Deseja propor uma iniciativa ou convênio em sua região?</h2>
                <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1.5rem;">
                    O INPP está aberto ao diálogo com prefeituras, secretarias estaduais e municipais, institutos e empresas privadas para viabilizar projetos com respaldo histórico, capacidade técnica e foco em resultados.
                </p>

                <ul class="parceria-checklist">
                    <li>
                        <i class="ph-bold ph-check-circle"></i>
                        <span>Elaboração técnica conjunta de Planos de Trabalho e metas de impacto.</span>
                    </li>
                    <li>
                        <i class="ph-bold ph-check-circle"></i>
                        <span>Gestão transparente com relatórios periódicos e acompanhamento contínuo.</span>
                    </li>
                    <li>
                        <i class="ph-bold ph-check-circle"></i>
                        <span>Articulação institucional para contratação de equipes e banco de talentos.</span>
                    </li>
                </ul>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem;">
                    <a href="/contato" class="btn btn-outline btn-lg">
                        <i class="ph ph-envelope-simple"></i> Canais de Atendimento
                    </a>
                    <a href="/trabalhe-conosco" class="btn btn-primary btn-lg">
                        <i class="ph ph-briefcase"></i> Banco de Talentos
                    </a>
                </div>
            </div>

            <div class="parceria-form-card">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">
                    Proposta de Parceria Institucional
                </h3>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                    Preencha o formulário para conectar sua demanda pública com nossa equipe técnica:
                </p>

                <form action="/contato" method="GET">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.775rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Nome / Representante</label>
                            <input type="text" name="nome" placeholder="Seu nome completo" required style="width: 100%; padding: 0.65rem 0.85rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.775rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Órgão / Empresa / Município</label>
                            <input type="text" name="orgao" placeholder="Ex: Secretaria de Assistência Social" required style="width: 100%; padding: 0.65rem 0.85rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.775rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">E-mail Institucional</label>
                            <input type="email" name="email" placeholder="contato@entidade.gov.br" required style="width: 100%; padding: 0.65rem 0.85rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.775rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Eixo de Atuação de Interesse</label>
                            <select name="assunto" style="width: 100%; padding: 0.65rem 0.85rem; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface); color: var(--text-main);">
                                <option value="Educação e Capacitação">Educação &amp; Capacitação</option>
                                <option value="Desenvolvimento Social">Desenvolvimento Social &amp; Famílias</option>
                                <option value="Cultura, Esporte e Bem-Estar">Cultura, Esporte &amp; Bem-Estar</option>
                                <option value="Governança e Políticas Públicas">Governança &amp; Políticas Públicas</option>
                                <option value="Outro">Outra Demanda Institucional</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem; justify-content: center; padding: 0.85rem 1.5rem;">
                            <i class="ph-bold ph-paper-plane-tilt"></i> Iniciar Contato de Parceria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
