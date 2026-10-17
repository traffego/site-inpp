<?php
/**
 * Fornecedores — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';

$page_title       = 'Cadastro de Fornecedores & Prestadores';
$page_description = 'Credenciamento de empresas fornecedoras e prestadores de serviços para cotações e aquisições do Instituto Nacional de Políticas Públicas (INPP).';

$estados = db_fetch_all('SELECT idUf, nome FROM estado ORDER BY nome');
$erros   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'nome_fantasia'     => trim($_POST['nome_fantasia'] ?? ''),
        'razao_social'      => trim($_POST['razao_social'] ?? ''),
        'cnpj'              => trim($_POST['cnpj'] ?? ''),
        'endereco'          => trim($_POST['endereco'] ?? ''),
        'bairro'            => trim($_POST['bairro'] ?? ''),
        'cidade'            => trim($_POST['cidade'] ?? ''),
        'estado'            => trim($_POST['estado'] ?? ''),
        'cep'               => trim($_POST['cep'] ?? ''),
        'contato_nome'      => trim($_POST['contato_nome'] ?? ''),
        'contato_cargo'     => trim($_POST['contato_cargo'] ?? ''),
        'contato_telefone'  => trim($_POST['contato_telefone'] ?? ''),
        'contato_email'     => trim($_POST['contato_email'] ?? ''),
    ];

    if (empty($dados['razao_social']))     $erros[] = 'Razão social é obrigatória.';
    if (empty($dados['cnpj']))             $erros[] = 'CNPJ é obrigatório.';
    if (empty($dados['contato_nome']))     $erros[] = 'Nome do contato é obrigatório.';
    if (empty($dados['contato_telefone'])) $erros[] = 'Telefone do contato é obrigatório.';
    if (!empty($dados['contato_email']) && !filter_var($dados['contato_email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail do contato inválido.';
    }

    if (empty($erros)) {
        db_insert('tab_fornecedores', $dados);
        flash('success', 'Cadastro realizado com sucesso! Sua empresa está registrada em nosso banco de fornecedores para cotações de preços.');
        redirect('/fornecedores');
    } else {
        foreach ($dados as $k => $v) $_SESSION['old'][$k] = $v;
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
            <span>Fornecedores</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Compras Públicas &bull; Cotações &bull; MROSC</span>
            <h1 class="qs-hero-title">Cadastro Geral de Fornecedores</h1>
            <p class="qs-hero-lead">
                Credenciamento de empresas fornecedoras e prestadores de serviços para participação em cotações prévias de preços e processos de aquisição vinculados aos projetos executados pelo INPP.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE COMPRAS E CONTRATAÇÕES -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Princípio Legal</span>
                <strong class="qs-fact-val">Isonomia &amp; Economicidade</strong>
                <span class="qs-fact-desc">Art. 42 da Lei nº 13.019/2014</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Procedimento</span>
                <strong class="qs-fact-val">Cotação Prévia</strong>
                <span class="qs-fact-desc">Pesquisas de mercado abertas</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Integridade</span>
                <strong class="qs-fact-val">Compliance &amp; Idoneidade</strong>
                <span class="qs-fact-desc">Regularidade fiscal e jurídica</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Transparência</span>
                <strong class="qs-fact-val">100% Público</strong>
                <span class="qs-fact-desc">Notas e comprovantes auditáveis</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DE DIRETRIZES DE AQUISIÇÕES -->
<section class="section tc-guidelines-section">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Diretrizes de Aquisição</span>
            <h2 class="qs-title">Como o INPP Contrata Bens e Serviços</h2>
            <p class="qs-subtitle">
                Todas as aquisições de materiais, locações e serviços vinculadas a termos de fomento seguem regras formais de cotação de mercado, garantindo a melhor aplicação dos recursos públicos.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Cotações Prévias Periódicas</h3>
                <p>
                    O cadastro prévio habilita sua empresa a receber convites e solicitações de orçamentos para aquisições diretas e certames vinculados aos projetos do instituto.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Regularidade Fiscal e Cadastral</h3>
                <p>
                    Para contratação efetiva, exige-se CNPJ ativo compatível com o objeto social, Certidões Negativas de Débitos (CNDs federais, estaduais e municipais) e regularidade perante o FGTS.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Publicidade e Prestação de Contas</h3>
                <p>
                    Em obediência ao MROSC, todos os processos de compra, cotações comparativas, contratos e comprovantes de pagamento são lançados no <a href="/transparencia/financeiro">Portal da Transparência</a>.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DO FORMULÁRIO DE CADASTRO -->
<section class="section tc-form-section" id="fornecedores">
    <div class="container container-narrow">

        <div class="tc-form-intro">
            <span class="qs-kicker">Credenciamento de Pessoa Jurídica</span>
            <h2 class="qs-title">Formulário de Cadastro Empresarial</h2>
            <p>Preencha os dados cadastrais da sua empresa. As informações integrarão nosso banco de potenciais fornecedores para envio de cotações.</p>
        </div>

        <div id="js-form-errors" class="alert alert-error mb-6" style="display:none;" role="alert">
            <strong>Por favor, corrija os campos apontados abaixo:</strong>
            <ul id="js-errors-list"></ul>
        </div>

        <?php if (!empty($erros)): ?>
        <div class="alert alert-error mb-6" role="alert">
            <strong>Por favor, corrija os erros abaixo:</strong>
            <ul><?php foreach ($erros as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <div class="tc-form-card">
            <form method="POST" action="/fornecedores" id="form-fornecedor">
                <?= csrf_field() ?>

                <!-- ETAPA 1: DADOS DA EMPRESA -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">01</span>
                        <div>
                            <h3 class="tc-block-title">Identificação da Empresa</h3>
                            <p class="tc-block-desc">Razão social, nome fantasia e registro de CNPJ.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-group-wide">
                            <label for="razao_social" class="form-label">Razão Social Completa <span class="required">*</span></label>
                            <input type="text" id="razao_social" name="razao_social" class="form-input" required
                                   value="<?= e(old('razao_social')) ?>" placeholder="Razão social conforme cartão CNPJ">
                        </div>
                        <div class="form-group">
                            <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                            <input type="text" id="nome_fantasia" name="nome_fantasia" class="form-input"
                                   value="<?= e(old('nome_fantasia')) ?>" placeholder="Nome comercial da empresa">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cnpj" class="form-label">CNPJ <span class="required">*</span></label>
                        <input type="text" id="cnpj" name="cnpj" class="form-input" required
                               value="<?= e(old('cnpj')) ?>" placeholder="00.000.000/0000-00"
                               data-mask="cnpj" maxlength="18">
                    </div>
                </div>

                <!-- ETAPA 2: SEDE E ENDEREÇO -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">02</span>
                        <div>
                            <h3 class="tc-block-title">Sede Operacional &amp; Localização</h3>
                            <p class="tc-block-desc">Endereço da matriz ou filial responsável pelo atendimento.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep_forn" class="form-label">CEP</label>
                            <input type="text" id="cep_forn" name="cep" class="form-input"
                                   value="<?= e(old('cep')) ?>" placeholder="00000-000" data-mask="cep" maxlength="9">
                        </div>
                        <div class="form-group form-group-wide">
                            <label for="endereco" class="form-label">Logradouro &amp; Número</label>
                            <input type="text" id="endereco" name="endereco" class="form-input"
                                   value="<?= e(old('endereco')) ?>" placeholder="Rua, avenida, número, complemento/sala">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" id="bairro" name="bairro" class="form-input"
                                   value="<?= e(old('bairro')) ?>" placeholder="Nome do bairro">
                        </div>
                        <div class="form-group">
                            <label for="estado_forn" class="form-label">Estado (UF)</label>
                            <select id="estado_forn" name="estado" class="form-select" data-estado-select="cidade_forn">
                                <option value="">Selecione o estado</option>
                                <?php foreach ($estados as $uf): ?>
                                <option value="<?= e($uf['idUf']) ?>" <?= old('estado') === $uf['idUf'] ? 'selected' : '' ?>>
                                    <?= e($uf['nome']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cidade_forn" class="form-label">Município</label>
                            <select id="cidade_forn" name="cidade" class="form-select"
                                    data-cidade-select="cidade_forn"
                                    data-valor-antigo="<?= e(old('cidade')) ?>">
                                <option value="">Selecione o estado primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ETAPA 3: CONTATO COMERCIAL -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">03</span>
                        <div>
                            <h3 class="tc-block-title">Responsável Comercial / Contato</h3>
                            <p class="tc-block-desc">Pessoa de referência para recebimento de cotações e orçamentos.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-group-wide">
                            <label for="contato_nome" class="form-label">Nome do Contato <span class="required">*</span></label>
                            <input type="text" id="contato_nome" name="contato_nome" class="form-input" required
                                   value="<?= e(old('contato_nome')) ?>" placeholder="Nome completo do responsável">
                        </div>
                        <div class="form-group">
                            <label for="contato_cargo" class="form-label">Cargo / Setor</label>
                            <input type="text" id="contato_cargo" name="contato_cargo" class="form-input"
                                   value="<?= e(old('contato_cargo')) ?>" placeholder="Ex: Diretor Comercial, Gerente de Vendas">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contato_telefone" class="form-label">Telefone / WhatsApp <span class="required">*</span></label>
                            <input type="tel" id="contato_telefone" name="contato_telefone" class="form-input" required
                                   value="<?= e(old('contato_telefone')) ?>" placeholder="(00) 00000-0000" data-mask="telefone">
                        </div>
                        <div class="form-group">
                            <label for="contato_email" class="form-label">E-mail Comercial</label>
                            <input type="email" id="contato_email" name="contato_email" class="form-input"
                                   value="<?= e(old('contato_email')) ?>" placeholder="comercial@empresa.com.br">
                        </div>
                    </div>
                </div>

                <div class="tc-form-footer">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-cadastrar">
                        <i class="ph ph-check-circle"></i> Confirmar &amp; Salvar Cadastro
                    </button>
                    <p class="tc-form-disclaimer">
                        <i class="ph ph-shield-check"></i> Os dados cadastrais fornecidos destinam-se exclusivamente à formação do banco de cotações de preços do INPP, em conformidade com as exigências da Lei nº 13.019/2014.
                    </p>
                </div>
            </form>
        </div>

    </div>
</section>

<!-- PAINEL DE TRANSPARÊNCIA E EDITAIS ABERTOS -->
<section class="section tc-bottom-transparency">
    <div class="container">
        <div class="proj-transparency-box">
            <div class="proj-transparency-text">
                <span class="qs-kicker">Cotações Públicas &amp; Aquisições</span>
                <h3>Consulte os Editais e Termos de Fomento</h3>
                <p>
                    Acesse a lista completa de editais, termos de cooperação e chamamentos públicos abertos pelo Instituto Nacional de Políticas Públicas.
                </p>
            </div>
            <div class="proj-transparency-links">
                <a href="/editais" class="btn btn-primary">
                    Consultar Editais Vigentes
                </a>
                <a href="/transparencia/termos" class="btn btn-outline">
                    Termos de Parceria
                </a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
