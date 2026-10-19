<?php
/**
 * Contato — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/mail.php';

$page_title       = 'Contato Institucional & Atendimento';
$page_description = 'Canais de comunicação direta com o Instituto Nacional de Políticas Públicas (INPP). Fale com nossa equipe técnica, ouvidoria ou proponha parcerias.';

// Dados da organização
$org = null;
try {
    if (function_exists('db_fetch')) {
        $org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001 LIMIT 1');
    }
} catch (Throwable $e) {}

$cnpj = $org['cnpj'] ?? '27.282.557/0001-19';
$telefone = $org['telefone'] ?? '(81) 98328-8465';
$email = (!empty($org['e_mail'])) ? $org['e_mail'] : (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'contato@inpp.org');

$endereco_completo = 'Av. Ayrton Senna, nº 3.000 – sala 321 bloco 1 – Barra da Tijuca, Rio de Janeiro - RJ, CEP 22.775-904';
if (!empty($org['endereco'])) {
    $endereco_completo = $org['endereco'];
    if (!empty($org['bairro'])) $endereco_completo .= ' – ' . $org['bairro'];
    if (!empty($org['cidade']) && !empty($org['estado'])) {
        $endereco_completo .= ', ' . $org['cidade'] . ' - ' . $org['estado'];
    }
    if (!empty($org['cep'])) $endereco_completo .= ', CEP ' . $org['cep'];
}

$tel_num = preg_replace('/\D/', '', $telefone);

// Processar formulário
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $assunto  = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (empty($nome))     $erros[] = 'Nome é obrigatório.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    if (empty($assunto))  $erros[] = 'Assunto é obrigatório.';
    if (empty($mensagem)) $erros[] = 'Mensagem é obrigatória.';

    if (empty($erros)) {
        $enviado = send_contact_mail($nome, $email, $telefone, $mensagem);
        if ($enviado) {
            flash('success', 'Mensagem enviada com sucesso! Retornaremos o seu contato em breve.');
            redirect('/contato');
        } else {
            flash('error', 'Erro ao enviar mensagem. Tente novamente ou contate-nos diretamente pelo telefone institucional.');
        }
    }

    if (!empty($erros)) {
        $_SESSION['old']['nome']     = $nome;
        $_SESSION['old']['email']    = $email;
        $_SESSION['old']['telefone'] = $telefone;
        $_SESSION['old']['assunto']  = $assunto;
        $_SESSION['old']['mensagem'] = $mensagem;
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
            <span>Contato</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Comunicação Oficial &bull; Ouvidoria &bull; MROSC</span>
            <h1 class="qs-hero-title">Canais de Atendimento Institucional</h1>
            <p class="qs-hero-lead">
                Canal direto de diálogo com a sociedade, órgãos governamentais, proponentes de parcerias, entidades do terceiro setor e veículos de imprensa.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE ATENDIMENTO -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Prazo de Resposta</span>
                <strong class="qs-fact-val">Até 48h úteis</strong>
                <span class="qs-fact-desc">Protocolo e registro interno</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Canais Oficiais</span>
                <strong class="qs-fact-val">Formulário, E-mail &amp; Tel</strong>
                <span class="qs-fact-desc">Comunicação formal e rastreável</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Controle Social</span>
                <strong class="qs-fact-val">LAI (Lei 12.527/11)</strong>
                <span class="qs-fact-desc">Acesso à informação garantido</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Horário da Sede</span>
                <strong class="qs-fact-val">Seg a Sex &bull; 09h às 18h</strong>
                <span class="qs-fact-desc">Horário oficial de Brasília</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DE CANAIS SETORIAIS -->
<section class="section tc-guidelines-section">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Direcionamento por Setor</span>
            <h2 class="qs-title">Como Podemos Ajudar</h2>
            <p class="qs-subtitle">
                Identifique abaixo o assunto de seu interesse para um encaminhamento mais ágil de sua solicitação.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Parcerias &amp; Convênios</h3>
                <p>
                    Para secretarias municipais, estaduais, órgãos federais e instituições privadas que desejam propor termos de cooperação, termos de fomento ou projetos conjuntos.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Ouvidoria &amp; Transparência</h3>
                <p>
                    Pedidos de esclarecimento sobre projetos em andamento, relatórios de prestação de contas, consultas com respaldo da Lei de Acesso à Informação e sugestões comunitárias.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Secretaria &amp; Fornecedores</h3>
                <p>
                    Informações sobre editais de chamamento público, processos seletivos de pessoal, credenciamento de fornecedores e solicitações administrativas gerais.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO PRINCIPAL DE CONTATO E FORMULÁRIO -->
<section class="section ct-main-section" id="contato">
    <div class="container">

        <div id="js-form-errors" class="alert alert-error mb-6" style="display:none;" role="alert">
            <strong>Por favor, corrija os campos apontados abaixo:</strong>
            <ul id="js-errors-list"></ul>
        </div>

        <?php if (!empty($erros)): ?>
        <div class="alert alert-error mb-6" role="alert">
            <strong>Por favor, corrija os erros abaixo:</strong>
            <ul>
                <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="ct-layout-grid">
            <!-- FORMULÁRIO INSTITUCIONAL -->
            <div class="ct-form-card">
                <div class="ct-card-header">
                    <span class="qs-kicker">Mensagem Eletrônica</span>
                    <h2 class="qs-title">Envie Sua Solicitação</h2>
                    <p class="ct-card-sub">Preencha os campos abaixo com dados válidos para que nossa equipe técnica possa responder formalmente.</p>
                </div>

                <form method="POST" action="/contato" id="form-contato">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="nome" class="form-label">Nome Completo <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-input" required
                               value="<?= e(old('nome')) ?>" placeholder="Seu nome completo" autocomplete="name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">E-mail Institucional ou Pessoal <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-input" required
                                   value="<?= e(old('email')) ?>" placeholder="exemplo@email.com" autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                            <input type="tel" id="telefone" name="telefone" class="form-input"
                                   value="<?= e(old('telefone')) ?>" placeholder="(00) 00000-0000"
                                   data-mask="telefone" autocomplete="tel">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assunto" class="form-label">Assunto da Mensagem <span class="required">*</span></label>
                        <select id="assunto" name="assunto" class="form-select" required>
                            <option value="">Selecione o assunto</option>
                            <option value="Proposta de Parceria Institucional" <?= old('assunto') === 'Proposta de Parceria Institucional' ? 'selected' : '' ?>>Proposta de Parceria Institucional (Governo / Empresa)</option>
                            <option value="Acesso à Informação & Transparência" <?= old('assunto') === 'Acesso à Informação & Transparência' ? 'selected' : '' ?>>Acesso à Informação &amp; Transparência (LAI)</option>
                            <option value="Editais & Chamamentos Públicos" <?= old('assunto') === 'Editais & Chamamentos Públicos' ? 'selected' : '' ?>>Editais &amp; Chamamentos Públicos</option>
                            <option value="Cadastro de Fornecedores" <?= old('assunto') === 'Cadastro de Fornecedores' ? 'selected' : '' ?>>Cadastro de Fornecedores &amp; Cotações</option>
                            <option value="Imprensa & Comunicação" <?= old('assunto') === 'Imprensa & Comunicação' ? 'selected' : '' ?>>Imprensa &amp; Comunicação Institucional</option>
                            <option value="Dúvidas Gerais & Ouvidoria" <?= old('assunto') === 'Dúvidas Gerais & Ouvidoria' ? 'selected' : '' ?>>Dúvidas Gerais &amp; Ouvidoria</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensagem" class="form-label">Mensagem Detalhada <span class="required">*</span></label>
                        <textarea id="mensagem" name="mensagem" class="form-textarea" required rows="5"
                                  placeholder="Descreva detalhadamente o objetivo de seu contato, informações do projeto ou solicitação..."><?= e(old('mensagem')) ?></textarea>
                    </div>

                    <div class="ct-form-submit-wrap">
                        <button type="submit" class="btn btn-primary btn-lg" id="btn-enviar">
                            <i class="ph ph-paper-plane-tilt"></i> Enviar Mensagem Oficial
                        </button>
                        <p class="tc-form-disclaimer">
                            <i class="ph ph-shield-check"></i> As informações enviadas serão tratadas em conformidade com a LGPD (Lei nº 13.709/2018).
                        </p>
                    </div>
                </form>
            </div>

            <!-- INFORMAÇÕES DA SEDE & CONTATOS DIRETOS -->
            <div class="ct-info-column">
                <div class="ct-info-card">
                    <div class="ct-card-header">
                        <span class="qs-kicker">Sede Operacional</span>
                        <h3 class="ct-info-title">Instituto Nacional de Políticas Públicas</h3>
                    </div>

                    <ul class="ct-details-list">
                        <li class="ct-detail-item">
                            <div class="ct-detail-icon">
                                <i class="ph-duotone ph-map-pin"></i>
                            </div>
                            <div class="ct-detail-content">
                                <strong>Endereço da Sede</strong>
                                <span><?= e($endereco_completo) ?></span>
                            </div>
                        </li>

                        <li class="ct-detail-item">
                            <div class="ct-detail-icon">
                                <i class="ph-duotone ph-identification-card"></i>
                            </div>
                            <div class="ct-detail-content">
                                <strong>CNPJ Institucional</strong>
                                <span><?= e($cnpj) ?></span>
                            </div>
                        </li>

                        <li class="ct-detail-item">
                            <div class="ct-detail-icon">
                                <i class="ph-duotone ph-phone"></i>
                            </div>
                            <div class="ct-detail-content">
                                <strong>Telefone de Atendimento</strong>
                                <a href="tel:<?= e($tel_num) ?>"><?= e($telefone) ?></a>
                            </div>
                        </li>

                        <li class="ct-detail-item">
                            <div class="ct-detail-icon">
                                <i class="ph-duotone ph-envelope-simple"></i>
                            </div>
                            <div class="ct-detail-content">
                                <strong>E-mail Institucional</strong>
                                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                            </div>
                        </li>

                        <li class="ct-detail-item">
                            <div class="ct-detail-icon">
                                <i class="ph-duotone ph-clock"></i>
                            </div>
                            <div class="ct-detail-content">
                                <strong>Horário de Funcionamento</strong>
                                <span>Segunda a Sexta-feira: 09h às 18h</span>
                            </div>
                        </li>
                    </ul>

                    <?php
                    $has_social = !empty($org['facebook']) || !empty($org['instagram']) || !empty($org['youtube']) || !empty($org['linkedin']);
                    ?>
                    <?php if ($has_social): ?>
                    <div class="ct-social-box">
                        <span class="ct-social-label">Canais Oficiais nas Redes Sociais</span>
                        <div class="footer-social">
                            <?php if (!empty($org['facebook'])): ?>
                            <a href="<?= e($org['facebook']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <i class="ph ph-facebook-logo"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($org['instagram'])): ?>
                            <a href="<?= e($org['instagram']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <i class="ph ph-instagram-logo"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($org['youtube'])): ?>
                            <a href="<?= e($org['youtube']) ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                <i class="ph ph-youtube-logo"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($org['linkedin'])): ?>
                            <a href="<?= e($org['linkedin']) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                <i class="ph ph-linkedin-logo"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- CARD DE ACESSO À INFORMAÇÃO -->
                <div class="ct-transparency-card">
                    <div class="ct-transparency-icon">
                        <i class="ph-duotone ph-shield-check"></i>
                    </div>
                    <div>
                        <h4>Portal da Transparência</h4>
                        <p>Documentos estatutários, demonstrativos financeiros, termos de fomento e relatórios de auditoria estão disponíveis para acesso público.</p>
                        <?php if (is_transparencia_habilitada('financeiro')): ?>
                        <a href="/transparencia/financeiro" class="btn btn-outline btn-sm">
                            Acessar Prestação de Contas <i class="ph ph-arrow-right"></i>
                        </a>
                        <?php elseif (is_transparencia_habilitada('estatuto')): ?>
                        <a href="/transparencia/estatuto" class="btn btn-outline btn-sm">
                            Acessar Portal da Transparência <i class="ph ph-arrow-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
