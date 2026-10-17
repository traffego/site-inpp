<?php
/**
 * Admin: Visualizar Currículo
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    flash('error', 'Currículo não especificado.');
    redirect(base_url('/admin/curriculos/listar.php'));
}

$curriculo = db_fetch(
    "SELECT c.*, f.funcao, p.nome_projeto, est.nome AS nome_estado, c.cidade AS nome_cidade
     FROM tab_curriculos c
     LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
     LEFT JOIN tab_projetos p ON c.id_projeto = p.id
     LEFT JOIN estado est ON c.estado = est.idUf
     WHERE c.id = ?",
    [$id]
);

if (!$curriculo) {
    flash('error', 'Currículo não encontrado.');
    redirect(base_url('/admin/curriculos/listar.php'));
}

$page_title = 'Admin — Detalhes do Currículo';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Currículos', 'url' => base_url('/admin/curriculos/listar.php')],
    ['label' => 'Detalhes: ' . truncate($curriculo['nome'], 25)],
];

$nome_partes = explode(' ', trim($curriculo['nome'] ?? ''));
$iniciais = mb_strtoupper(mb_substr($nome_partes[0] ?? 'C', 0, 1) . mb_substr($nome_partes[1] ?? '', 0, 1));
$arq = !empty($curriculo['arquivo_curriculo']) ? $curriculo['arquivo_curriculo'] : ($curriculo['id'] . '.pdf');

ob_start();
?>
<!-- Header da Página -->
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Banco de Talentos &amp; Recursos Humanos</div>
            <h2 class="page-title text-truncate"><?= e($curriculo['nome']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <?php if (!empty($arq)): ?>
                <a href="/uploads/curriculos/<?= e($arq) ?>" target="_blank" class="btn btn-outline-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Baixar Currículo PDF
                </a>
                <?php endif; ?>
                <a href="<?= base_url('/admin/curriculos/listar.php') ?>" class="btn btn-outline-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Voltar à Listagem
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Card Destaque: Perfil do Candidato -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="avatar avatar-xl rounded-circle bg-primary-lt fw-bold fs-2 text-primary">
                    <?= e($iniciais) ?>
                </span>
            </div>
            <div class="col">
                <h3 class="card-title mb-1 fs-2"><?= e($curriculo['nome']) ?></h3>
                <div class="text-secondary d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-primary-lt fs-4">
                        <?= e($curriculo['funcao'] ?? 'Candidato Geral') ?>
                    </span>
                    <span>•</span>
                    <span>Projeto de Interesse: <strong><?= e($curriculo['nome_projeto'] ?: 'Banco Geral') ?></strong></span>
                    <span>•</span>
                    <span>Cadastro em: <strong><?= !empty($curriculo['data_cadastro']) ? format_date($curriculo['data_cadastro']) : '—' ?></strong></span>
                </div>
            </div>
            <div class="col-12 col-md-auto mt-3 mt-md-0">
                <div class="btn-list">
                    <?php if (!empty($curriculo['e_mail'])): ?>
                    <a href="mailto:<?= e($curriculo['e_mail']) ?>" class="btn btn-sm btn-outline-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        Enviar E-mail
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($curriculo['telefone_1'])): ?>
                    <a href="tel:<?= preg_replace('/\D/', '', $curriculo['telefone_1']) ?>" class="btn btn-sm btn-outline-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        Ligar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <!-- Coluna 1: Dados Pessoais & Contato -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Dados Pessoais &amp; Endereço</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <tbody>
                        <tr>
                            <td class="text-secondary" style="width: 35%;">E-mail</td>
                            <td><strong><?= e($curriculo['e_mail'] ?: 'Não informado') ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Telefone Principal</td>
                            <td><?= e($curriculo['telefone_1'] ?: 'Não informado') ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Telefone Recado</td>
                            <td><?= e($curriculo['telefone_2'] ?: 'Não informado') ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Sexo</td>
                            <td><?= e($curriculo['sexo'] ?: 'Não informado') ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Data Nascimento</td>
                            <td><?= !empty($curriculo['data_nascimento']) ? format_date($curriculo['data_nascimento']) : 'Não informado' ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Estado Civil</td>
                            <td><?= e($curriculo['estado_civil'] ?: 'Não informado') ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Endereço Completo</td>
                            <td>
                                <div><?= e($curriculo['endereco'] ?: 'Endereço não informado') ?></div>
                                <?php if (!empty($curriculo['bairro']) || !empty($curriculo['cep'])): ?>
                                    <div class="text-secondary small"><?= e($curriculo['bairro'] ?: '') ?> <?= !empty($curriculo['cep']) ? '• CEP: ' . e($curriculo['cep']) : '' ?></div>
                                <?php endif; ?>
                                <div class="text-secondary small"><?= e($curriculo['nome_cidade'] ?: ($curriculo['cidade'] ?: '')) ?> / <?= e($curriculo['estado'] ?: '') ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Coluna 2: Formação & Requisitos -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Formação &amp; Competências</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <tbody>
                        <tr>
                            <td class="text-secondary" style="width: 38%;">Escolaridade</td>
                            <td><strong><?= e($curriculo['escolaridade'] ?: 'Não informada') ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Outro Idioma</td>
                            <td>
                                <?php if ($curriculo['idioma_sim_nao']): ?>
                                    <span class="badge bg-success-lt me-1">Sim</span>
                                    <?= e($curriculo['idioma_quais'] ?? '') ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary-lt">Não</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Conhecimentos de Informática</td>
                            <td>
                                <?php if ($curriculo['informatica_sim_nao']): ?>
                                    <span class="badge bg-success-lt">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-lt">Não</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Projeto Vinculado</td>
                            <td><span class="fw-medium"><?= e($curriculo['nome_projeto'] ?: 'Geral / Sem projeto específico') ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Função / Cargo</td>
                            <td><span class="badge bg-blue-lt"><?= e($curriculo['funcao'] ?? 'Geral') ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Arquivo Anexo</td>
                            <td>
                                <?php if (!empty($arq)): ?>
                                    <a href="/uploads/curriculos/<?= e($arq) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        Abrir Documento PDF
                                    </a>
                                <?php else: ?>
                                    <span class="text-secondary small">Nenhum anexo disponível</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Seção: Experiência Profissional -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Histórico e Experiências Profissionais</h3>
            </div>
            <div class="card-body">
                <?php if ($curriculo['experiencia_sim_nao']): ?>
                    <div class="row row-cards mb-3">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <?php if (!empty($curriculo["empresa_{$i}"])): ?>
                                <div class="col-12 col-md-4">
                                    <div class="card card-sm bg-body-tertiary">
                                        <div class="card-body">
                                            <div class="text-secondary small text-uppercase fw-bold mb-1">Empresa <?= $i ?></div>
                                            <div class="fw-bold fs-3 text-primary mb-2"><?= e($curriculo["empresa_{$i}"]) ?></div>
                                            <div class="mb-1"><span class="text-secondary">Cargo:</span> <strong><?= e($curriculo["empresa_{$i}_funcao"] ?: '—') ?></strong></div>
                                            <div><span class="text-secondary">Período:</span> <?= e($curriculo["empresa_{$i}_periodo"] ?: '—') ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <p class="text-secondary mb-3">O candidato declarou não possuir experiências formais anteriores.</p>
                <?php endif; ?>

                <?php if (!empty($curriculo['cursos_relevantes'])): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Cursos &amp; Certificações Relevantes</label>
                        <div class="p-3 border rounded bg-body-tertiary text-reset font-monospace small" style="white-space: pre-wrap;"><?= e($curriculo['cursos_relevantes']) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($curriculo['experiencia_profissional'])): ?>
                    <div>
                        <label class="form-label fw-bold text-secondary">Resumo da Trajetória Profissional</label>
                        <div class="p-3 border rounded bg-body-tertiary text-reset small" style="white-space: pre-wrap; line-height: 1.6;"><?= e($curriculo['experiencia_profissional']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
