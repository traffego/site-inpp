<?php
/**
 * Admin: Dados da Organização
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$page_title = 'Admin — Dados da Organização';
$breadcrumb = [
    ['label' => 'Dashboard',    'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Configurações'],
    ['label' => 'Organização'],
];

try {
    db_execute("ALTER TABLE tab_org ADD COLUMN tema_padrao VARCHAR(10) DEFAULT 'dark'");
} catch (Throwable $e) {}
try {
    db_execute("ALTER TABLE tab_org ADD COLUMN facebook VARCHAR(255) DEFAULT NULL");
} catch (Throwable $e) {}
try {
    db_execute("ALTER TABLE tab_org ADD COLUMN instagram VARCHAR(255) DEFAULT NULL");
} catch (Throwable $e) {}
try {
    db_execute("ALTER TABLE tab_org ADD COLUMN youtube VARCHAR(255) DEFAULT NULL");
} catch (Throwable $e) {}
try {
    db_execute("ALTER TABLE tab_org ADD COLUMN linkedin VARCHAR(255) DEFAULT NULL");
} catch (Throwable $e) {}

$org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'nome_org'       => trim($_POST['nome_org'] ?? ''),
        'presidente_org' => trim($_POST['presidente_org'] ?? ''),
        'telefone'       => trim($_POST['telefone'] ?? ''),
        'e_mail'         => trim($_POST['e_mail'] ?? ''),
        'endereco'       => trim($_POST['endereco'] ?? ''),
        'bairro'         => trim($_POST['bairro'] ?? ''),
        'cidade'         => trim($_POST['cidade'] ?? ''),
        'estado'         => trim($_POST['estado'] ?? ''),
        'cep'            => trim($_POST['cep'] ?? ''),
        'site'           => trim($_POST['site'] ?? ''),
        'facebook'       => trim($_POST['facebook'] ?? ''),
        'instagram'      => trim($_POST['instagram'] ?? ''),
        'youtube'        => trim($_POST['youtube'] ?? ''),
        'linkedin'       => trim($_POST['linkedin'] ?? ''),
        'tema_padrao'    => trim($_POST['tema_padrao'] ?? 'dark'),
        'liberado'       => isset($_POST['liberado']) ? 1 : 0,
    ];

    if ($org) {
        db_update('tab_org', $dados, 'cod_org = 10001', []);
    } else {
        $dados['cod_org'] = 10001;
        db_insert('tab_org', $dados);
    }

    flash('success', 'Dados da organização e tema padrão atualizados!');
    redirect(base_url('/admin/configuracoes/organizacao.php'));
}

// Preencher com dados atuais
$org = $org ?: [];

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Parâmetros do Sistema</div>
            <h2 class="page-title">Dados Institucionais da Organização</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/dashboard.php') ?>" class="btn btn-outline-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Voltar ao Dashboard
            </a>
        </div>
    </div>
</div>

<form method="POST" id="form-org">
    <?= csrf_field() ?>
    
    <div class="row row-cards">
        <!-- COLUNA PRINCIPAL: DADOS CADASTRAIS E ENDEREÇO (8 Colunas) -->
        <div class="col-12 col-lg-8">
            <!-- Card: Identificação Institucional -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Identificação da Instituição</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome_org" class="form-label required">Razão Social / Nome da Entidade</label>
                        <input type="text" id="nome_org" name="nome_org" class="form-control" required value="<?= e($org['nome_org'] ?? '') ?>" placeholder="Instituto Nacional de Políticas Públicas">
                    </div>
                    
                    <div class="mb-3">
                        <label for="presidente_org" class="form-label">Diretor Presidente / Representante Legal</label>
                        <input type="text" id="presidente_org" name="presidente_org" class="form-control" value="<?= e($org['presidente_org'] ?? '') ?>" placeholder="Nome do representante legal">
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label for="telefone" class="form-label">Telefone Principal</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" value="<?= e($org['telefone'] ?? '') ?>" data-mask="telefone" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="e_mail" class="form-label">E-mail Institucional</label>
                            <input type="email" id="e_mail" name="e_mail" class="form-control" value="<?= e($org['e_mail'] ?? '') ?>" placeholder="contato@inpp.org.br">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Endereço da Sede -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Sede Administrativa &amp; Localização</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Logradouro &amp; Número</label>
                        <input type="text" id="endereco" name="endereco" class="form-control" value="<?= e($org['endereco'] ?? '') ?>" placeholder="Avenida, Rua, Número, Sala">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" id="bairro" name="bairro" class="form-control" value="<?= e($org['bairro'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" id="cep" name="cep" class="form-control font-monospace" value="<?= e($org['cep'] ?? '') ?>" data-mask="cep" placeholder="00000-000">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-8">
                            <label for="cidade" class="form-label">Cidade / Município</label>
                            <input type="text" id="cidade" name="cidade" class="form-control" value="<?= e($org['cidade'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="estado" class="form-label">Estado (UF)</label>
                            <input type="text" id="estado" name="estado" class="form-control text-uppercase" value="<?= e($org['estado'] ?? '') ?>" maxlength="2" placeholder="DF">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Redes Sociais Institucionais -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Redes Sociais Institucionais</h3>
                </div>
                <div class="card-body">
                    <p class="text-secondary small mb-3">
                        Preencha apenas as redes que a instituição utiliza. No portal público (rodapé e contato), somente os ícones com link cadastrado serão exibidos. Deixe em branco os que não possuir.
                    </p>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label for="instagram" class="form-label">Instagram</label>
                            <input type="url" id="instagram" name="instagram" class="form-control" value="<?= e($org['instagram'] ?? '') ?>" placeholder="https://instagram.com/inpp">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="url" id="facebook" name="facebook" class="form-control" value="<?= e($org['facebook'] ?? '') ?>" placeholder="https://facebook.com/inpp">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="youtube" class="form-label">YouTube</label>
                            <input type="url" id="youtube" name="youtube" class="form-control" value="<?= e($org['youtube'] ?? '') ?>" placeholder="https://youtube.com/@inpp">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="linkedin" class="form-label">LinkedIn</label>
                            <input type="url" id="linkedin" name="linkedin" class="form-control" value="<?= e($org['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/company/inpp">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUNA LATERAL: CONFIGURAÇÕES E SALVAMENTO (4 Colunas) -->
        <div class="col-12 col-lg-4">
            <!-- Card: Portal & Preferências -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Portal Público</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="site" class="form-label">URL do Website</label>
                        <input type="url" id="site" name="site" class="form-control font-monospace" value="<?= e($org['site'] ?? '') ?>" placeholder="https://inpp.org.br">
                    </div>

                    <div class="mb-3">
                        <label for="tema_padrao" class="form-label">Tema Inicial para Visitantes</label>
                        <select id="tema_padrao" name="tema_padrao" class="form-select">
                            <option value="light" <?= ($org['tema_padrao'] ?? 'light') === 'light' ? 'selected' : '' ?>>☀️ Modo Claro (Light Mode)</option>
                            <option value="dark" <?= ($org['tema_padrao'] ?? 'light') === 'dark' ? 'selected' : '' ?>>🌙 Modo Escuro (Dark Mode)</option>
                        </select>
                        <small class="form-hint">Tema apresentado aos visitantes antes de personalizarem sua preferência no portal.</small>
                    </div>

                    <div>
                        <label class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="liberado" value="1" <?= !empty($org['liberado']) ? 'checked' : '' ?>>
                            <span class="form-check-label fw-medium">Organização Habilitada</span>
                        </label>
                        <small class="text-secondary d-block">Indica que o cadastro e serviços da instituição estão ativos no sistema.</small>
                    </div>
                </div>
            </div>

            <!-- Card de Ações -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Salvar Configurações
                    </button>
                    <a href="<?= base_url('/admin/dashboard.php') ?>" class="btn btn-outline-secondary w-100">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
