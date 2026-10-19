<?php
/**
 * Trabalhe Conosco — Instituto Nacional de Políticas Públicas (INPP)
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/upload.php';

$page_title       = 'Trabalhe Conosco & Banco de Talentos';
$page_description = 'Cadastre seu currículo no Instituto Nacional de Políticas Públicas (INPP) e participe de nossos processos seletivos e projetos em todo o Brasil.';

// Dados para os selects
$projetos = db_fetch_all('SELECT id, nome_projeto FROM tab_projetos WHERE ativo = 1 ORDER BY nome_projeto');
$estados  = db_fetch_all('SELECT idUf, nome FROM estado ORDER BY nome');

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // Coletar todos os dados
    $dados = [
        'nome'                    => trim($_POST['nome'] ?? ''),
        'sexo'                    => trim($_POST['sexo'] ?? ''),
        'data_nascimento'         => trim($_POST['data_nascimento'] ?? ''),
        'estado_civil'            => trim($_POST['estado_civil'] ?? ''),
        'telefone_1'              => trim($_POST['telefone_1'] ?? ''),
        'telefone_2'              => trim($_POST['telefone_2'] ?? ''),
        'e_mail'                  => trim($_POST['e_mail'] ?? ''),
        'cep'                     => trim($_POST['cep'] ?? ''),
        'endereco'                => trim($_POST['endereco'] ?? ''),
        'bairro'                  => trim($_POST['bairro'] ?? ''),
        'estado'                  => trim($_POST['estado'] ?? ''),
        'cidade'                  => trim($_POST['cidade'] ?? ''),
        'id_projeto'              => (int)($_POST['id_projeto'] ?? 0),
        'id_funcao'               => (int)($_POST['id_funcao'] ?? 0),
        'escolaridade'            => trim($_POST['escolaridade'] ?? ''),
        'idioma_sim_nao'          => (int)($_POST['idioma_sim_nao'] ?? 0),
        'idioma_quais'            => trim($_POST['idioma_quais'] ?? ''),
        'informatica_sim_nao'     => (int)($_POST['informatica_sim_nao'] ?? 0),
        'cursos_relevantes'       => trim($_POST['cursos_relevantes'] ?? ''),
        'experiencia_sim_nao'     => (int)($_POST['experiencia_sim_nao'] ?? 0),
        'empresa_1'               => trim($_POST['empresa_1'] ?? ''),
        'empresa_1_periodo'       => trim($_POST['empresa_1_periodo'] ?? ''),
        'empresa_1_funcao'        => trim($_POST['empresa_1_funcao'] ?? ''),
        'empresa_2'               => trim($_POST['empresa_2'] ?? ''),
        'empresa_2_periodo'       => trim($_POST['empresa_2_periodo'] ?? ''),
        'empresa_2_funcao'        => trim($_POST['empresa_2_funcao'] ?? ''),
        'empresa_3'               => trim($_POST['empresa_3'] ?? ''),
        'empresa_3_periodo'       => trim($_POST['empresa_3_periodo'] ?? ''),
        'empresa_3_funcao'        => trim($_POST['empresa_3_funcao'] ?? ''),
        'experiencia_profissional' => trim($_POST['experiencia_profissional'] ?? ''),
    ];

    // Validações básicas
    if (empty($dados['nome']))            $erros[] = 'Nome completo é obrigatório.';
    if (empty($dados['sexo']))            $erros[] = 'Sexo é obrigatório.';
    if (empty($dados['data_nascimento']))   $erros[] = 'Data de nascimento é obrigatória.';
    if (empty($dados['telefone_1']))      $erros[] = 'Telefone principal é obrigatório.';
    if (empty($dados['endereco']))        $erros[] = 'Endereço é obrigatório.';
    if (empty($dados['bairro']))          $erros[] = 'Bairro é obrigatório.';
    if (empty($dados['cep']))             $erros[] = 'CEP é obrigatório.';
    if (empty($dados['estado']))          $erros[] = 'Estado é obrigatório.';
    if (empty($dados['cidade']))          $erros[] = 'Cidade é obrigatória.';
    if (!$dados['id_projeto'])            $erros[] = 'Selecione um projeto.';
    if (!$dados['id_funcao'])             $erros[] = 'Selecione uma função/cargo.';
    if (empty($dados['escolaridade']))    $erros[] = 'Escolaridade é obrigatória.';

    // Gerenciamento de Upload e Preservação de PDF Temporário
    $temp_pdf_path = trim($_POST['temp_pdf_path'] ?? '');
    $temp_pdf_name = trim($_POST['temp_pdf_name'] ?? '');

    $temp_dir = UPLOAD_PATH . '/temp';
    if (!is_dir($temp_dir)) @mkdir($temp_dir, 0755, true);

    if (isset($_FILES['arquivo_curriculo']) && $_FILES['arquivo_curriculo']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_file($_FILES['arquivo_curriculo'], $temp_dir, ['pdf']);
        if ($upload['success']) {
            $temp_pdf_path = 'temp/' . $upload['filename'];
            $temp_pdf_name = $_FILES['arquivo_curriculo']['name'];
        } else {
            $erros[] = 'Erro no upload do currículo: ' . $upload['error'];
        }
    } elseif (!empty($temp_pdf_path) && file_exists(UPLOAD_PATH . '/' . $temp_pdf_path)) {
        // Arquivo temporário preservado de envio anterior
    } else {
        $erros[] = 'Envio do currículo em PDF é obrigatório.';
    }

    if (empty($erros)) {
        // Mover arquivo temporário para a pasta definitiva de currículos
        $pasta_curriculos = UPLOAD_PATH . '/curriculos';
        if (!is_dir($pasta_curriculos)) @mkdir($pasta_curriculos, 0755, true);

        $nome_definitivo = uniqid('curriculo_') . '.pdf';
        $caminho_origem = UPLOAD_PATH . '/' . $temp_pdf_path;
        $caminho_destino = $pasta_curriculos . '/' . $nome_definitivo;

        if (file_exists($caminho_origem) && rename($caminho_origem, $caminho_destino)) {
            $dados['arquivo_curriculo'] = $nome_definitivo;
            db_insert('tab_curriculos', $dados);
            
            unset($_SESSION['old']);
            flash('success', 'Currículo enviado com sucesso! Seus dados foram cadastrados em nosso banco de talentos.');
            redirect('/trabalhe-conosco');
        } else {
            $erros[] = 'Erro ao salvar o arquivo de currículo. Por favor, anexe o PDF novamente.';
        }
    }

    // Repopular todos os dados digitados e arquivo em caso de erro
    foreach ($dados as $k => $v) {
        $_SESSION['old'][$k] = $v;
    }
    if (!empty($temp_pdf_path)) {
        $_SESSION['old']['temp_pdf_path'] = $temp_pdf_path;
        $_SESSION['old']['temp_pdf_name'] = $temp_pdf_name;
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
            <span>Trabalhe Conosco</span>
        </nav>
        <div class="qs-hero-content">
            <span class="qs-badge-history">Recrutamento &amp; Banco de Talentos &bull; MROSC</span>
            <h1 class="qs-hero-title">Oportunidades &amp; Banco de Talentos</h1>
            <p class="qs-hero-lead">
                Faça parte de uma equipe multidisciplinar comprometida com a formulação, gestão e execução de políticas públicas que transformam realidades sociais em todo o território nacional.
            </p>
        </div>

        <!-- RÉGUA TÉCNICA DE DIRETRIZES DE CONTRATAÇÃO -->
        <div class="qs-facts-bar">
            <div class="qs-fact-item">
                <span class="qs-fact-label">Critério de Seleção</span>
                <strong class="qs-fact-val">Mérito &amp; Isonomia</strong>
                <span class="qs-fact-desc">Processos seletivos públicos</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Regime Regulatório</span>
                <strong class="qs-fact-val">MROSC &bull; Lei 13.019/14</strong>
                <span class="qs-fact-desc">Equipes alocadas por projeto</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Privacidade &amp; Dados</span>
                <strong class="qs-fact-val">LGPD (Lei 13.709/18)</strong>
                <span class="qs-fact-desc">Sigilo e proteção de currículos</span>
            </div>
            <div class="qs-fact-item">
                <span class="qs-fact-label">Transparência</span>
                <strong class="qs-fact-val">Editais Públicos</strong>
                <span class="qs-fact-desc">Homologações e resultados abertos</span>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DE DIRETRIZES E MODALIDADES DE INGRESSO -->
<section class="section tc-guidelines-section">
    <div class="container">
        <div class="qs-section-header text-center qs-center-header">
            <span class="qs-kicker">Estrutura de Oportunidades</span>
            <h2 class="qs-title">Como Funcionam as Seleções no INPP</h2>
            <p class="qs-subtitle">
                Atuamos na execução de parcerias com a administração pública e entidades privadas, demandando profissionais qualificados em diversas áreas de conhecimento.
            </p>
        </div>

        <div class="tc-guidelines-grid">
            <div class="tc-guideline-card">
                <span class="tc-card-num">01</span>
                <h3>Banco de Talentos Contínuo</h3>
                <p>
                    Ao preencher o formulário abaixo, seu perfil técnico fica registrado em nossa base para chamamentos imediatos em novos projetos ou substituição de equipes.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">02</span>
                <h3>Seleções Vinculadas a Editais</h3>
                <p>
                    Vagas para projetos específicos são abertas mediante processo seletivo público com critérios de pontuação claros, divulgados em nossa aba de <a href="/editais">Editais &amp; Chamamentos</a>.
                </p>
            </div>

            <div class="tc-guideline-card">
                <span class="tc-card-num">03</span>
                <h3>Equipes Multidisciplinares</h3>
                <p>
                    Buscamos educadores, pedagogos, assistentes sociais, psicólogos, instrutores técnicos, coordenadores de projetos, analistas administrativos e pesquisadores.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SEÇÃO DO FORMULÁRIO DE CANDIDATURA -->
<section class="section tc-form-section" id="candidatura">
    <div class="container container-narrow">

        <div class="tc-form-intro">
            <span class="qs-kicker">Formulário de Inscrição</span>
            <h2 class="qs-title">Cadastre Seu Currículo</h2>
            <p>Preencha os campos abaixo com atenção. Todos os dados são utilizados exclusivamente para avaliação técnica em nossos processos seletivos.</p>
        </div>

        <div id="js-form-errors" class="alert alert-error mb-6" style="display:none;" role="alert">
            <strong>Por favor, corrija os campos apontados abaixo:</strong>
            <ul id="js-errors-list"></ul>
        </div>

        <?php if (!empty($erros)): ?>
        <div class="alert alert-error mb-6" role="alert">
            <strong>Por favor, corrija os erros abaixo:</strong>
            <ul>
                <?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="tc-form-card">
            <form method="POST" action="/trabalhe-conosco" enctype="multipart/form-data" id="form-curriculo">
                <?= csrf_field() ?>

                <!-- ETAPA 1: DADOS PESSOAIS -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">01</span>
                        <div>
                            <h3 class="tc-block-title">Identificação Pessoal</h3>
                            <p class="tc-block-desc">Informações básicas para contato e localização.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nome" class="form-label">Nome Completo <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-input" required
                               value="<?= e(old('nome')) ?>" placeholder="Nome completo do candidato" autocomplete="name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Sexo <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="sexo" value="Masculino" <?= old('sexo') === 'Masculino' ? 'checked' : '' ?> required>
                                    <span>Masculino</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="sexo" value="Feminino" <?= old('sexo') === 'Feminino' ? 'checked' : '' ?>>
                                    <span>Feminino</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="data_nascimento" class="form-label">Data de Nascimento <span class="required">*</span></label>
                            <input type="date" id="data_nascimento" name="data_nascimento" class="form-input" required
                                   value="<?= e(old('data_nascimento')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="estado_civil" class="form-label">Estado Civil <span class="required">*</span></label>
                            <select id="estado_civil" name="estado_civil" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach (['Solteiro(a)', 'Casado(a)', 'Divorciado(a)', 'Viúvo(a)', 'União Estável'] as $ec): ?>
                                <option value="<?= e($ec) ?>" <?= old('estado_civil') === $ec ? 'selected' : '' ?>><?= e($ec) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone_1" class="form-label">Telefone Principal (WhatsApp) <span class="required">*</span></label>
                            <input type="tel" id="telefone_1" name="telefone_1" class="form-input" required
                                   value="<?= e(old('telefone_1')) ?>" placeholder="(00) 00000-0000" data-mask="telefone">
                        </div>
                        <div class="form-group">
                            <label for="telefone_2" class="form-label">Telefone Secundário</label>
                            <input type="tel" id="telefone_2" name="telefone_2" class="form-input"
                                   value="<?= e(old('telefone_2')) ?>" placeholder="(00) 00000-0000" data-mask="telefone">
                        </div>
                        <div class="form-group">
                            <label for="e_mail" class="form-label">E-mail de Contato</label>
                            <input type="email" id="e_mail" name="e_mail" class="form-input"
                                   value="<?= e(old('e_mail')) ?>" placeholder="exemplo@email.com" autocomplete="email">
                        </div>
                    </div>
                </div>

                <!-- ETAPA 2: ENDEREÇO -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">02</span>
                        <div>
                            <h3 class="tc-block-title">Endereço &amp; Localidade</h3>
                            <p class="tc-block-desc">Necessário para alocação em projetos regionais e comunitários.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep" class="form-label">CEP <span class="required">*</span></label>
                            <input type="text" id="cep" name="cep" class="form-input" required
                                   value="<?= e(old('cep')) ?>" placeholder="00000-000" data-mask="cep" maxlength="9">
                        </div>
                        <div class="form-group form-group-wide">
                            <label for="endereco" class="form-label">Endereço Completo <span class="required">*</span></label>
                            <input type="text" id="endereco" name="endereco" class="form-input" required
                                   value="<?= e(old('endereco')) ?>" placeholder="Logradouro, número, complemento" autocomplete="street-address">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro <span class="required">*</span></label>
                            <input type="text" id="bairro" name="bairro" class="form-input" required
                                   value="<?= e(old('bairro')) ?>" placeholder="Nome do bairro">
                        </div>
                        <div class="form-group">
                            <label for="estado_res" class="form-label">Estado (UF) <span class="required">*</span></label>
                            <select id="estado_res" name="estado" class="form-select" required
                                    data-estado-select="cidade_res">
                                <option value="">Selecione o estado</option>
                                <?php foreach ($estados as $uf): ?>
                                <option value="<?= e($uf['idUf']) ?>"
                                    <?= old('estado') === $uf['idUf'] ? 'selected' : '' ?>
                                    data-valor-antigo-target="cidade_res">
                                    <?= e($uf['nome']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cidade_res" class="form-label">Município <span class="required">*</span></label>
                            <select id="cidade_res" name="cidade" class="form-select" required
                                    data-cidade-select="cidade_res"
                                    data-valor-antigo="<?= e(old('cidade')) ?>">
                                <option value="">Selecione o estado primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ETAPA 3: VINCULAÇÃO AO PROJETO -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">03</span>
                        <div>
                            <h3 class="tc-block-title">Projeto &amp; Função de Interesse</h3>
                            <p class="tc-block-desc">Selecione o projeto ativo e o cargo ao qual deseja se candidatar.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="projeto" class="form-label">Projeto Ativo <span class="required">*</span></label>
                            <select id="projeto" name="id_projeto" class="form-select" required data-projeto-select>
                                <option value="">Selecione o projeto</option>
                                <?php foreach ($projetos as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (int)old('id_projeto') === (int)$p['id'] ? 'selected' : '' ?>>
                                    <?= e($p['nome_projeto']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="funcao" class="form-label">Função / Cargo Pretendido <span class="required">*</span></label>
                            <select id="funcao" name="id_funcao" class="form-select" required data-funcao-select>
                                <option value="">Selecione o projeto primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ETAPA 4: FORMAÇÃO ACADÊMICA -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">04</span>
                        <div>
                            <h3 class="tc-block-title">Formação Acadêmica &amp; Qualificações</h3>
                            <p class="tc-block-desc">Nível educacional, habilidades linguísticas e cursos relevantes.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="escolaridade" class="form-label">Nível de Escolaridade <span class="required">*</span></label>
                        <select id="escolaridade" name="escolaridade" class="form-select" required>
                            <option value="">Selecione a escolaridade</option>
                            <?php foreach (['Ensino Fundamental Incompleto','Ensino Fundamental Completo','Ensino Médio Incompleto','Ensino Médio Completo','Ensino Superior Incompleto','Ensino Superior Completo','Pós-Graduação','Mestrado','Doutorado'] as $esc): ?>
                            <option value="<?= e($esc) ?>" <?= old('escolaridade') === $esc ? 'selected' : '' ?>><?= e($esc) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Possui idiomas estrangeiros? <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="idioma_sim_nao" value="1" <?= old('idioma_sim_nao') == '1' ? 'checked' : '' ?>>
                                    <span>Sim</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="idioma_sim_nao" value="0" <?= old('idioma_sim_nao') == '0' || old('idioma_sim_nao') === '' ? 'checked' : '' ?>>
                                    <span>Não</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Conhecimentos em informática / sistemas?</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="informatica_sim_nao" value="1" <?= old('informatica_sim_nao') == '1' ? 'checked' : '' ?>>
                                    <span>Sim</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="informatica_sim_nao" value="0" <?= old('informatica_sim_nao') == '0' || old('informatica_sim_nao') === '' ? 'checked' : '' ?>>
                                    <span>Não</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="bloco-idiomas" style="display:none">
                        <div class="form-group">
                            <label for="idioma_quais" class="form-label">Quais idiomas e níveis de fluência?</label>
                            <input type="text" id="idioma_quais" name="idioma_quais" class="form-input"
                                   value="<?= e(old('idioma_quais')) ?>" placeholder="Ex: Inglês (Intermediário), Espanhol (Básico)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cursos_relevantes" class="form-label">Cursos, Certificações e Treinamentos</label>
                        <textarea id="cursos_relevantes" name="cursos_relevantes" class="form-textarea" rows="3"
                                  placeholder="Mencione cursos de capacitação técnica, extensão universitária ou especializações..."><?= e(old('cursos_relevantes')) ?></textarea>
                    </div>
                </div>

                <!-- ETAPA 5: EXPERIÊNCIA PROFISSIONAL -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">05</span>
                        <div>
                            <h3 class="tc-block-title">Histórico Profissional</h3>
                            <p class="tc-block-desc">Atuações anteriores em iniciativas sociais, públicas ou privadas.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Possui experiência profissional anterior? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="experiencia_sim_nao" value="1" <?= old('experiencia_sim_nao') == '1' ? 'checked' : '' ?>>
                                <span>Sim</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="experiencia_sim_nao" value="0" <?= old('experiencia_sim_nao') == '0' || old('experiencia_sim_nao') === '' ? 'checked' : '' ?>>
                                <span>Não</span>
                            </label>
                        </div>
                    </div>

                    <div id="bloco-experiencias" style="display:none">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="experience-block">
                            <h4 class="experience-block-title">Empresa / Organização <?= $i ?></h4>
                            <div class="form-row">
                                <div class="form-group form-group-wide">
                                    <label for="empresa_<?= $i ?>" class="form-label">Nome da Instituição</label>
                                    <input type="text" id="empresa_<?= $i ?>" name="empresa_<?= $i ?>" class="form-input"
                                           value="<?= e(old("empresa_$i")) ?>" placeholder="Empresa ou órgão">
                                </div>
                                <div class="form-group">
                                    <label for="empresa_<?= $i ?>_periodo" class="form-label">Período de Atuação</label>
                                    <input type="text" id="empresa_<?= $i ?>_periodo" name="empresa_<?= $i ?>_periodo" class="form-input"
                                           value="<?= e(old("empresa_{$i}_periodo")) ?>" placeholder="Ex: 01/2021 — 12/2023">
                                </div>
                                <div class="form-group">
                                    <label for="empresa_<?= $i ?>_funcao" class="form-label">Função Exercida</label>
                                    <input type="text" id="empresa_<?= $i ?>_funcao" name="empresa_<?= $i ?>_funcao" class="form-input"
                                           value="<?= e(old("empresa_{$i}_funcao")) ?>" placeholder="Cargo / função">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>

                        <div class="form-group">
                            <label for="experiencia_profissional" class="form-label">Resumo das Principais Atribuições &amp; Conquistas</label>
                            <textarea id="experiencia_profissional" name="experiencia_profissional" class="form-textarea" rows="4"
                                      placeholder="Descreva sucintamente suas principais experiências e competências desenvolvidas..."><?= e(old('experiencia_profissional')) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- ETAPA 6: ARQUIVO DO CURRÍCULO -->
                <div class="tc-form-block">
                    <div class="tc-block-header">
                        <span class="tc-block-num">06</span>
                        <div>
                            <h3 class="tc-block-title">Anexo do Currículo (PDF)</h3>
                            <p class="tc-block-desc">Envie o documento completo e atualizado em formato PDF.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="arquivo_curriculo" class="form-label">Arquivo do Currículo em PDF <span class="required">*</span></label>
                        <div class="file-upload-area">
                            <input type="file" id="arquivo_curriculo" name="arquivo_curriculo"
                                   accept=".pdf" <?= !empty(old('temp_pdf_path')) ? '' : 'required' ?>
                                   data-accept="pdf" data-max-mb="16"
                                   data-nome-alvo="#nome-arquivo-curriculo">
                            <label for="arquivo_curriculo" class="file-upload-label">
                                <i class="ph ph-file-pdf tc-upload-icon"></i>
                                <span>Clique aqui para selecionar ou arraste o PDF</span>
                                <small>Formato exigido: PDF &bull; Tamanho limite: até 16MB</small>
                            </label>
                            <span id="nome-arquivo-curriculo" class="file-name">
                                <?= !empty(old('temp_pdf_name')) ? 'PDF Salvo: ' . e(old('temp_pdf_name')) : 'Nenhum arquivo selecionado' ?>
                            </span>
                            <?php if (!empty(old('temp_pdf_path'))): ?>
                                <input type="hidden" name="temp_pdf_path" value="<?= e(old('temp_pdf_path')) ?>">
                                <input type="hidden" name="temp_pdf_name" value="<?= e(old('temp_pdf_name')) ?>">
                                <div class="tc-pdf-saved-alert">
                                    <i class="ph ph-check-circle"></i> Currículo preservado: <strong><?= e(old('temp_pdf_name')) ?></strong> (Se desejar, pode selecionar outro para substituir)
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tc-form-footer">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-enviar">
                        <i class="ph ph-paper-plane-tilt"></i> Confirmar &amp; Enviar Candidatura
                    </button>
                    <p class="tc-form-disclaimer">
                        <i class="ph ph-shield-check"></i> Os dados e documentos anexados são confidenciais e tratados em estrita observância à Lei Geral de Proteção de Dados (LGPD — Lei nº 13.709/2018).
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
                <span class="qs-kicker">Chamamentos Públicos &amp; Seleções</span>
                <h3>Editais Abertos para Contratação</h3>
                <p>
                    Para acompanhar os editais vigentes de seleção pública de pessoal, critérios de pontuação técnica e resultados de homologação por projeto, acesse a página de Editais e Documentos Oficiais.
                </p>
            </div>
            <div class="proj-transparency-links">
                <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('editais')): ?>
                <a href="/editais" class="btn btn-primary">
                    Consultar Editais Abertos
                </a>
                <?php endif; ?>
                <a href="/fornecedores" class="btn btn-outline">
                    Cadastro de Fornecedores
                </a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
