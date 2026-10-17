<?php
/**
 * Admin: Editar Projeto (Tabler UI)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

$projeto = db_fetch('SELECT * FROM tab_projetos WHERE id = ?', [$id]);
if (!$projeto) { flash('error', 'Projeto não encontrado.'); redirect(base_url('/admin/projetos/listar.php')); }

$status_list = db_fetch_all('SELECT * FROM tab_projetos_status ORDER BY id');
$erros       = [];

$page_title = 'Admin — Editar Projeto';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Projetos',  'url' => base_url('/admin/projetos/listar.php')],
    ['label' => 'Editar: ' . truncate($projeto['nome_projeto'], 35)],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'nome_projeto'    => trim($_POST['nome_projeto'] ?? ''),
        'num_proposta'    => trim($_POST['num_proposta'] ?? ''),
        'termo_fomento'   => trim($_POST['termo_fomento'] ?? ''),
        'valor'           => trim($_POST['valor'] ?? ''),
        'data_assinatura' => !empty($_POST['data_assinatura']) ? $_POST['data_assinatura'] : null,
        'publicacao_dou'  => !empty($_POST['publicacao_dou']) ? $_POST['publicacao_dou'] : null,
        'inicio_vigencia' => !empty($_POST['inicio_vigencia']) ? $_POST['inicio_vigencia'] : null,
        'termino_vigencia'=> !empty($_POST['termino_vigencia']) ? $_POST['termino_vigencia'] : null,
        'prestacao_contas'=> !empty($_POST['prestacao_contas']) ? $_POST['prestacao_contas'] : null,
        'projeto_status'  => (int)($_POST['projeto_status'] ?? 0) ?: null,
        'objeto'          => trim($_POST['objeto'] ?? ''),
        'apresentacao'    => $_POST['apresentacao'] ?? '',
        'mostra_inicial'  => isset($_POST['mostra_inicial']) ? 1 : 0,
        'ultima_alteracao'=> auth_user()['id'],
        'data_ultima_alteracao' => date('Y-m-d'),
    ];

    if (empty($dados['nome_projeto'])) {
        $erros[] = 'O nome do projeto é obrigatório.';
    }

    if (empty($erros)) {
        db_update('tab_projetos', $dados, 'id = ?', [$id]);
        flash('success', 'Projeto atualizado com sucesso!');
        redirect(base_url('/admin/projetos/listar.php'));
    }
    // Repopular com os novos dados em caso de erro
    $projeto = array_merge($projeto, $dados);
}

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Planejamento &amp; Execução</div>
            <h2 class="page-title text-truncate">Editar: <?= e($projeto['nome_projeto']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="<?= base_url('/admin/projetos/documentos.php?projeto_id=' . $id) ?>" class="btn btn-outline-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Documentos Anexos
                </a>
                <a href="<?= base_url('/admin/projetos/funcoes.php?projeto_id=' . $id) ?>" class="btn btn-outline-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    Funções
                </a>
                <a href="<?= base_url('/admin/projetos/listar.php') ?>" class="btn btn-outline-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <h4 class="alert-title mb-1">Por favor, corrija os erros abaixo:</h4>
                <ul class="mb-0 ps-3">
                    <?php foreach ($erros as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<form method="POST" id="form-projeto">
    <?= csrf_field() ?>
    
    <div class="row row-cards">
        <!-- COLUNA PRINCIPAL: IDENTIFICAÇÃO E TEXTOS (8 Colunas) -->
        <div class="col-12 col-lg-8">
            <!-- Card: Identificação & Apresentação -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Identificação do Projeto</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome_projeto" class="form-label required">Nome Completo do Projeto</label>
                        <input type="text" id="nome_projeto" name="nome_projeto" class="form-control form-control-lg" required value="<?= e($projeto['nome_projeto']) ?>" placeholder="Nome oficial do projeto...">
                    </div>

                    <div class="mb-3">
                        <label for="objeto" class="form-label">Objeto Resumido do Projeto</label>
                        <textarea id="objeto" name="objeto" class="form-control" rows="3" placeholder="Descreva sucintamente o objetivo e o público-alvo beneficiado..."><?= e($projeto['objeto'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="apresentacao" class="form-label">Apresentação Detalhada &amp; Justificativa</label>
                        <textarea id="apresentacao" name="apresentacao" class="form-control tinymce-editor" rows="10"><?= $projeto['apresentacao'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Card: Prazos e Vigência -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Cronograma &amp; Prazos de Vigência</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label for="data_assinatura" class="form-label">Data de Assinatura</label>
                            <input type="date" id="data_assinatura" name="data_assinatura" class="form-control" value="<?= e($projeto['data_assinatura'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="publicacao_dou" class="form-label">Publicação no DOU</label>
                            <input type="date" id="publicacao_dou" name="publicacao_dou" class="form-control" value="<?= e($projeto['publicacao_dou'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="inicio_vigencia" class="form-label">Início da Vigência</label>
                            <input type="date" id="inicio_vigencia" name="inicio_vigencia" class="form-control" value="<?= e($projeto['inicio_vigencia'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="termino_vigencia" class="form-label">Término da Vigência</label>
                            <input type="date" id="termino_vigencia" name="termino_vigencia" class="form-control" value="<?= e($projeto['termino_vigencia'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label for="prestacao_contas" class="form-label">Prazo Final para Prestação de Contas</label>
                            <input type="date" id="prestacao_contas" name="prestacao_contas" class="form-control" value="<?= e($projeto['prestacao_contas'] ?? '') ?>">
                            <small class="form-hint">Data limite para o envio do relatório conclusivo da prestação de contas.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUNA LATERAL: DADOS JURÍDICOS, VALOR E AÇÕES (4 Colunas) -->
        <div class="col-12 col-lg-4">
            <!-- Card: Fomento & Financeiro -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Fomento &amp; Dados Legais</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="projeto_status" class="form-label">Status da Execução</label>
                        <select id="projeto_status" name="projeto_status" class="form-select">
                            <option value="">Selecione o status...</option>
                            <?php foreach ($status_list as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= (int)$projeto['projeto_status'] === (int)$s['id'] ? 'selected' : '' ?>>
                                    <?= e($s['projetos_status']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label">Orçamento Aprovado</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" id="valor" name="valor" class="form-control font-monospace" value="<?= e($projeto['valor'] ?? '') ?>" placeholder="500.000,00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="num_proposta" class="form-label">Número da Proposta / SICONV</label>
                        <input type="text" id="num_proposta" name="num_proposta" class="form-control font-monospace" value="<?= e($projeto['num_proposta'] ?? '') ?>" placeholder="Ex: 012345/2026">
                    </div>

                    <div class="mb-3">
                        <label for="termo_fomento" class="form-label">Termo de Fomento / Parceria</label>
                        <input type="text" id="termo_fomento" name="termo_fomento" class="form-control font-monospace" value="<?= e($projeto['termo_fomento'] ?? '') ?>" placeholder="Ex: TF nº 123/2026">
                    </div>
                </div>
            </div>

            <!-- Card: Visibilidade no Portal -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Visibilidade &amp; Destaque</h3>
                </div>
                <div class="card-body">
                    <label class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="mostra_inicial" value="1" <?= !empty($projeto['mostra_inicial']) ? 'checked' : '' ?>>
                        <span class="form-check-label fw-medium">Exibir na Página Inicial</span>
                    </label>
                    <small class="text-secondary d-block">Quando marcado, o projeto ganha destaque na área principal do portal público.</small>
                </div>
            </div>

            <!-- Card de Ações -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Salvar Alterações
                    </button>
                    <a href="<?= base_url('/admin/projetos/listar.php') ?>" class="btn btn-outline-secondary w-100">
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
